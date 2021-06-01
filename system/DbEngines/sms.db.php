<?php 

error_reporting(E_ALL ^ E_DEPRECATED);

class SmsDB {
	
	private $connection;
	
	private $parent;
	
	public function __construct($parent = NULL) {
		$this->parent = $parent;
	}
	
	private function open_db() {
		$this->connection = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die(mysql_error());
		mysql_set_charset('utf8',$this->connection);
		mysql_select_db(DB_NAME, $this->connection) or die(mysql_error());
	}
	
	private function close_db() {
		//mysql_close($this->connection);
	}
	
	public function new_mandate_sms($mandate_id = NULL) {
		
		$mandate = $this->parent->get_component('mandates')->load_mandate($mandate_id);

		$tracking = $this->parent->get_component('mandate_tracking')->get_track($mandate_id);

		$phone_number = $mandate['CustomerPhone'];
		
		$phone_number = str_replace(" ","",$phone_number);
		
		$phone_number = str_replace("-","",$phone_number);

		$text = "Tisztelt ".$mandate['CustomerName']."! Megbízását köszönjük! Nyomkövetés itt: ".ROOT_URL."/nyomkovetes. Azonosító: ".$tracking['Uname']." Jelszó: ".$tracking['Passw'].". Homedt Masterservice";
		
		$this->open_db();
		
		$sql = "INSERT INTO sms (phone_number, text, order_id) VALUES ('".$phone_number."', '".$text."', ".$mandate['ID'].")";
		
		$ret =  mysql_query($sql,$this->connection) or die (mysql_error());
		
		$this->close_db();
		
		return $ret;
		
	}
	
}
?>