<?php
error_reporting(E_ALL ^ E_DEPRECATED);

class DaysDB {
	
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
	
	public function add_day($inserted) {
		//var_dump($inserted);exit;
		
		if(isset($inserted['day-allyear']) && $inserted['day-allyear'] == 'on') {
			$allyear = 1;
		} else {
			$allyear = 0;
		}
		
		if(isset($inserted['day-workingday']) && $inserted['day-workingday'] == 'on') {
			$workingday = 1;
		} else {
			$workingday = 0;
		}
		
		$this->open_db();
		
		$sql = "INSERT INTO days (Datum, AllYear, WorkingDay) VALUE ('".$inserted['day-datum']."', ".$allyear.", ".$workingday.")";
		
		//var_dump($sql);
		
		$ret = mysql_query($sql,$this->connection) or die (mysql_error());
		
		$this->close_db();
		
		return $ret;
		
	}
	
	public function get_allyear_disabled() {
		
		$this->open_db();
		
		$sql = "SELECT * FROM days WHERE AllYear = 1";
		
		$ret = mysql_query($sql,$this->connection) or die (mysql_error());
		
		$this->close_db();
		
		return $ret;
		
	}
	
	public function get_datum($datum = NULL) {
		
		$this->open_db();
		
		$sql = "SELECT * FROM days WHERE DATE(Datum) = '" . $datum . "'";
		
		$res = mysql_query($sql,$this->connection) or die (mysql_error());
		
		$ret = mysql_fetch_assoc($res);
		
		$this->close_db();
		
		return $ret;
		
	}
	
	public function load_day($id = NULL) {
		
		$this->open_db();
		
		$sql = "SELECT * FROM days WHERE ID = ". $id;
		
		$ret = mysql_fetch_assoc(mysql_query($sql,$this->connection)) or die (mysql_error());
		
		$this->close_db();
		
		return $ret;
	}
	
	public function update_day($inserted) {
		
		if(isset($inserted['day-allyear']) && $inserted['day-allyear'] == 'on') {
			$allyear = 1;
		} else {
			$allyear = 0;
		}
		
		if(isset($inserted['day-workingday']) && $inserted['day-workingday'] == 'on') {
			$workingday = 1;
		} else {
			$workingday = 0;
		}
		
		$this->open_db();
		
		$sql = "UPDATE days SET Datum = '" . $inserted['day-datum'] . "', AllYear = " . $allyear . ", WorkingDay = " . $workingday . " WHERE ID = " . $inserted['id'];
		//var_dump($sql);
		$ret = mysql_query($sql,$this->connection) or die (mysql_error());
		
		$this->close_db();
		
		return $ret;
		
	}
	
	public function list_days($filter = NULL) {
		
		$this->open_db();
		
		$sql = "SELECT * FROM days";
		
		$ret = mysql_query($sql,$this->connection) or die(mysql_error());
		
		$this->close_db();
		
		return $ret;
		
	}
	
}