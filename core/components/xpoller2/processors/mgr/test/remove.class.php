<?php
/**
 * Remove an Item
 */
class xPoller2TestRemoveProcessor extends modObjectRemoveProcessor {
	public $checkRemovePermission = true;
	public $objectType = 'xpTest';
	public $classKey = 'xpTest';
	public $languageTopics = array('xpoller2');

}

return 'xPoller2TestRemoveProcessor';