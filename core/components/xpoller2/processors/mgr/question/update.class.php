<?php
/**
 * Update an Item
 */
class xPoller2QuestionUpdateProcessor extends modObjectUpdateProcessor {
	public $objectType = 'xpQuestion';
	public $classKey = 'xpQuestion';
	public $languageTopics = array('xpoller2');
	public $permission = 'edit_document';

		public function beforeSet() {
			

		    // ================================================================================= //

	        $culturekeys_list = $this->modx->getOption('xpoller2_culturekeys_list',null,$this->modx->getOption('core_path').'components/xpoller2/');
			$culturekeys_array = explode(',', $culturekeys_list);

			$question_id = $this->getProperty('id'); // Получаем id вновь созданного вопроса
			$question_text = $this->getProperty('text');

			foreach ($culturekeys_array as $key => $value) {
				$arrayOfProperties = array(
				'data'			=> '{
										"name":"question_'.$question_id.'",
										"value":"'.$question_text.'",
										"namespace":"xpoller2",
										"topic":"translations",
										"language":"'.$value.'",
										"editedon":'.time().',
										"overridden":1,
										"menu":null
									}',
	        	);
	        $response = $this->modx->runProcessor('workspace/lexicon/updatefromgrid',$arrayOfProperties);

				if ($response->isError()) {
				    return $this->modx->lexicon('xpoller2_translate_save_error');
				}
			}
			$culturekeys_count = count($context_array);

			// ================================================================================= //

	    return !$this->hasErrors();
	    }
}



return 'xPoller2QuestionUpdateProcessor';
