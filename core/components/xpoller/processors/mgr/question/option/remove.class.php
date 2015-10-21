<?php
/**
 * Remove an Item
 */
class xPollerOptionRemoveProcessor extends modObjectRemoveProcessor {
	public $checkRemovePermission = true;
	public $objectType = 'xpOption';
	public $classKey = 'xpOption';
	public $languageTopics = array('xpoller');

	public function beforeRemove() {
		// ================================================================================= //

    	$culturekeys_list = $this->modx->getOption('xpoller_culturekeys_list',null,$this->modx->getOption('core_path').'components/xpoller/');
		$culturekeys_array = explode(',', $culturekeys_list);

		$option_id = $this->object->get('id');
        $qid = $this->modx->getObject($this->classKey, array( 'id' => $option_id ));

		foreach ($culturekeys_array as $key => $value) {
			$arrayOfProperties = array(
	        'name' 			=> 'option_'. $qid->get('qid') . '_' . $option_id,
	        'namespace'		=> 'xpoller',
	        'language' 		=> $value,
	        'topic' 		=> 'translations');
	        $response = $this->modx->runProcessor('workspace/lexicon/revert',$arrayOfProperties);
			if ($response->isError()) {
			    return $this->modx->lexicon('xpoller_translate_save_error');
			}
		}

		// ================================================================================= //

		if ($this->hasErrors()) {
            return false;
        }
		return !$this->hasErrors();
	}
}
	
return 'xPollerOptionRemoveProcessor';