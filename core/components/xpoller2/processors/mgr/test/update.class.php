<?php
/**
 * Update an Item
 */
class xPoller2TestUpdateProcessor extends modObjectUpdateProcessor {
	public $objectType = 'xpTest';
	public $classKey = 'xpTest';
	public $languageTopics = array('xpoller2');
	public $permission = 'edit_document';
}

return 'xPoller2TestUpdateProcessor';