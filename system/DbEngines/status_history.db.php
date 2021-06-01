<?php

error_reporting(E_ALL ^ E_DEPRECATED);

class Status_history {
	
	private $connection;
	
	public function __construct() {
		
	}
	
	private function open_db() {
		$this->connection = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die(mysql_error());
		mysql_set_charset('utf8',$this->connection);
		mysql_select_db(DB_NAME, $this->connection) or die(mysql_error());	
		
		$this->parcel_db = new ParcelDB();
		
		
	}
	
	private function close_db() {
		//mysql_close($this->connection);
	}	
	
	public function add_history($inserted = NULL) {
		
		$inserted['IP'] = $this->getUserIP();
		
		if($inserted['old_status'] == NULL){
			$inserted['old_status'] = 'NULL';
		}
		
		if(isset($_SESSION['HDT_parcel_user'])) {
			$inserted['user_type'] = 1;
			$inserted['user_id'] = $_SESSION['HDT_parcel_user'];
		} elseif(isset($_SESSION['HDT_master_user'])) {
			$inserted['user_type'] = 2;
			$inserted['user_id'] = $_SESSION['HDT_master_user'];
		} else {
			$inserted['user_type'] = 0;
			$inserted['user_id'] = $_SESSION['HDT_uid'];
		}
		
		$this->open_db();

		
		$sql = "INSERT INTO status_history ( mandate_id, changed_time, old_status, new_status, IP, user_id, user_type) VALUE ( ".$inserted['mandate_id'].", '".date("Y.m.d H:i:s")."', ".$inserted['old_status'].", ".$inserted['new_status'].", '".$inserted['IP']."', ".$inserted['user_id'].", ".$inserted['user_type']." )";
		
		//var_dump($sql);
		
		$ret =  mysql_query($sql,$this->connection) or die (mysql_error());
		
		$this->close_db();
		
		return $ret;
		
	}
	
	private function getUserIP() {
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_X_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if(isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
			$ipaddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
		else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if(isset($_SERVER['REMOTE_ADDR']))
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}
	
	public function history($mandate_id = NULL) {
		
		if($mandate_id != NULL) {
			
			$history = array();
			
			$this->open_db();
			
			$sql = "SELECT * FROM status_history WHERE mandate_id = " . $mandate_id;
			
			$ret =  mysql_query($sql,$this->connection) or die (mysql_error());
			
			while($row = mysql_fetch_assoc($ret)) {
				$history[] = $row;
			}
			
			$this->close_db();
			
			return $history;
			
		}
		return false;
	}
	
}
?>
