<?php

error_reporting(E_ALL ^ E_DEPRECATED);

class ClientoptionsDB {

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
	
	public function get_option($filter = NULL) {
		$this->open_db();
		
		$sql = "SELECT * FROM client_options";
		
		if($filter != NULL) {
		
			$sql .= $this->build_where($filter);
		}
		
		//var_dump($sql);
		
		$result = mysql_query($sql,$this->connection) or die(mysql_error());

		$this->close_db();

		return $result;
	}
	
	public function list_subclientoptions($client_id = NULL) {
	
		if($client_id == NULL) {
			return false;
		}
		
		$this->open_db();
		
		$sql = "SELECT * FROM client_options WHERE Subclient_ID = ".$client_id;
		
		$res = mysql_query($sql,$this->connection) or die(mysql_error());
		
		
		
		$result = array();
		
		while($row = mysql_fetch_assoc($res)) {
			$result[] = $row;
		}
		
		$this->close_db();
		
		return $result;
		
	}
	
	public function list_clientoptions($client_id = NULL) {
	
		if($client_id == NULL) {
			return false;
		}
		
		$this->open_db();
		
		$sql = "SELECT * FROM client_options WHERE Client_ID = ".$client_id;
		
		$res = mysql_query($sql,$this->connection) or die(mysql_error());
		
		
		
		$result = array();
		
		while($row = mysql_fetch_assoc($res)) {
			$result[] = $row;
		}
		
		$this->close_db();
		
		return $result;
		
	}
	
	public function update_subclientoptions($inserted) {
	
		$this->open_db();
		
		//$sql = "DELETE FROM client_options WHERE partner_group_id = " . $inserted['partner_group_id'];
		
		$sql = "DELETE FROM client_options WHERE Subclient_ID = " . $inserted['id'];
		
		$ret = mysql_query($sql,$this->connection) or die (mysql_error());

		$this->close_db();
		
		/*echo '<pre>';var_export($inserted);echo '</pre>';*/
		
		if(!isset($inserted['client-installation'])) {
			return $ret;
		}
		
		foreach($inserted['client-installation'] as $key=>$value) {
			if(isset($inserted['client-percent'][$key])) {
				$percent = 1;
			} else {
				$percent = 0;
			}
			foreach($inserted['option'][$key] as $option_key=>$option_value) {
				//var_dump($option_value);
				/*foreach($option_value as $option_value_key=>$single_value) {*/
					if($option_value != ''){
						//$sql = "INSERT INTO client_options (partner_group_id, installation_id, mandates_option_id, percent, value) VALUES ( ".$inserted['partner_group_id'].",".$key.",".$option_key.",".$percent.",".$option_value.")";
						$sql = "INSERT INTO client_options (Subclient_ID, installation_id, mandates_option_id, percent, value) VALUES ( ".$inserted['id'].",".$key.",".$option_key.",".$percent.",".$option_value.")";
						//echo $sql . '<br />';
						//var_dump($sql);exit;
						$this->open_db();
						$ret = mysql_query($sql,$this->connection) or die (mysql_error());
						$this->close_db();
					}
				/*}*/
			}
		}
		
		return $ret;
	
	}
	
	public function update_clientoptions($inserted) {
	
		$this->open_db();
		
		//$sql = "DELETE FROM client_options WHERE partner_group_id = " . $inserted['partner_group_id'];
		
		$sql = "DELETE FROM client_options WHERE Client_ID = " . $inserted['id'];
		
		$ret = mysql_query($sql,$this->connection) or die (mysql_error());

		$this->close_db();
		
		/*echo '<pre>';var_export($inserted);echo '</pre>';*/
		
		if(!isset($inserted['client-installation'])) {
			return $ret;
		}
		
		foreach($inserted['client-installation'] as $key=>$value) {
			if(isset($inserted['client-percent'][$key])) {
				$percent = 1;
			} else {
				$percent = 0;
			}
			foreach($inserted['option'][$key] as $option_key=>$option_value) {
				//var_dump($option_value);
				/*foreach($option_value as $option_value_key=>$single_value) {*/
					if($option_value != ''){
						//$sql = "INSERT INTO client_options (partner_group_id, installation_id, mandates_option_id, percent, value) VALUES ( ".$inserted['partner_group_id'].",".$key.",".$option_key.",".$percent.",".$option_value.")";
						$sql = "INSERT INTO client_options (Client_ID, installation_id, mandates_option_id, percent, value) VALUES ( ".$inserted['id'].",".$key.",".$option_key.",".$percent.",".$option_value.")";
						//echo $sql . '<br />';
						//var_dump($sql);exit;
						$this->open_db();
						$ret = mysql_query($sql,$this->connection) or die (mysql_error());
						$this->close_db();
					}
				/*}*/
			}
		}
		
		return $ret;
	
	}
	
	private function build_where($wheres = NULL) {
	
		if(is_array($wheres)) {
			$where = ' WHERE ';
			$order = '';
			foreach($wheres as $row) {
				if(strpos($row,"ORDER") !== false) {
					$order = trim($row);
				} else {
					$where .= $row . " AND ";
				}
			}
			$where = preg_replace('/ AND $/','',$where);
			if(trim($order) != "") {
				$where .= " " . trim($order);
			}	
		}
		
		return $where;
	}
	
}

?>
