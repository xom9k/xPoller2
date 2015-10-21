<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';
$corePath = $modx->getOption('xpoller_core_path', null, $modx->getOption('core_path') . 'components/xpoller2/');
require_once $corePath . 'model/xpoller2/xpoller2.class.php';
$modx->xpoller = new xPoller2($modx);
$modx->lexicon->load('xpoller2:default');
/* handle request */
$path = $modx->getOption('processorsPath', $modx->xpoller->config, $corePath . 'processors/');
$modx->request->handleRequest(array(
	'processors_path' => $path,
	'location' => '',
));