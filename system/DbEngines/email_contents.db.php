<?php

error_reporting(E_ALL ^ E_DEPRECATED);

class Email_contentsDB {
	
	private $parent;
	
	public $connection;
	
	public function __construct($parent = NULL) {
			
			$this->parent = $parent;
			
	}
	
	private function open_db() {
		$this->connection = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die(mysql_error());
		mysql_set_charset('utf8',$this->connection);
		mysql_select_db(DB_NAME, $this->connection) or die(mysql_error());
		
		
		
		//$this->connection = $this->parent->connection;
		//var_dump($this->parent->connection);exit;
		//var_dump($this->connection);exit;
		
		$this->parcel_db = new ParcelDB();
		
		
	}
	
	private function close_db() {
		//mysql_close($this->connection);
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
	
	public function load_content($hook = null) {
	
		if($hook== NULL){
			return false;
		}
		$this->open_db();
		
		$sql = "SELECT * FROM email_contents WHERE Hook = '" . $hook . "'";
		
		$ret = mysql_fetch_assoc(mysql_query($sql,$this->connection)) or die (mysql_error());
		
		$this->close_db();
		
		return $ret;
	
	}
	
	public function get_email_contents($id = NULL) {
		
		if($id == NULL){
			return false;
		}
		$this->open_db();
		
		$sql = "SELECT * FROM email_contents WHERE ID = " . $id;
		
		$ret = mysql_fetch_assoc(mysql_query($sql,$this->connection)) or die (mysql_error());
		
		$this->close_db();
		
		return $ret;
	
	}
	
	public function update_email_contents($inserted = null) {
		
		if($inserted != NULL && is_array($inserted)) {
			
			$this->open_db();
			
			$sql = "UPDATE email_contents SET Label = '".$inserted['email_contents-label']."', Hook = '".$inserted['email_contents-hook']."', Content = '".$inserted['email_contents-content']."' WHERE ID = " . $inserted['id'];
			
			//var_dump($sql);exit;
			
			$ret =  mysql_query($sql,$this->connection) or die (mysql_error());
			
			$this->close_db();
			
			return $ret;
			
		}
		
	}
	
	public function list_email_contents($filter = NULL) {
		
		$this->open_db();
		
		$sql = "SELECT * FROM email_contents";
		
		if($filter != NULL) {

			$sql .= $this->build_where($filter);
		}
		
		$result = mysql_query($sql,$this->connection) or die(mysql_error());

		$this->close_db();
		
		return $result;
		
	}
	
	public function add_email_contents($inserted = NULL) {
	
		//var_dump($inserted);
		
		//exit;
		
		$this->open_db();
		
		$sql = "INSERT INTO email_contents ( Label, Hook, Content ) VALUE ( '".$inserted['email_contents-label']."', '".$inserted['email_contents-hook']."', '".$inserted['email_contents-content']."')";
		
		$ret =  mysql_query($sql,$this->connection) or die (mysql_error());
		
		$this->close_db();
		
		return $ret;
		
		//exit;
	
	}
	
	public function delete_email_contents($inserted = NULL) {
		
		$this->open_db();
		
		$sql = "DELETE FROM email_contents WHERE ID = " . $inserted['id'];
		
		$ret =  mysql_query($sql,$this->connection) or die (mysql_error());
		
		$this->close_db();
		
		return $ret;
		
	}
	
}

?>
