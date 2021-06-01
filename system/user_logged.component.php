<?php

require_once('DbEngines/user_logged.db.php');

class user_logged {
	
	private $user_logged_db;
	
	private $parent;
	
	public function __construct($parent = NULL) {
		
		$this->parent = $parent;
		
		$this->user_logged_db = new User_loggedDB($this->parent);
		
	}
	
	public function add_log($uid = null, $master = false) {
		
		return $this->user_logged_db->add_log($uid,$master);
		
	}
	
	public function add_check() {
	
		$res = $this->user_logged_db->add_check();
		
		if($res == false){
			$ret['status'] = 'error';
		} else {
			$ret['status'] = 'ok';
		}
		
		return $ret;
		
	}
	
	public function close_logout($uid = null,$session = false, $master = false){
		return $this->user_logged_db->close_logout($uid,$session,$master);
	}
	
	public function set_logout(){
		return $this->user_logged_db->set_logout();
	}
	
	public function check_logged($uid = null, $master = false) {
		
		if($master == false) {
			
			//$uid = $this->parent->get_component('user')->login($username,$password);
			
			$filter = array(
							"Uid = " . $uid,
							"Logout IS NULL",
							);
		} else {
			
			//$uid = $this->parent->get_component('master')->login($username,$password);
			
			$filter = array(
							"MasterUid = " . $uid,
							"Logout IS NULL",
							);
			
		}
		
		$res = $this->user_logged_db->get_logs($filter);
		
		$ret = array();
		
		while($row = mysql_fetch_assoc($res)) {
			$ret[] = $row;
		}
		
		//var_dump($ret);
		
		return $ret;
		
	}
}

?>
