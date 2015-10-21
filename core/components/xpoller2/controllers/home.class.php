<?php
/**
 * The home manager controller for xPoller2.
 *
 */
class xPoller2HomeManagerController extends xPoller2MainController {
	/* @var xPoller2 $xPoller2 */
	public $xPoller2;


	/**
	 * @param array $scriptProperties
	 */
	public function process(array $scriptProperties = array()) {
	}


	/**
	 * @return null|string
	 */
	public function getPageTitle() {
		return $this->modx->lexicon('xpoller2');
	}


	/**
	 * @return void
	 */
	public function loadCustomCssJs() {
		$this->addJavascript($this->xPoller2->config['jsUrl'] . 'mgr/widgets/tests.grid.js');
		$this->addJavascript($this->xPoller2->config['jsUrl'] . 'mgr/widgets/lexicon.grid.js');
		$this->addJavascript($this->xPoller2->config['jsUrl'] . 'mgr/widgets/questions.grid.js');
        $this->addJavascript($this->xPoller2->config['jsUrl'] . 'mgr/widgets/options.grid.js');
		$this->addJavascript($this->xPoller2->config['jsUrl'] . 'mgr/widgets/home.panel.js');
		$this->addJavascript($this->xPoller2->config['jsUrl'] . 'mgr/sections/home.js');
		$this->addHtml('<script type="text/javascript">
		Ext.onReady(function() {
			MODx.load({ xtype: "xpoller2-page-home"});
		});
		</script>');
	}


	/**
	 * @return string
	 */
	public function getTemplateFile() {
		return $this->xPoller2->config['templatesPath'] . 'home.tpl';
	}
}