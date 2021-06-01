<?php 

require_once('DbEngines/sms.db.php');

class Sms {
	
	private $sms_db;
	
	private $parent;
	
	public function __construct($parent = NULL) {
		
		$this->parent = $parent;
		
		$this->sms_db = new SmsDB($parent);
		
	}
	
	public function new_mandate_sms($mandate_id = NULL) {
		
		$this->sms_db->new_mandate_sms($mandate_id);
		
	}
	
}
?>