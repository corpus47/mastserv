<?php
error_reporting(E_ALL ^ E_DEPRECATED);

class ParcelDB {
	
	private $connection;
	
	public function __construct() {
			
	}

	private function open_db() {
		$this->connection = mysql_connect(PARCEL_DB_HOST, PARCEL_DB_USER, PARCEL_DB_PASSWORD) or die(mysql_error());
		mysql_set_charset('utf8',$this->connection);
		mysql_select_db(PARCEL_DB_NAME, $this->connection) or die(mysql_error());	
	}
	
	private function close_db() {
		//mysql_close($this->connection);
	}	
	
	public function list_partners($filter = NULL) {
		
		$this->open_db();
		
		$sql = "SELECT * FROM partners";
		
		if($filter != NULL) {

			$sql .= $this->build_where($filter);
		}
		
		$ret = mysql_query($sql,$this->connection) or die (mysql_error());
		
		$this->close_db();
		
		return $ret;
		
	}
	
	public function list_clients($filter = NULL) {
		
		$this->open_db();
		
		$sql = "SELECT * FROM partner_group";

		if($filter == NULL) {
		
			$sql .= $filter;
		
		}
		
		$ret = mysql_query($sql,$this->connection) or die (mysql_error());
		
		$this->close_db();
		
		return $ret;
		
	}
	
	public function get_user($id = NULL) {
		if($id == NULL){
			return false;
		}
		$this->open_db();
		
		$sql = "SELECT * FROM users WHERE user_id = " . $id;
		
		$ret = mysql_fetch_assoc(mysql_query($sql,$this->connection)) or die (mysql_error());
		
		$user = false;
		
		if($ret != false) {
			$user['user_id'] = $ret['user_id'];
			$user['ID'] = $ret['user_id'];
			$user['FullName'] = 'HDT -> ' . $ret['user_fullname'];
			$user['Phone'] = $ret['user_telephone'];
			$user['Login'] = $ret['user_login'];
			$user['Partner'] = $ret['user_partner_id'];
			$user['UserType'] = 5;
		} 
		
		$this->close_db();
		
		return $user;
	}
	
	public function add_user($inserted = array()) {
		
	}
	
	public function list_users($filter = NULL) {
		$this->open_db();
		
		if($filter == NULL) {
			$sql = "SELECT * FROM users";
		} else {
			$sql = $filter;
		}
		
		$result = mysql_query($sql,$this->connection);
		
		$this->close_db();
		
		return $result;
	}
	
	public function login_user($username = NULL,$password = NULL) {
		
		$this->open_db();
		
		// Az id ha sessionb�l �rkezik
		/*if($id !== NULL) {
			$sql = "SELECT * FROM users WHERE user_id = '".$id."'";
			
			$result = mysql_query($sql,$this->connection);
			
		} else*/
		
		if($username !== NULL && $password !== NULL) {
			
			$uname = mysql_real_escape_string($username);
			$passwd = md5(mysql_real_escape_string($password));
			
			$sql = "SELECT * FROM users WHERE user_login = '".$uname."' AND user_password = '".$passwd."'";
			
			$result = mysql_query($sql,$this->connection) or die(mysql_error());
			
			if($result){
				$user = mysql_fetch_assoc($result);
			} else {
				return NULL;
			}
				
		}
		$this->close_db();
		
		return $user;
	}
	
	public function get_partner($id = NULL) {
		//var_dump($id);
		if($id != NULL) {
			
			$this->open_db();
			
			$sql = "SELECT * FROM partners WHERE partner_id = " . $id;
			
			$result = mysql_query($sql,$this->connection);
			
			if($result != false) {
				
				$partner = mysql_fetch_assoc($result) or die(mysql_error());
				
			} else {
				$partner = NULL;
			}
			
			$this->close_db();
			
		}

		return $partner;
		
	}
	
	public function get_client($id = NULL) {
		
		if($id != NULL) {
			
			$this->open_db();
			
			$sql = "SELECT * FROM partner_group WHERE partner_group_id = " . $id;
			
			$result = mysql_query($sql,$this->connection);
			
			if($result != false) {
				
				$partner = mysql_fetch_assoc($result) or die(mysql_error());
				
			} else {
				$partner = NULL;
			}
			
			$this->close_db();
			
		}
		
		return $partner;
		
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
