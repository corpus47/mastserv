<?php

error_reporting(E_ALL ^ E_DEPRECATED);

class User_loggedDB {
	
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
	
	public function add_log($uid = null, $master = false) {
		
		$this->open_db();
		
		//var_dump($_SESSION);
		
		//$uid = $_SESSION['HDT_uid'];
		
		if($master !== false){
		
			$master_uid = $uid;
			
		} else {
		
			$master_uid = 'NULL';
		
		}
		//var_dump($master);exit;
		$session_id = session_id();
		//var_dump($session_id);
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		
		$sql = "INSERT INTO user_logged ( Uid, MasterUid, LoginTime, Session, IP ) VALUE ( " . $uid .", " . $master_uid . ", now(), '" . $session_id . "', '" . $ip . "')";
		//var_dump($sql);exit;
		$ret = mysql_query($sql,$this->connection) or die(mysql_error());
		
		$this->close_db();
		
		return $ret;
		
	}
	
	public function add_check() {
		
		$this->open_db();
		
		if(isset($_SESSION['HDT_master_user'])) {
			$sql = "UPDATE user_logged SET LastCheck = now() WHERE MasterUid = " . $_SESSION['HDT_master_user'] . " AND Session = '" . session_id() . "' AND Logout IS NULL";
		} else {
			$sql = "UPDATE user_logged SET LastCheck = now() WHERE Uid = " . $_SESSION['HDT_uid'] . " AND Session = '" . session_id() . "' AND Logout IS NULL";
		}
		
		$ret = mysql_query($sql,$this->connection) or die(mysql_error());

		$this->close_db();
		
		return $ret;
		
	}
	
	public function close_logout($uid = null,$session = false, $master = false) {
		$this->open_db();
		
		if($master != false) {
			$sql = "UPDATE user_logged SET Logout = now() WHERE MasterUid = " . $uid . " AND Session = '" . $session . "'";
		} else {
			$sql = "UPDATE user_logged SET Logout = now() WHERE Uid = " . $uid . " AND Session = '" . $session . "'";
		}
		
		$ret = mysql_query($sql,$this->connection) or die(mysql_error());

		$this->close_db();
		
		return $ret;
	}
	
	public function set_logout() {
		
		$this->open_db();
		
		if(isset($_SESSION['HDT_master_user'])) {
			$sql = "UPDATE user_logged SET Logout = now() WHERE MasterUid = " . $_SESSION['HDT_master_user'] . " AND Session = '" . session_id() . "'";
		} else {
			$sql = "UPDATE user_logged SET Logout = now() WHERE Uid = " . $_SESSION['HDT_uid'] . " AND Session = '" . session_id() . "'";
		}
		
		$ret = mysql_query($sql,$this->connection) or die(mysql_error());

		$this->close_db();
		
		return $ret;
		
	}
	
	public function get_logs($filter = null) {
	
		if(!is_array($filter) || $filter == null) {
			return false;
		}
		
		$sql = "SELECT * FROM user_logged";
		
		$sql .= $this->build_where($filter);
		
		//var_dump($sql);
		
		$this->open_db();
		
		$ret = mysql_query($sql,$this->connection) or die(mysql_error());

		$this->close_db();
		
		return $ret;
	
	}
	
	private function build_where($wheres = NULL) {
	
		if(is_array($wheres)) {
			$where = ' WHERE ';
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
	
}
?>
