<?php

error_reporting(E_ALL ^ E_DEPRECATED);

class ClientsDB {
	
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
	
	public function unset_owner($inserted = NULL) {
		
		$this->open_db();
		
		$sql = "SELECT * FROM clients WHERE Owner = " . $inserted['id'];

		$ret = mysql_query($sql,$this->connection) or die(mysql_error());
		
		$row = mysql_fetch_assoc($ret);
		
		if($row != false) {
			$sql = "UPDATE clients SET Owner = NULL WHERE ID = " . $row['ID'];
			$ret = mysql_query($sql,$this->connection) or die(mysql_error());
		}
		
		$this->close_db();
		
		return $ret;
	}
	
	public function set_owner($inserted = null,$new = false) {
		
		$this->open_db();

		if($new == true) {
			
			$result = mysql_query('SELECT MIN(id) AS min, MAX(id) AS max FROM sys_users',$this->connection); 
			$row = mysql_fetch_assoc($result);
			$sql = "UPDATE clients SET Owner = ".$row['max']." WHERE ID = " . $inserted['user-own'];
			$ret = mysql_query($sql,$this->connection) or die(mysql_error());
		
			$this->close_db();
		
			return $ret;
			
		}
		
		$sql = "SELECT * FROM clients WHERE Owner = " . $inserted['id'];

		$ret = mysql_query($sql,$this->connection) or die(mysql_error());
		
		$row = mysql_fetch_assoc($ret);
		
		if($row != false) {
			$sql = "UPDATE clients SET Owner = NULL WHERE ID = " . $row['ID'];
			$ret = mysql_query($sql,$this->connection) or die(mysql_error());
		}
		
		$sql = "UPDATE clients SET Owner = ".$inserted['id']." WHERE ID = " .$inserted['user-own'];
		
		$ret = mysql_query($sql,$this->connection) or die(mysql_error());
		
		$this->close_db();
		
		return $ret;
	}
	
	public function get_owner($user_id = NULL) {
		$this->open_db();
		
		$sql = "SELECT * FROM clients WHERE owner = " . $user_id;
		//var_dump($sql);
		$ret = mysql_query($sql,$this->connection) or die(mysql_error());
		
		$ret = mysql_fetch_assoc($ret);
		//var_dump($ret);
		
		$this->close_db();
		
		return $ret;
	}
	
	public function get_client($id = NULL) {

		if($id == NULL){
			//return false;
		}
		$this->open_db();
		
		$sql = "SELECT * FROM clients WHERE ID = " . $id;
		//var_dump($sql);
		$ret = mysql_query($sql,$this->connection) or die(mysql_error());
		
		$ret = mysql_fetch_assoc($ret);
		//var_dump($ret);
		
		$this->close_db();
		
		return $ret;
	}
	
	public function add_client($inserted = NULL) {
		
		if($inserted === NULL) {
			return false;
		}
		
		$this->open_db();
		
		if(isset($inserted['client-active'])) {
			$active = 1;
		} else {
			$active = 0;
		}
		
		if(isset($inserted['partner_group_id']) && $inserted['partner_group_id'] != '') {
			$inserted['parcel-user'] = $inserted['partner_group_id'];
		} else {
			$inserted['parcel-user'] = 0;
		}
		
		
		$sql = "INSERT INTO clients ( Name, Prefix, Comment, Parcel_user, Active, DateOfAdd) VALUES ( '".$inserted['client-name']."', '".$inserted['client-prefix']."', '".$inserted['client-comment']."', ".$inserted['parcel-user'].", ".$active.", now())";
		
		$ret =  mysql_query($sql,$this->connection) or die (mysql_error());
		
		$client_id = mysql_insert_id();
		
		$this->close_db();
		
		if($ret == true && isset($inserted['import-client'])) {
			
			foreach($inserted['import-client'] as $key=>$row){

				$partner = $this->parent->get_component('parcel')->load_partner($key);

				$subclient['subclient-name'] = $partner['partner_name'];
				$subclient['subclient-prefix'] = $partner['partner_code'];
				$subclient['subclient-zipcode'] = $partner['partner_zipcode'];
				$subclient['subclient-city'] = $partner['partner_city'];
				$subclient['subclient-address'] = $partner['partner_address'];
				$subclient['subclient-phonenum'] = $partner['partner_telephone'];
				$subclient['subclient-email'] = $partner['partner_email'];
				$subclient['subclient-comment'] = 'Parcelből importálva :' . date("Y.m.d H:i:s",time());
				$subclient['subclient-clientid'] = $client_id;
				$subclient['subclient-active'] = '1';
				
				$this->parent->get_component('subclients')->subclient_add($subclient,true);
				
			}
		}
	
		return $ret;
		
	}
	
