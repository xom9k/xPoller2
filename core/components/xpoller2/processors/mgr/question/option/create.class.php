<?php
/**
 * Create an Item
 */
class xPoller2OptionCreateProcessor extends modObjectCreateProcessor {
	public $objectType = 'xpOption';
	public $classKey = 'xpOption';
	public $languageTopics = array('xpoller2');
	public $permission = 'new_document';


	/**
	 * @return bool
	 */
	public function beforeSet() {
        $right = $this->getProperty('right');
		$this->setProperty('right', !empty($right) && $right != 'false');
		$alreadyExists = $this->modx->getObject($this->classKey, array(
			'option' => $this->getProperty('option'),
            'qid' => $this->getProperty('qid')
		));
		if ($alreadyExists) {
			$this->modx->error->addField('option', $this->modx->lexicon('xpoller2_option_err_ae'));
		} elseif ($this->getProperty('right') && $this->modx->getObject($this->classKey, array('right' => $this->getProperty('right'), 'qid' => $this->getProperty('qid')))) {
            $this->modx->error->addField('right', $this->modx->lexicon('xpoller2_option_err_ae_right'));
		}

    	$this->setProperty('right', !empty($right) && $right != 'false');

		return !$this->hasErrors();
	}

	public function afterSave() {

    	// ================================================================================= //
    	$c = $this->modx->newQuery($this->classKey);
        $c->sortby('rank','DESC');
        $c->limit(1);
        $c->prepare();
        $c->stmt->execute();
        $lastQuests = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
        $lastQuest = array_shift($lastQuests);
        $rank = $lastQuest[$this->classKey.'_rank'] + 1;
        $this->setProperty('rank', $rank);

    	$culturekeys_list = $this->modx->getOption('xpoller2_culturekeys_list',null,$this->modx->getOption('core_path').'components/xpoller2/');
		$culturekeys_array = explode(',', $culturekeys_list);

		$option_id = $this->object->get('id');
		$option_text = $this->getProperty('option');
        $question_id = $this->getProperty('qid');

		foreach ($culturekeys_array as $key => $value) {
			$arrayOfProperties = array(
	        'name' 			=> 'option_'.$question_id.'_'.$option_id,
	        'namespace'		=> 'xpoller2',
	        'language' 		=> $value,
	        'topic' 		=> 'translations',
	        'value' 		=> $option_text);
	        $response = $this->modx->runProcessor('workspace/lexicon/create',$arrayOfProperties);
			if ($response->isError()) {
			    return $this->modx->lexicon('xpoller2_translate_save_error');
			}
		}

		// ================================================================================= //
		return !$this->hasErrors();
		
	}
}

return 'xPoller2OptionCreateProcessor';