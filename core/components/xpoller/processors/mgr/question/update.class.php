<?php
/**
 * Update an Item
 */
class xPollerQuestionUpdateProcessor extends modObjectUpdateProcessor {
	public $objectType = 'xpQuestion';
	public $classKey = 'xpQuestion';
	public $languageTopics = array('xpoller');
	public $permission = 'edit_document';

		public function beforeSet() {
			

		    // ================================================================================= //

	        $culturekeys_list = $this->modx->getOption('xpoller_culturekeys_list',null,$this->modx->getOption('core_path').'components/xpoller/');
			$culturekeys_array = explode(',', $culturekeys_list);

			$question_id = $this->getProperty('id'); // Получаем id вновь созданного вопроса
			$question_text = $this->getProperty('text');

			foreach ($culturekeys_array as $key => $value) {
				$arrayOfProperties = array(
				'data'			=> '{
										"name":"question_'.$question_id.'",
										"value":"'.$question_text.'",
										"namespace":"xpoller",
										"topic":"translations",
										"language":"'.$value.'",
										"editedon":'.time().',
										"overridden":1,
										"menu":null
									}',
	        	);
	        $response = $this->modx->runProcessor('workspace/lexicon/updatefromgrid',$arrayOfProperties);

				if ($response->isError()) {
				    return $this->modx->lexicon('xpoller_translate_save_error');
				}
			}
			$culturekeys_count = count($context_array);

			// ================================================================================= //

	    return !$this->hasErrors();
	    }
}



return 'xPollerQuestionUpdateProcessor';
