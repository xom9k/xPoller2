<?php
/**
 * Get an Item
 */
class xPoller2QuestionGetProcessor extends modObjectGetProcessor {
	public $objectType = 'xpQuestion';
	public $classKey = 'xpQuestion';
	public $languageTopics = array('xpoller2:default');
}

return 'xPoller2QuestionGetProcessor';