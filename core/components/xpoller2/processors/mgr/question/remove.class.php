<?php
/**
 * Remove an Item
 */
class xPoller2QuestionRemoveProcessor extends modObjectRemoveProcessor {
	public $checkRemovePermission = true;
	public $objectType = 'xpQuestion';
	public $classKey = 'xpQuestion';
	public $languageTopics = array('xpoller2');

	public function beforeRemove() {
		// ================================================================================= //

    	$culturekeys_list = $this->modx->getOption('xpoller2_culturekeys_list',null,$this->modx->getOption('core_path').'components/xpoller2/');
		$culturekeys_array = explode(',', $culturekeys_list);

		$qid = $this->object->get('id');

		foreach ($culturekeys_array as $key => $value) {
			$arrayOfProperties = array(
	        'name' 			=> 'question_'. $qid,
	        'namespace'		=> 'xpoller2',
	        'language' 		=> $value,
	        'topic' 		=> 'translations');
	        $response = $this->modx->runProcessor('workspace/lexicon/revert',$arrayOfProperties);
			if ($response->isError()) {
			    return $this->modx->lexicon('xpoller2_translate_save_error');
			}

			$options = $this->modx->getCollection('xpOption',array('qid' => $qid));
			foreach ($options as $res) {
				$arrayOfProperties_options = array(
		        'name' 			=> 'option_'. $qid . '_' . $res->get('id'),
		        'namespace'		=> 'xpoller2',
		        'language' 		=> $value,
		        'topic' 		=> 'translations');
		        $response_options = $this->modx->runProcessor('workspace/lexicon/revert',$arrayOfProperties_options);
				if ($response_options->isError()) {
				    return $this->modx->lexicon('xpoller2_translate_save_error');
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

return 'xPoller2QuestionRemoveProcessor';