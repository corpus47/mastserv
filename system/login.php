<?php

class login {
	
	private $params;
	
	public function __construct($params = NULL) {
		if($params != NULL) {
			$this->params = $params;
		}
	}
	
	public function view() {
		echo 'login';
	}
	
}
