<?php
/**
 * Update an Item
 */
class xPoller2OptionUpdateProcessor extends modObjectUpdateProcessor {
	public $objectType = 'xpOption';
	public $classKey = 'xpOption';
	public $languageTopics = array('xpoller2');
	public $permission = 'edit_document';
    
    public function beforeSet() {
        $right = $this->getProperty('right');
		$this->setProperty('right', !empty($right) && $right != 'false');
		$alreadyExists = $this->modx->getObject($this->classKey, array(
            'id:!=' => $this->getProperty('id'),
			'option' => $this->getProperty('option'),
            'qid' => $this->getProperty('qid')
		));
		if ($alreadyExists) {
			$this->modx->error->addField('option', $this->modx->lexicon('xpoller2_option_err_ae'));
		} elseif ($this->getProperty('right') && $this->modx->getObject($this->classKey, array(
             'id:!=' => $this->getProperty('id'),
             'right' => 1,
             'qid' => $this->getProperty('qid')))) {
            $this->modx->error->addField('right', $this->modx->lexicon('xpoller2_option_err_ae_right'));
		}

		// ================================================================================= //

	        $culturekeys_list = $this->modx->getOption('xpoller2_culturekeys_list',null,$this->modx->getOption('core_path').'components/xpoller2/');
			$culturekeys_array = explode(',', $culturekeys_list);

			

			$option_id = $this->getProperty('id');
			$option_text = $this->getProperty('option');
			$qid = $this->modx->getObject($this->classKey, array(
				'id' => $this->getProperty('id'),
			));
			
	       	$option_translations = $qid->get('qid') . '_' . $option_id;

			foreach ($culturekeys_array as $key => $value) {
				$arrayOfProperties = array(
				'data'			=> '{
										"name":"option_'.$option_translations.'",
										"value":"'.$option_text.'",
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

        if ($this->hasErrors()) {
            return false;
        }
		return !$this->hasErrors();
	}

}

return 'xPoller2OptionUpdateProcessor';
