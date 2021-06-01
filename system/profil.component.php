<?php

class profil {
	
	private $parent;
	
	private $rules = array(

							);
	
	public function __construct($parent = NULL){
		
		$this->parent = $parent;
	}
	
	public function check_rule() {
		
		//$user = $this->parent->get_component('user')->load_user($_SESSION['HDT_uid']);
		//return in_array($user['UserType'],$this->rules);
		
		return true;
		
	}
	
}