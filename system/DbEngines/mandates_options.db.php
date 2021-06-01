<?php

error_reporting(E_ALL ^ E_DEPRECATED);

class Mandates_optionsDB {

	private $connection;
	
	public function __construct() {
			
	}
	
	private function open_db() {
		$this->connection = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die(mysql_error());
		mysql_set_charset('utf8',$this->connection);
		mysql_select_db(DB_NAME, $this->connection) or die(mysql_error());	
	}
	
	private function close_db() {
		//mysql_close($this->connection);
	}
	
	private function build_where($wheres = NULL) {
	
		if(is_array($wheres)) {
			$where = ' WHERE ';
			foreach($wheres as $row) {
				$where .= $row . ",";
			}
			$where = preg_replace('/,$/','',$where);	
		}
		
		return $where;
	}
	
	public function get_mandates_option($id) {
		
		if($id == NULL){
			return false;
		}
		
		$this->open_db();
		
		$sql = "SELECT * FROM mandates_options WHERE ID = ". $id;
		
		$ret = mysql_fetch_assoc(mysql_query($sql,$this->connection)) or die (mysql_error());
		
		$this->close_db();
		
		return $ret;
		
	}
	
	public function add_mandates_option($inserted) {
	
		$this->open_db();
		
		$sql = "INSERT INTO mandates_options (OptionName, Distance) VALUE ( '".$inserted['mandates-option-name']."', ".$inserted['mandates-option-distance'].")";

		$ret =  mysql_query($sql,$this->connection) or die (mysql_error());
		
		$this->close_db();
		
		return $ret;
	
	}
	
	public function update_mandates_option($inserted) {
		
		if($inserted != NULL && is_array($inserted)) {
		
			$this->open_db();
		
			$sql = "UPDATE mandates_options SET OptionName = '".$inserted['mandates-option-name']."', Distance = ".$inserted['mandates-option-distance'] . " WHERE ID = " . $inserted['id'];
		
			$ret = mysql_query($sql,$this->connection) or die (mysql_error());
		
			$this->close_db();
		
			return $ret;
		
		}
		
	}
	
	public function check_unique($fieldname = NULL, $value = NULL) {

		$this->open_db();
		
		if($fieldname != NULL && $value != NULL ) {

			$sql = "SELECT * FROM mandates_options WHERE " . $fieldname . " = '" . $value . "'";

			$ret = mysql_fetch_assoc(mysql_query($sql,$this->connection));
		} else {
			$ret = true;
		}
		$this->close_db();

		if(isset($ret['ID'])) {
			$ret = false;
		} else {
			$ret = true;
		}

		return $ret;
	}
	
	public function list_mandates_options($filter = NULL) {
		
		$this->open_db();
		
		$sql = "SELECT * FROM mandates_options";
		
		if($filter != NULL) {
			$sql .= $this->build_where($filter);
		}
		
		$result = mysql_query($sql,$this->connection);
		
		$this->close_db();
		
		return $result;
		
	}

}
