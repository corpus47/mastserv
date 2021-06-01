<?php 

error_reporting(E_ALL ^ E_DEPRECATED);

class Mandate_trackingDB {
	
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
	
	public function add_track($mandate_id = NULL) {
		
		$this->open_db();
		
		$uname = $this->generateRandomString(5);
		
		$passw = $this->generateRandomString(5);
		
		$sql = "INSERT INTO mandate_tracking (MandateID, Uname, Passw) VALUES (".$mandate_id.", '".$uname."', '".$passw."')";
		
		$ret =  mysql_query($sql,$this->connection) or die (mysql_error());
		
		$this->close_db();
		
		return $ret;
		
	}
	
	public function get_track($mandate_id = NULL) {
		
		$this->open_db();
		
		$sql = "SELECT * FROM mandate_tracking WHERE MandateID = " . $mandate_id;
		
		$res =  mysql_query($sql,$this->connection) or die (mysql_error());
		
		$ret = mysql_fetch_assoc($res);
		
		$this->close_db();
		
		return $ret;
		
	}
	
	public function login_track($uname,$passw){
		
		$this->open_db();
		
		$uname = mysql_real_escape_string($uname);
		$passw = mysql_real_escape_string($passw);
		
		$sql = "SELECT * FROM mandate_tracking WHERE Uname = '" . $uname . "' AND Passw = '" . $passw ."'";
		
		$res =  mysql_query($sql,$this->connection) or die (mysql_error());
		
		$ret = mysql_fetch_assoc($res);

		$this->close_db();
		
		if($ret == false) {
			return $ret;
		} else {
			return $ret['MandateID'];
		}
		
	}
	
	private function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	
}

?>