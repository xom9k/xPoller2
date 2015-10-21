<?php
/**
 * Create an Item
 */
class xPoller2TestCreateProcessor extends modObjectCreateProcessor {
    public $objectType = 'xpTest';
	public $classKey = 'xpTest';
	public $languageTopics = array('xpoller2');
	public $permission = 'new_document';


	/**
	 * @return bool
	 */
	public function beforeSet() {
		$alreadyExists = $this->modx->getObject($this->classKey, array(
			'name' => $this->getProperty('name')
		));
		if ($alreadyExists) {
			$this->modx->error->addField('name', $this->modx->lexicon('xpoller2_test_err_ae'));
		}
		return !$this->hasErrors();
	}

}

return 'xPoller2TestCreateProcessor';