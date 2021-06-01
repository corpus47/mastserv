<?php
error_reporting(E_ALL ^ E_DEPRECATED);

class CitiesDB {

	private $connection;
	
	public function __construct(){
		
	}
	
	private function open_db() {
		$this->connection = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die(mysql_error());
		mysql_set_charset('utf8',$this->connection);
		mysql_select_db(DB_NAME, $this->connection) or die(mysql_error());	
	}
	
	private function close_db() {
		//mysql_close($this->connection);
	}
	
	public function get_city($zipcode = NULL) {
		
		if($zipcode == NULL){
			return false;
		}
		
		$this->open_db();
		
		$sql = "SELECT * FROM cities WHERE iranyitoszam = " . $iranyitoszam;
		
		$ret = mysql_fetch_assoc(mysql_query($sql,$this->connection)) or die (mysql_error());
		
		$this->close_db();
		
		return $ret;	
		
	}
	
	public function list_cities($filter = NULL) {
		
		$this->open_db();
		
		if($filter == NULL) {
			$sql = "SELECT * FROM cities";
		} else {
			$sql = $filter;
		}
		
		$result = mysql_query($sql,$this->connection) or die (mysql_error());

		$this->close_db();
		
		return $result;
		
	}

}
