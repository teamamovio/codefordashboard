<?php

require_once dirname(__FILE__)."/CityForm.php";

class EditCityForm extends CityForm {
	public function __construct($formName, $states, $cityRow) {
		$this->cityRow = $cityRow;
		
		$this->conf['fields']['name']['def'] = $this->cityRow['name'];
		$this->conf['fields']['state_id']['def'] = $this->cityRow['state_id'];
		
		parent::__construct($formName, $states);
	}
	
	public function dbSave() {
		global $db, $www_base;
		
		$q = @ mysql_query(
			"UPDATE cities SET ".
				"name = '".mysql_real_escape_string($this->f['name'], $db)."',".
				"state_id = '".mysql_real_escape_string($this->f['state_id'], $db)."' ".
			"WHERE id = '".mysql_real_escape_string($this->cityRow['id'], $db)."'",
			$db
		);
		
		if ($q === false) {
			$this->addError(
				'name',
				"City with specified name already exists in the state of ".$this->states[$this->f['state_id']]
			);
		} else {
			$ret = (isset($_GET['ret']) && is_string($_GET['ret'])) ? $_GET['ret'] : "{$www_base}/admin/cities/";
			header("Location: {$ret}");
			exit;
		}
	}
}