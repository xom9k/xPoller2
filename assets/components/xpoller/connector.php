<?php
// For debug
ini_set('display_errors', 1);
ini_set('error_reporting', -1);
// Load MODX config
if (file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php')) {
	require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
}
else {
	require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
}
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';
$corePath = $modx->getOption('xpoller_core_path', null, $modx->getOption('core_path') . 'components/xpoller/');
require_once $corePath . 'model/xpoller/xpoller.class.php';
$modx->xpoller = new xpoller($modx);
$modx->lexicon->load('xpoller:default');
/* handle request */
$path = $modx->getOption('processorsPath', $modx->xpoller->config, $corePath . 'processors/');
$modx->request->handleRequest(array(
	'processors_path' => $path,
	'location' => '',
));