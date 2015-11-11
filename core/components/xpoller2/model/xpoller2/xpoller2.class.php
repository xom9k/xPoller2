<?php
/**
 * The base class for xPoller2.
 */

class xPoller2 {
	/* @var modX $modx */
	public $modx;
	/* @var xPoller2ControllerRequest $request */
	protected $request;
	public $initialized = array();
	public $chunks = array();


	/**
	 * @param modX $modx
	 * @param array $config
	 */
	function __construct(modX &$modx, array $config = array()) {
		$this->modx =& $modx;

		$corePath = $this->modx->getOption('xpoller2_core_path', $config, $this->modx->getOption('core_path') . 'components/xpoller2/');
		$assetsUrl = $this->modx->getOption('xpoller2_assets_url', $config, $this->modx->getOption('assets_url') . 'components/xpoller2/');
		$connectorUrl = $assetsUrl . 'connector.php';

		$this->config = array_merge(array(
			'assetsUrl' => $assetsUrl,
			'cssUrl' => $assetsUrl . 'css/',
			'jsUrl' => $assetsUrl . 'js/',
			'imagesUrl' => $assetsUrl . 'images/',
			'connectorUrl' => $connectorUrl,
			'actionUrl' => $assetsUrl . 'action.php',
			'corePath' => $corePath,
			'modelPath' => $corePath . 'model/',
			'chunksPath' => $corePath . 'elements/chunks/',
			'templatesPath' => $corePath . 'elements/templates/',
			'chunkSuffix' => '.chunk.tpl',
			'snippetsPath' => $corePath . 'elements/snippets/',
			'processorsPath' => $corePath . 'processors/'
		), $config);

		$this->modx->addPackage('xpoller2', $this->config['modelPath']);
		$this->modx->lexicon->load('xpoller2:default');

		if (!defined('MODX_API_MODE') || !MODX_API_MODE) {
			$this->modx->regClientScript($this->config['jsUrl'].'web/default.js');
			$this->modx->regClientCSS($this->config['cssUrl'].'web/default.css');
			$this->modx->regClientStartupScript(preg_replace('#(\n|\t)#', '', '
				<script type="text/javascript">
				xPoller2Config = {
					cssUrl: "' . $this->config['cssUrl'] . '",
					jsUrl: "' . $this->config['jsUrl'] . '",
					actionUrl: "' . $this->config['actionUrl'] . '"
				};
				</script>'), true);
			$this->modx->regClientStartupScript(preg_replace('#(\n|\t)#', '', '
			<script type="text/javascript">
			if (typeof jQuery == "undefined") {
				document.write("<script src=\"' . $this->config['jsUrl'] . 'web/lib/jquery.min.js\" type=\"text/javascript\"><\/script>");
			}
			</script>
			'), true);
		}
	}


	/**
	 * Initializes xPoller2 into different contexts.
	 *
	 * @access public
	 *
	 * @param string $ctx The context to load. Defaults to web.
	 */
	public function initialize($ctx = 'web') {
		switch ($ctx) {
			case 'mgr':
				if (!$this->modx->loadClass('xpoller2.request.xPoller2ControllerRequest', $this->config['modelPath'], true, true)) {
					return 'Could not load controller request handler.';
				}
				$this->request = new xPoller2ControllerRequest($this);

				return $this->request->handleRequest();
				break;
			case 'web':
				break;
			default:
				/* if you wanted to do any generic frontend stuff here.
				 * For example, if you have a lot of snippets but common code
				 * in them all at the beginning, you could put it here and just
				 * call $xpoller2->initialize($modx->context->get('key'));
				 * which would run this.
				 */
				break;
		}
		return true;
	}


	/**
	 * Gets a Chunk and caches it; also falls back to file-based templates
	 * for easier debugging.
	 *
	 * @access public
	 *
	 * @param string $name The name of the Chunk
	 * @param array $properties The properties for the Chunk
	 *
	 * @return string The processed content of the Chunk
	 */
	public function getChunk($name, array $properties = array()) {
		$chunk = null;
		if (!isset($this->chunks[$name])) {
			$chunk = $this->modx->getObject('modChunk', array('name' => $name), true);
			if (empty($chunk)) {
				$chunk = $this->_getTplChunk($name, $this->config['chunkSuffix']);
				if ($chunk == false) {
					return false;
				}
			}
			$this->chunks[$name] = $chunk->getContent();
		}
		else {
			$o = $this->chunks[$name];
			$chunk = $this->modx->newObject('modChunk');
			$chunk->setContent($o);
		}
		$chunk->setCacheable(false);

		return $chunk->process($properties);
	}


	/**
	 * Returns a modChunk object from a template file.
	 *
	 * @access private
	 *
	 * @param string $name The name of the Chunk. Will parse to name.chunk.tpl by default.
	 * @param string $suffix The suffix to add to the chunk filename.
	 *
	 * @return modChunk/boolean Returns the modChunk object if found, otherwise
	 * false.
	 */
	private function _getTplChunk($name, $suffix = '.chunk.tpl') {
		$chunk = false;
		$f = $this->config['chunksPath'] . strtolower($name) . $suffix;
		if (file_exists($f)) {
			$o = file_get_contents($f);
			$chunk = $this->modx->newObject('modChunk');
			$chunk->set('name', $name);
			$chunk->setContent($o);
		}

		return $chunk;
	}
	
	public function setxPoller2Cookie($qid) {
		if (!$this->modx->user->isAuthenticated($this->modx->context->key)) {
		    $xpVotedString = "";
			if(isset($_COOKIE['xpVoted'])) $xpVotedString = $_COOKIE['xpVoted'];
		    $xpVotedString .= $qid . ',';
		    setCookie('xpVoted', $xpVotedString, time()+360000000, '/');
		}
	    return true;
	}
	public function saveSessionProperties($scriptProperties = array()) {
		// session_destroy('xPoller2')
		$_SESSION['xPoller2'] = $scriptProperties;
		return true;
	}
}