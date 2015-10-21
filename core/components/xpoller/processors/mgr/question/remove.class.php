<?php
/**
 * Remove an Item
 */
class xPollerQuestionRemoveProcessor extends modObjectRemoveProcessor {
	public $checkRemovePermission = true;
	public $objectType = 'xpQuestion';
	public $classKey = 'xpQuestion';
	public $languageTopics = array('xpoller');

	public function beforeRemove() {
		// ================================================================================= //

    	$culturekeys_list = $this->modx->getOption('xpoller_culturekeys_list',null,$this->modx->getOption('core_path').'components/xpoller/');
		$culturekeys_array = explode(',', $culturekeys_list);

		$qid = $this->object->get('id');

		foreach ($culturekeys_array as $key => $value) {
			$arrayOfProperties = array(
	        'name' 			=> 'question_'. $qid,
	        'namespace'		=> 'xpoller',
	        'language' 		=> $value,
	        'topic' 		=> 'translations');
	        $response = $this->modx->runProcessor('workspace/lexicon/revert',$arrayOfProperties);
			if ($response->isError()) {
			    return $this->modx->lexicon('xpoller_translate_save_error');
			}

			$options = $this->modx->getCollection('xpOption',array('qid' => $qid));
			foreach ($options as $res) {
				$arrayOfProperties_options = array(
		        'name' 			=> 'option_'. $qid . '_' . $res->get('id'),
		        'namespace'		=> 'xpoller',
		        'language' 		=> $value,
		        'topic' 		=> 'translations');
		        $response_options = $this->modx->runProcessor('workspace/lexicon/revert',$arrayOfProperties_options);
				if ($response_options->isError()) {
				    return $this->modx->lexicon('xpoller_translate_save_error');
				}
			}
		}

		// ================================================================================= //

		if ($this->hasErrors()) {
            return false;
        }
		return !$this->hasErrors();
	}
}

return 'xPollerQuestionRemoveProcessor';