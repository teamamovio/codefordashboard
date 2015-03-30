<?php

require_once dirname(__FILE__)."/StateForm.php";

class NewStateForm extends StateForm {
	protected function dbSave() {
		global $db;
		
		$q = @ mysql_query(
			"INSERT INTO states SET ".
				"name = '".mysql_real_escape_string($this->f['name'], $db)."'",
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