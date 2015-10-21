<?php
/**
 * Get an Item
 */
class xPoller2OptionGetProcessor extends modObjectGetProcessor {
	public $objectType = 'xpOption';
	public $classKey = 'xpOption';
	public $languageTopics = array('xpoller2:default');
}

return 'xPoller2OptionGetProcessor';