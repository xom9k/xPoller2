<?php

/**
 * Class xPoller2MainController
 */
abstract class xPoller2MainController extends modExtraManagerController {
	/** @var xPoller2 $xPoller2 */
	public $xPoller2;


	/**
	 * @return void
	 */
	public function initialize() {
		$corePath = $this->modx->getOption('xpoller2_core_path', null, $this->modx->getOption('core_path') . 'components/xpoller2/');
		require_once $corePath . 'model/xpoller2/xpoller2.class.php';

		$this->xPoller2 = new xPoller2($this->modx);

		$this->addCss($this->xPoller2->config['cssUrl'] . 'mgr/main.css');
		$this->addJavascript($this->xPoller2->config['jsUrl'] . 'mgr/xpoller2.js');
		$this->addHtml('<script type="text/javascript">
		Ext.onReady(function() {
			xPoller2.config = ' . $this->modx->toJSON($this->xPoller2->config) . ';
			xPoller2.config.connector_url = "' . $this->xPoller2->config['connectorUrl'] . '";
		});
		</script>');

		parent::initialize();
	}


	/**
	 * @return array
	 */
	public function getLanguageTopics() {
		return array('xpoller2:default');
	}


	/**
	 * @return bool
	 */
	public function checkPermissions() {
		return true;
	}
}


/**
 * Class IndexManagerController
 */
class IndexManagerController extends xPoller2MainController {

	/**
	 * @return string
	 */
	public static function getDefaultController() {
		return 'home';
	}
}