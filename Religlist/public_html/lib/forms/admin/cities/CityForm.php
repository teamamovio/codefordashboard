<?php

require_once dirname(__FILE__)."/../../Form.php";

class CityForm extends Form {
	public $conf = array(
		'fields' => array(
			'name'			=> array(
				'human' => "City Name",
				'required' => true,
				'maxLen' => 255,
				'html_extra' => 'size="50"',
			),
			'state_id'	=> array(
				'human' => "State",
				'required' => true,
				'html' => 'select',
			),
		),
	);
	
	public function __construct($formName, $states) {
		$this->states = $states;
		$this->conf['fields']['state_id']['inArray'] = $this->states;
		
		parent::__construct($formName);
	}
}