	public function update_client($inserted = NULL) {
		//var_dump($inserted);exit;
		if($inserted != NULL && is_array($inserted)) {
			$this->open_db();
			
		
			if($inserted['client-active'] == 'on') {
				$inserted['client-active'] = 1;
			} else {
				$inserted['client-active'] = 0;
			}
			
			if(isset($inserted['client-default']) && $inserted['client-default'] == 'on') {
				$inserted['client-default'] = 1;
			} else {
				$inserted['client-default'] = 0;
			}
			
			$sql = "UPDATE clients SET Name = '".$inserted['client-name']."', Prefix = '".$inserted['client-prefix']."', Comment = '".$inserted['client-comment']."', Active = ".$inserted['client-active'].", Default_Client = ".$inserted['client-default']." WHERE ID = " . $inserted['id'];
			
			//var_dump($sql);exit;
			
			$ret =  mysql_query($sql,$this->connection) or die (mysql_error());
			
			$this->close_db();
			
			return $ret;
			
		}
		
	}
	
	public function delete_client($inserted = NULL) {
		
		$this->open_db();
		
		$sql = "DELETE FROM clients WHERE ID = ". $inserted['id'];
		
		$ret =  mysql_query($sql,$this->connection) or die (mysql_error());
		
		$this->close_db();

		if($ret == true) {
			// Almegbízók törlése

			$this->parent->get_component('subclients')->delete_subclients($inserted['id']);
		}
		
		return $ret;
		
	}
	
	public function kill_default($id = NULL) {
		
		$this->open_db();
		
		$sql = "UPDATE clients SET Default_Client = 0 WHERE ID = " .$id;
		
		$ret =  mysql_query($sql,$this->connection) or die (mysql_error());
		
		$this->close_db();
		
		return $ret;
	}
	
	public function set_active($id = NULL,$value = NULL) {
		if($id == NULL || $value == NULL){
			return false;
		}
		$this->open_db();
		
		$sql = "UPDATE clients SET Active = " . $value . " WHERE ID = " . $id;

		$ret = mysql_query($sql,$this->connection) or die (mysql_error());
		//var_dump($sql);
		$this->close_db();
		
		if($ret == NULL) {
			return false;
		} else {
			return true;
		}
	}
	
	public function list_clients($filter = NULL) {
		
		$this->open_db();
		
		$sql = "SELECT * FROM clients";
		
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
			$sql = "SELECT * FROM clients WHERE " . $fieldname . " = '" . $value . "'";
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
		
		$sql = "SELECT clients.Locked FROM clients WHERE ID = " . $id;
		
		$user = mysql_fetch_assoc(mysql_query($sql,$this->connection)) or die(mysql_error());
		
		if($user['Locked'] == $_SESSION["HDT_uid"]) {
		
			$sql = "UPDATE clients SET Locked = 0";
			
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
			
			$sql = "UPDATE clients SET Locked = " . $_SESSION['HDT_uid'] . " WHERE ID = ". $id;
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
		
		$sql = "SELECT clients.Locked FROM clients WHERE ID = " . $id;
		
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