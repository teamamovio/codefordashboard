<?php

class Paypal {
	protected $_errors = array();
	
	protected $_credentials = array(
		'USER' => 'par12b12-hui_api1.gmail.com',
		'PWD' => '1396424780',
		'SIGNATURE' => 'An5ns1Kso7MWUdW4ErQKJJJ4qi4-AeA.aE1OOOuCM0irKwy65n.IY8W8',
	);
	
	protected $_endPoint = 'https://api-3t.sandbox.paypal.com/nvp';
	
	public $domain = "";
	
	protected $_version = '74.0';
	
	public function __construct($USER, $PWD, $SIGNATURE, $sandbox) {
		$this->_credentials['USER'] = $USER;
		$this->_credentials['PWD'] = $PWD;
		$this->_credentials['SIGNATURE'] = $SIGNATURE;
		
		if ($sandbox) {
			$this->_endPoint = "https://api-3t.sandbox.paypal.com/nvp";
			$this->domain = "www.sandbox.paypal.com";
		} else {
			$this->_endPoint = "https://api-3t.paypal.com/nvp";
			$this->domain = "www.paypal.com";
		}
	}
	
	public function request($method, $params = array()) {
		$this->_errors = array();
		if (empty($method)) {
			$this->_errors[] = 'API method is missing';
			return false;
		}
		
		$requestParams = array(
			'METHOD' => $method,
			'VERSION' => $this->_version,
		) + $this->_credentials;
		
		$request = http_build_query($requestParams + $params);
		
		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_URL => $this->_endPoint,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => $request
		));
		$response = curl_exec($ch);
		
		if (curl_errno($ch)) {
			$this->_errors[] = curl_error($ch);
			curl_close($ch);
			return false;
		} else {
			curl_close($ch);
			$responseArray = array();
			parse_str($response, $responseArray);
			return $responseArray;
		}
	}
}