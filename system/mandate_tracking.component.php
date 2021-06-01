<?php
require_once('DbEngines/mandate_tracking.db.php');

class mandate_tracking {
	
	private $parent;
	
	private $mandate_tracking_db;
	
	public function __construct($parent = NULL) {
		
		$this->parent = $parent;
		
		$this->mandate_tracking_db = new Mandate_trackingDB($parent);
		
	}
	
	public function add_track($mandate_id = NULL) {
		
		$this->mandate_tracking_db->add_track($mandate_id);
		
	}
	
	public function get_track($mandate_id = NULL) {
		
		return $this->mandate_tracking_db->get_track($mandate_id);
		
	}
	
	public function login_track($uname = NULL, $passw = NULL) {
		
		return $this->mandate_tracking_db->login_track($uname,$passw);
		
	}
	
}
?>