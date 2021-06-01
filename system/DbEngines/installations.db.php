<?php

error_reporting(E_ALL ^ E_DEPRECATED);

class InstallationsDB {
	
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
	
	public function get_installation($id = NULL) {
		if($id == NULL){
			return false;
		}
		$this->open_db();
		
		$sql = "SELECT * FROM installations_cat WHERE ID = " . $id;
		
		$ret = mysql_fetch_assoc(mysql_query($sql,$this->connection)) or die (mysql_error());
		
		$this->close_db();
		
		return $ret;
	}
	
	public function add_item($cat_id = NULL, $item_name = NULL, $item_cost = NULL) {
		
		if($item_name == NULL || $item_cost == NULL) {
			return false;
		}	
		
	}
	
	public function check_unique($fieldname = NULL, $value = NULL) {

		$this->open_db();
		
		if($fieldname != NULL && $value != NULL ) {

			$sql = "SELECT * FROM installations_cat WHERE " . $fieldname . " = '" . $value . "'";

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
	
	public function list_installations($filter = NULL) {
	
		$this->open_db();
		
		$sql = "SELECT * FROM installations_cat";
		
		if($filter != NULL) {
			$sql .= $this->build_where($filter);
		}
		
		$result = mysql_query($sql,$this->connection);
		
		$this->close_db();
		
		return $result;
	
	}
	
	public function get_item($id = NULL) {
		if($id != NULL) {
			$this->open_db();
			
			$sql = "SELECT * FROM installations WHERE ID = " . $id;
			
			$res = mysql_query($sql,$this->connection) or die(mysql_error());
			
			$this->close_db();
			
			return mysql_fetch_assoc($res);
		}
	}
	
	public function get_items($id = NULL) {
		if($id != NULL) {
			$this->open_db();
			
			$sql = "SELECT * FROM installations WHERE CatID = " . $id;
			
			$items = array();
			
			$res = mysql_query($sql,$this->connection) or die(mysql_error());
			
			while($row = mysql_fetch_assoc($res)) {
				$items[] = $row;
			}
			
			$this->close_db();
			
			return $items;
		}
	}
	
	public function add_installation($inserted = NULL) {
		
		if($inserted != NULL && is_array($inserted)) {
		
			//var_export($inserted);exit;
		
			$this->open_db();
		
			//$sql = "INSERT INTO installations_cat ( CategoryName, Cost ) VALUE ( '".$inserted['installation-cat-name']."', '".$inserted['installation-cost']."')";
			
			$sql = "INSERT INTO installations_cat ( CategoryName ) VALUE ( '".$inserted['installation-cat-name']."')";
			
			$ret =  mysql_query($sql,$this->connection) or die (mysql_error());
		
			$this->close_db();
			
			
	
			if(isset($inserted['installation-cat-item-name'])){

				//if(is_array($inserted['installation-cat-item-name']) && is_array($inserted['installation-cat-item-cost'])) {
				
				if(is_array($inserted['installation-cat-item-name'])) {
					
					$this->open_db();
					
					$sql = "SELECT * FROM installations_cat WHERE CategoryName = '".$inserted['installation-cat-name']."'";
					
					$result =  mysql_query($sql,$this->connection) or die (mysql_error());
					
					$cat = mysql_fetch_assoc($result);
						
					$max = count($inserted['installation-cat-item-name']);
					
					$sql = '';
					
					for($i = 1;$i <= $max;$i++) {
						
						//$sql = "INSERT INTO installations ( InstallationName, CatID, Cost, Comment ) VALUE ( '".$inserted['installation-cat-item-name'][$i]."', ".$cat['ID'].", '".$inserted['installation-cat-item-cost'][$i]."', '')";
						
						$sql = "INSERT INTO installations ( InstallationName, CatID, Comment, Req_Time ) VALUE ( '".$inserted['installation-cat-item-name'][$i]."', ".$cat['ID'].", '', ".$inserted['installation-cat-item-req_time'][$i].")";
						
						$r = mysql_query($sql,$this->connection) or die (mysql_error());
						
						$sql = '';
						
					}
					
					$this->close_db();

				}
			}
			
			return $ret;
			
		}
	
	}
	
	public function update_installation($inserted = NULL) {
		//var_dump($inserted);exit;
		if($inserted != NULL && is_array($inserted)) {
			
			$this->open_db();
			
			//$sql = "UPDATE installations_cat SET CategoryName = '".$inserted['installation-cat-name']."', Cost = ".$inserted['installation-cost']." WHERE ID = ".$inserted['id'];
			
			$sql = "UPDATE installations_cat SET CategoryName = '".$inserted['installation-cat-name']."'  WHERE ID = ".$inserted['id'];
			
			$ret = mysql_query($sql,$this->connection) or die (mysql_error());
			
			$this->close_db();
			
			if(isset($inserted['installation-cat-item-name'])){

				//if(is_array($inserted['installation-cat-item-name']) && is_array($inserted['installation-cat-item-cost'])) {
				
				if(is_array($inserted['installation-cat-item-name'])) {
					
					$this->open_db();
					
					// Delete all installation item_cost
					
					//$sql = "DELETE FROM installations WHERE CatID = " . $inserted['id'];
					
					$r = mysql_query($sql,$this->connection) or die (mysql_error());
					
					$max = count($inserted['installation-cat-item-name']);
					
					$sql = '';
					
					for($i = 1;$i <= $max;$i++) {
						
						//$sql = "INSERT INTO installations ( InstallationName, CatID, Cost, Comment ) VALUE ( '".$inserted['installation-cat-item-name'][$i]."', ".$inserted['id'].", '".$inserted['installation-cat-item-cost'][$i]."', '')";
						if($inserted['installation-cat-item-id'][$i] == 'null'){
							$sql = "INSERT INTO installations ( InstallationName, CatID, Comment, Req_Time ) VALUE ( '".$inserted['installation-cat-item-name'][$i]."', ".$inserted['id'].", '', ".$inserted['installation-cat-item-req_time'][$i].")";
						} else {
							$sql = "UPDATE installations SET InstallationName = '".$inserted['installation-cat-item-name'][$i]."', CatID = ".$inserted['id'].", Comment = '', Req_Time = ".$inserted['installation-cat-item-req_time'][$i]." WHERE ID = ".$inserted['installation-cat-item-id'][$i];
						}
						//var_dump($sql);exit;
						$r = mysql_query($sql,$this->connection) or die (mysql_error());
						
						$sql = '';
						
					}
					
					$this->close_db();
					
				}
			}
			
			return $ret;
		}
	}
	
}
