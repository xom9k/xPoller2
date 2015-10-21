<?php
/**
 * Create an Item
 */
class xPoller2QuestionCreateProcessor extends modObjectCreateProcessor {
	public $objectType = 'xpQuestion';
	public $classKey = 'xpQuestion';
	public $languageTopics = array('xpoller2');
	public $permission = 'new_document';


	/**
	 * @return bool
	 */
	public function beforeSet() {
		

        $tid = $this->getProperty('tid') ? $this->getProperty('tid') : 0;
		$alreadyExists = $this->modx->getObject($this->classKey, array(
			'text' => $this->getProperty('text'),
            'tid' => $tid
		));
		if ($alreadyExists) {
			$this->modx->error->addField('text', $this->modx->lexicon('xpoller2_question_err_ae'));
		}
        $c = $this->modx->newQuery($this->classKey, array('tid' => $tid));
        $c->sortby('rank','DESC');
        $c->limit(1);
        $c->prepare();
        $c->stmt->execute();
        $lastQuests = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
        $lastQuest = array_shift($lastQuests);
        $rank = $lastQuest[$this->classKey.'_rank'] + 1;
        $this->setProperty('rank', $rank);

        

		return !$this->hasErrors();
		
	}

	public function afterSave() {
		// ================================================================================= //

        $culturekeys_list = $this->modx->getOption('xpoller2_culturekeys_list',null,$this->modx->getOption('core_path').'components/xpoller2/');
		$culturekeys_array = explode(',', $culturekeys_list);

		$question_id = $this->object->get('id'); // Получаем id вновь созданного вопроса
		$question_text = $this->getProperty('text');

		foreach ($culturekeys_array as $key => $value) {
			$arrayOfProperties = array(
	        'name' 			=> 'question_'.$question_id,
	        'namespace'		=> 'xpoller2',
	        'language' 		=> $value,
	        'topic' 		=> 'translations',
	        'value' 		=> $question_text);
	        $response = $this->modx->runProcessor('workspace/lexicon/create',$arrayOfProperties);
			if ($response->isError()) {
			    return $this->modx->lexicon('xpoller2_translate_save_error');
			}
		}
		$culturekeys_count = count($context_array);

		// ================================================================================= //
		return !$this->hasErrors();
	}
}

return 'xPoller2QuestionCreateProcessor';