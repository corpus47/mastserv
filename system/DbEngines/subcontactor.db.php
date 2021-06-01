<?php

error_reporting(E_ALL ^ E_DEPRECATED);

class SubcontactorDB {
	
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
	
	public function get_subcontactor($id = NULL) {

		if($id == NULL){
			//return false;
		}
		$this->open_db();
		
		$sql = "SELECT * FROM sys_subcontactors WHERE ID = " . $id;
		$ret = mysql_query($sql,$this->connection) or die(mysql_error());
		//var_dump($ret);
		$ret = mysql_fetch_assoc($ret);
		//var_dump($ret);
		
		$this->close_db();
		
		return $ret;
	}
	
	public function set_active($id = NULL,$value = NULL) {
		if($id == NULL || $value == NULL){
			return false;
		}
		$this->open_db();
		
		$sql = "UPDATE sys_subcontactors SET Active = " . $value . " WHERE ID = " . $id;
		
		$ret = mysql_query($sql,$this->connection) or die (mysql_error());
		
		$this->close_db();
		
		if($ret == NULL) {
			return false;
		} else {
			return true;
		}
	}
	
	public function check_unique($fieldname = NULL, $value = NULL) {

		$this->open_db();
		if($fieldname != NULL && $value != NULL ) {
			//var_dump($value);
			//$value = 'kjlksdjljfsl';
			$sql = "SELECT * FROM sys_subcontactors WHERE " . $fieldname . " = '" . $value . "'";

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
	
	public function update_subcontactor($inserted = NULL) {
		//var_dump($inserted);
		if($inserted != NULL && is_array($inserted)) {
			$this->open_db();
			
		
			if($inserted['subcontactor-active'] == 'on') {
				$inserted['subcontactor-active'] = 1;
			} else {
				$inserted['subcontactor-active'] = 0;
			}
			$subcontactor_zips = str_replace("\r\n",',',$inserted['subcontactor-zips']);
			$inserted['subcontactor-zips'] = $subcontactor_zips;
			
			$sql = "UPDATE sys_subcontactors SET Name = '".$inserted['subcontactor-name']."', ContactPerson = '".$inserted['subcontactor-contactperson']."', Phone = '".$inserted['subcontactor-phonenum']."', Email = '".$inserted['subcontactor-email']."', Active = ".$inserted['subcontactor-active'].", Zips = '".$inserted['subcontactor-zips']."', Address = '".$inserted['subcontactor-address']."', AdminID = ".$inserted['subcontactor-admin_id']." WHERE ID = " . $inserted['id'];
			
			//var_dump($sql);exit;
			
			$ret =  mysql_query($sql,$this->connection) or die (mysql_error());
			
			$this->close_db();
			
			return $ret;
			
		}
	}
	
	public function add_subcontactor($inserted = NULL) {
		
		if($inserted === NULL) {
			return false;
		}
		
		$this->open_db();
		
		if(isset($inserted['subcontactor-active'])) {
			$active = 1;
		} else {
			$active = 0;
		}
		
		$inserted['subcontactor-zips'] = str_replace(" ",",",$inserted['subcontactor-zips']);
		
		$sql = "INSERT INTO sys_subcontactors (
												Name,
												Email,
												Phone,
												ContactPerson,
												DateOfAdd,
												Active,
												Zips,
												Locked,
												ModulesRule,
												Address
												) 
												VALUES 
												(
												'".$inserted['subcontactor-name']."',
												'".$inserted['subcontactor-email']."',
												'".$inserted['subcontactor-phonenum']."',
												'".$inserted['subcontactor-contactperson']."',
												now(),
												".$active.",
												'".$inserted['subcontactor-zips']."',
												NULL,
												'',
												'".$inserted['subcontactor-address']."
												)";
		$ret =  mysql_query($sql,$this->connection) or die (mysql_error());
		
		$this->close_db();
		
		return $ret;
	}
	
	public function list_subcontactors($filter = NULL) {
		
		$this->open_db();
		
		$sql = "SELECT * FROM sys_subcontactors";
		
		if($filter != NULL) {
			
			$sql .= $this->build_where($filter);
		}
		//var_dump($sql);
		$result = mysql_query($sql,$this->connection);
		
		$this->close_db();
		
		return $result;
	}
	
	/*private function build_where($wheres = NULL) {
	
		if(is_array($wheres)) {
			$where = ' WHERE ';
			foreach($wheres as $row) {
				$where .= $row . ",";
			}
			$where = preg_replace('/,$/','',$where);	
		}
		
		return $where;
	}*/
	
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
	
	public function unlock($id = NULL) {
		// Felszabadítás
		if($id == NULL) {
			return;
		}
		
		$this->open_db();
		
		$sql = "SELECT sys_subcontactors.Locked FROM sys_subcontactors WHERE ID = " . $id;
		
		$user = mysql_fetch_assoc(mysql_query($sql,$this->connection)) or die(mysql_error());
		
		if($user['Locked'] == $_SESSION["HDT_uid"]) {
		
			$sql = "UPDATE sys_subcontactors SET Locked = 0";
			
			mysql_query($sql,$this->connection);
		
		}
		
		$this->close_db();
		
		return;
		
	}
	
	public function lock($id = NULL) {
		// Lezárás
		if($id == NULL) {
			return;
		}
		
		$this->open_db();
			
		if(isset($_SESSION["HDT_uid"])) {
			
			$sql = "UPDATE sys_subcontactors SET Locked = " . $_SESSION['HDT_uid'] . " WHERE ID = ". $id;
			mysql_query($sql,$this->connection) or die(mysql_error());
		}
		
		$this->close_db();
		return;
	}
	
	public function locked($id = NULL) {
		// Lezárás vizsgálata
		if($id == NULL) {
			return;
		}
		
		$this->open_db();
		
		$sql = "SELECT sys_subcontactors.Locked FROM sys_subcontactors WHERE ID = " . $id;
		
		$user = mysql_fetch_assoc(mysql_query($sql,$this->connection)) or die (mysql_error());
		
		$ret =true;
		
		if($user['Locked'] == $_SESSION['HDT_uid']) {
			$ret =false;
			$this->close_db();
			return $ret;
		} elseif($user['Locked'] === null || $user['Locked'] == 0) {
			$ret =false;
			$this->close_db();
			return $ret;
		}
		$this->close_db();
			return $ret;
		
	}
	
}
?>
