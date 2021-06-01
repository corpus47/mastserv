<?php
error_reporting(E_ALL ^ E_DEPRECATED);

class SubclientsDB {
	
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
	
	public function add_subclient($inserted = NULL) {
		//var_dump($inserted);
		if($inserted === NULL) {
			return false;
		}
	
		$this->open_db();
	
		if(isset($inserted['subclient-active'])) {
			$active = 1;
		} else {
			$active = 0;
		}
		
		if(isset($inserted['subclient-master_cash'])) {
			$master_cash = 1;
		} else {
			$master_cash = 0;
		}

		$sql = "INSERT INTO subclients ( Name,
										Prefix,
										Zipcode,
										City,
										Address,
										Telephone,
										Email,
										Comment,
										Master_cash,
										DateOfAdd,
										ClientID,
										Active
										)
										VALUES
										( 
										'".$inserted['subclient-name']."',
										'".$inserted['subclient-prefix']."',
										'".$inserted['subclient-zipcode']."',
										'".$inserted['subclient-city']."',
										'".$inserted['subclient-address']."',
										'".$inserted['subclient-phonenum']."',
										'".$inserted['subclient-email']."',
										'".$inserted['subclient-comment']."',
										'".$master_cash."',
										now(),
										'".$inserted['subclient-clientid']."',
										".$active."
										)";
		//var_dump($sql);
		$ret =  mysql_query($sql,$this->connection) or die (mysql_error());
	
		$this->close_db();
	
		return $ret;
	
	}
	
	public function update_subclient($inserted = NULL) {
	
		if($inserted != NULL && is_array($inserted)) {
			
			$this->open_db();
				
			if($inserted['subclient-active'] == 'on') {
				$inserted['subclient-active'] = 1;
			} else {
				$inserted['subclient-active'] = 0;
			}
			
			if($inserted['subclient-master_cash'] == 'on') {
				$inserted['subclient-master_cash'] = 1;
			} else {
				$inserted['subclient-master_cash'] = 0;
			}
			
			$subclient_users = array();
			
			if(isset($inserted['subclient-admin-user']) && is_array($inserted['subclient-admin-user'])) {
				foreach($inserted['subclient-admin-user'] as $key=>$row) {
					$subclient_users['admin'][] = $key;
				}
			}
			if(isset($inserted['subclient-user-user']) && is_array($inserted['subclient-user-user'])) {
				foreach($inserted['subclient-user-user'] as $key=>$row) {
					$subclient_users['user'][] = $key;
				}
			}
			if(isset($inserted['subclient-import-user']) && is_array($inserted['subclient-import-user'])) {
				foreach($inserted['subclient-import-user'] as $key=>$row) {
					$subclient_users['import'][] = $key;
				}
			}
			if(isset($inserted['subclient-export-user']) && is_array($inserted['subclient-export-user'])) {
				foreach($inserted['subclient-export-user'] as $key=>$row) {
					$subclient_users['export'][] = $key;
				}
			}
			
			//var_dump($subclient_users);exit;
			
			$sql = "UPDATE subclients SET Name = '".$inserted['subclient-name']."',
										  Prefix = '".$inserted['subclient-prefix']."',
										  Zipcode = '".$inserted['subclient-zipcode']."',
										  City = '".$inserted['subclient-city']."',
										  Address = '".$inserted['subclient-address']."',
										  Telephone = '".$inserted['subclient-phonenum']."',
										  Email = '".$inserted['subclient-email']."',
										  Comment = '".$inserted['subclient-comment']."',
										  Master_cash = '".$inserted['subclient-master_cash']."',
										  Users = '".serialize($subclient_users)."',
										  Active = ".$inserted['subclient-active']." WHERE ID = " . $inserted['id'];
				
			//var_dump($sql);
				
			$ret =  mysql_query($sql,$this->connection) or die (mysql_error());
				//exit;
			$this->close_db();
				
			return $ret;
				
		}
	
	}
	
	public function get_subclient($id = NULL) {
	
		if($id == NULL){
			//return false;
		}
		$this->open_db();
	
		$sql = "SELECT * FROM subclients WHERE ID = " . $id;
		//var_dump($sql);
		$ret = mysql_query($sql,$this->connection) or die(mysql_error());
	
		$ret = mysql_fetch_assoc($ret);
		//var_dump($ret);
	
		$this->close_db();
	
		return $ret;
	}
	
	public function delete_subclient($inserted = NULL) {
	
		$this->open_db();
	
		$sql = "DELETE FROM subclients WHERE ID = ". $inserted['id'];
	
		$ret =  mysql_query($sql,$this->connection) or die (mysql_error());
	
		$this->close_db();
	
		return $ret;
	
	}
	
	public function set_active($id = NULL,$value = NULL) {
		if($id == NULL || $value == NULL){
			return false;
		}
		$this->open_db();
	
		$sql = "UPDATE subclients SET Active = " . $value . " WHERE ID = " . $id;
	
		$ret = mysql_query($sql,$this->connection) or die (mysql_error());
		//var_dump($sql);
		$this->close_db();
	
		if($ret == NULL) {
			return false;
		} else {
			return true;
		}
	}
	
	public function list_subclients($filter = NULL) {
	
		$this->open_db();
	
		$sql = "SELECT * FROM subclients";
	
		if($filter != NULL) {
				
			$sql .= $this->build_where($filter);
		}
		//var_dump($sql);
		$result = mysql_query($sql,$this->connection);
	
		$this->close_db();
	
		return $result;
	}
	
	public function check_unique($fieldname = NULL, $value = NULL) {
	
		$this->open_db();
		if($fieldname != NULL && $value != NULL ) {
			$sql = "SELECT * FROM subclients WHERE " . $fieldname . " = '" . $value . "'";
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
	
	private function build_where($wheres = NULL) {
	
		/*if(is_array($wheres)) {
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
		}*/
		
		if(is_array($wheres)) {
			//$where = ' WHERE ';
			$where = '';
			$order = '';
			$limit = '';
			foreach($wheres as $row) {
				if(strpos($row,"ORDER") !== false) {
					$order = trim($row);
				} elseif(strpos($row,"LIMIT") !== false){
					$limit = trim($row);
				} else {
					$where .= $row . " AND ";
				}
			}
			$where = preg_replace('/ AND $/','',$where);
			
			if($where != '') {
				$where = ' WHERE ' . $where;
			}
			
			//$where .= ")";
			if(trim($order) != "") {
				$where .= " " . trim($order);
			}
			if(trim($limit) != "") {
				$where .= " " . trim($limit);
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
	
		$sql = "SELECT subclients.Locked FROM subclients WHERE ID = " . $id;
	
		$user = mysql_fetch_assoc(mysql_query($sql,$this->connection)) or die(mysql_error());
	
		if($user['Locked'] == $_SESSION["HDT_uid"]) {
	
			$sql = "UPDATE subclients SET Locked = 0";
				
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
				
			$sql = "UPDATE subclients SET Locked = " . $_SESSION['HDT_uid'] . " WHERE ID = ". $id;
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
	
		$sql = "SELECT subclients.Locked FROM subclients WHERE ID = " . $id;
	
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
