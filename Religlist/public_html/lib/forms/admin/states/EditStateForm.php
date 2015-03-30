<?php

require_once dirname(__FILE__)."/StateForm.php";

class EditStateForm extends StateForm {
	public function __construct($formName, $stateRow) {
		$this->stateRow = $stateRow;
		
		$this->conf['fields']['name']['def'] = $this->stateRow['name'];
		
		parent::__construct($formName);
	}
	
	protected function dbSave() {
		global $db;
		
		$q = @ mysql_query(
			"UPDATE states SET ".
				"name = '".mysql_real_escape_string($this->f['name'], $db)."' ".
			"WHERE id = '".mysql_real_escape_string($this->stateRow['id'], $db)."'",
			$db
		);
		
		if ($q === false) {
			$this->addError('name', "State with specified name already exists");
		} else {
			header("Location: {$_SERVER['REQUEST_URI']}");
			exit;
		}
	}
}