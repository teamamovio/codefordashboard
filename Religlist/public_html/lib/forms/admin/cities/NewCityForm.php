<?php

require_once dirname(__FILE__)."/CityForm.php";

class NewCityForm extends CityForm {
	protected function dbSave() {
		global $db;
		
		$q = @ mysql_query(
			"INSERT INTO cities SET ".
				"state_id = '".mysql_real_escape_string($this->f['state_id'], $db)."',".
				"name = '".mysql_real_escape_string($this->f['name'], $db)."'",
			$db
		);
		
		if ($q === false) {
			$this->addError(
				'name',
				"City with specified name already exists in the state of ".$this->states[$this->f['state_id']]
			);
		} else {
			header("Location: {$_SERVER['REQUEST_URI']}");
			exit;
		}
	}
}