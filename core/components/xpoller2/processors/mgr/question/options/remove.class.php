<?php
/**
 * Remove an Items
 */
class xPoller2OptionsRemoveProcessor extends modProcessor {
    public $checkRemovePermission = true;
	public $objectType = 'xpOption';
	public $classKey = 'xpOption';
	public $languageTopics = array('xpoller2');

	public function process() {

        foreach (explode(',',$this->getProperty('items')) as $id) {
            $item = $this->modx->getObject($this->classKey, $id);
            $item->remove();
        }
        
        return $this->success();

	}

}

return 'xPoller2OptionsRemoveProcessor';