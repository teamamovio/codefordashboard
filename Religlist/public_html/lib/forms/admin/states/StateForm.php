<?php

require_once dirname(__FILE__)."/../../Form.php";

class StateForm extends Form {
	protected $conf = array(
		'fields' => array(
			'name'		=> array(
				'type' => 'string',
				'human' => "State Name",
				'required' => true,
				'maxLen' => 255,
				'html_extra' => 'size="50"',
			),
		),
	);
}