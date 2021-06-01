<?php
error_reporting(E_ALL ^ E_DEPRECATED);

class MasterDB {
	
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
	
	public function get_master($id = NULL) {
		if($id == NULL){
			return false;
		}
		$this->open_db();
		
		$sql = "SELECT * FROM master_masters WHERE ID = " . $id;
		
		$ret = mysql_fetch_assoc(mysql_query($sql,$this->connection)) or die (mysql_error());
		
		$this->close_db();
		
		return $ret;
	}
	
	public function locked($id = NULL) {
		// Lezárás vizsgálata
		if($id == NULL) {
			return;
		}
		
		$this->open_db();
		
		$sql = "SELECT master_masters.Locked FROM master_masters WHERE ID = " . $id;
		
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
	
	public function unlock($id = NULL) {
		// Felszabadítás
		if($id == NULL) {
			return;
		}
		
		$this->open_db();
		
		$sql = "SELECT master_masters.Locked FROM master_masters WHERE ID = " . $id;
		
		$user = mysql_fetch_assoc(mysql_query($sql,$this->connection)) or die(mysql_error());
		
		if($user['Locked'] == $_SESSION["HDT_uid"]) {
		
			$sql = "UPDATE master_masters SET Locked = 0";
			
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
			
			$sql = "UPDATE master_masters SET Locked = " . $_SESSION['HDT_uid'] . " WHERE ID = ". $id;
			mysql_query($sql,$this->connection) or die(mysql_error());
		}
		
		$this->close_db();
		return;
	}
	
	public function set_active($id = NULL,$value = NULL) {
		if($id == NULL || $value == NULL){
			return false;
		}
		$this->open_db();
		
		$sql = "UPDATE master_masters SET Active = " . $value . " WHERE ID = " . $id;
		
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

			$sql = "SELECT * FROM master_masters WHERE " . $fieldname . " = '" . $value . "'";

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
	
	public function update_master($inserted = NULL) {

		if($inserted != NULL && is_array($inserted)) {
			$this->open_db();
			
		
			if($inserted['master-active'] == 'on') {
				$inserted['master-active'] = 1;
			} else {
				$inserted['master-active'] = 0;
			}
				//var_dump($inserted['Active']);exit;
			
			$master_installations = array();
			
			if(isset($inserted['master-installation'])) {
			
				foreach($inserted['master-installation'] as $key=>$row){
					$master_installations[] = $key;
				}
			
			}
			
			$master_installations = serialize($master_installations);
			
			if($inserted['pwd-one'] != "") {
				
				$salt = substr( md5(rand()), 0, 10);
				$password = sha1($inserted['pwd-one']. $salt);
				
				$sql = "UPDATE master_masters SET Pwd ='".$password."', Salt = '".$salt."', Phone = '".$inserted['master-phonenum']."', Email = '".$inserted['master-email']."', Cartype = '".$inserted['master-cartype']."', Comment = '".$inserted['master-comment']."', Active = ".$inserted['master-active'].", Subconid = ".$inserted["master-subconid"].", installations = '".$master_installations."' WHERE ID = " . $inserted['id'];
			} else {
				$sql = "UPDATE master_masters SET Phone = '".$inserted['master-phonenum']."', Email = '".$inserted['master-email']."', Cartype = '".$inserted['master-cartype']."', Comment = '".$inserted['master-comment']."',  Active = ".$inserted['master-active'].", Subconid = ".$inserted["master-subconid"].", installations = '".$master_installations."' WHERE ID = " . $inserted['id'];
			}
			
			//var_dump($sql);exit;
			
			$ret =  mysql_query($sql,$this->connection) or die (mysql_error());
			
			//var_dump($ret);exit;
			
			$this->close_db();
			
			return $ret;
			
		}
	}
	
	public function add_master($inserted = NULL) {
	//var_dump($inserted);exit;
		if($inserted != NULL && is_array($inserted)) {
			
			$this->open_db();
			
			// Jelszó sózás
			
			$salt = substr( md5(rand()), 0, 10);
			
			$password = sha1($inserted['pwd-one']. $salt);
			
			if($inserted['master-active'] == 'on') {
				$active = 1;
			} else {
				$active = 0;
			}
			
			$master_installations = array();
			
			foreach($inserted['master-installation'] as $key=>$row){
				$master_installations[] = $key;
			}
			
			$master_installations = serialize($master_installations);
			
			//var_dump($master_installations);exit;
			
			$sql = "INSERT INTO master_masters (
										Name,
										Phone,
										Email,
										DateOfAdd,
										Pwd,
										Salt,
										Cartype,
										LPNumber,
										Comment,
										Active,
										Subconid,
										ModulesRule,
										Locked,
										installations
										)
										VALUES
										(
										'".$inserted['master-name']."',
										'".$inserted['master-phonenum']."',
										'".$inserted['master-email']."',
										now(),
										'".$password."',
										'".$salt."',
										'".$inserted['master-cartype']."',
										'".$inserted['master-lpnumber']."',
										'".$inserted['master-comment']."',
										'".$active."',
										'".$inserted['master-subconid']."',
										'',
										NULL,
										'".$master_installations."'
										)";
			$ret =  mysql_query($sql,$this->connection) or die (mysql_error());
		
			$this->close_db();
		
			return $ret;
		}
	}
	
	public function list_masters($filter = NULL) {
		$this->open_db();
		
		$sql = "SELECT * FROM master_masters";
		
		if($filter != NULL) {

			$sql .= $this->build_where($filter);
		}
		
		//var_dump($sql);
		
		$result = mysql_query($sql,$this->connection);
		
		$this->close_db();
		
		return $result;
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
	
	/*public function login_master($master_name = NULL, $master_password = NULL) {
		
		return false;
		
	}*/
	
	public function login_master($username = NULL,$password = NULL) {
		
		$this->open_db();
		
		if($username !== NULL && $password !== NULL) {
			
			$uname = mysql_real_escape_string($username);
			
			$uname = str_replace("0","",$uname);
			
			$sql = "SELECT * FROM master_masters WHERE ID = '".(int)$uname."'";
			
			$result = mysql_query($sql,$this->connection);
			
			$master = mysql_fetch_assoc($result);
			
			//var_dump($master);exit;
			
			if($master !== false) {
				$passwd = sha1($password . $master['Salt']);

				if($master['Pwd'] !== $passwd) {
					$ret = false;
				}
			} else {
				//$ret = NULL;
			}	
			
		}
		
		$this->close_db();
		
		//var_dump($ret);exit;
		
		if(isset($ret) && $ret === false) {
			//var_dump('itt');exit;
			return false;
		} else {
			//$_SESSION['HDT_master_user'] = $master['ID'];
			return $master['ID'];
		}
		
	}
	
}
?>
