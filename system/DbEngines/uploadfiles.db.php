<?php

error_reporting(E_ALL ^ E_DEPRECATED);

class UploadfilesDB {
	
	private $parent;
	
	private $connection;
	
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
	
	public function list_files($filter = null) {
		$this->open_db();
		
		$sql = "SELECT * FROM upload_files";
		
		if($filter != NULL) {

			$sql .= $this->build_where($filter);
		}
		
		//$sql .= " LIMIT 20";
		
		//var_export($sql);
		
		$result = mysql_query($sql,$this->connection) or die(mysql_error());

		$this->close_db();
		
		return $result;
	}
	
	public function delete_file($id = NULL) {
		
		$this->open_db();
		
		$sql = "DELETE FROM upload_files WHERE ID = " . $id;
		
		$result = mysql_query($sql,$this->connection) or die(mysql_error());

		$this->close_db();
		
		return $result;
		
	}
	
	public function get_file($id = NULL) {
	
		$this->open_db();
		
		$sql = "SELECT * FROM upload_files WHERE ID = " . $id;
		
		$result = mysql_query($sql,$this->connection) or die(mysql_error());

		$this->close_db();
		
		return $result;
	
	}
	
	public function add_file($file_name = null,$mandate_id = null,$type = null) {
		
		$date = date('Y-m-d',time());
		
		$this->open_db();
		
		$sql = "INSERT INTO upload_files ( MandateID, FileName, AttachmentType, UploadDate, UploadUser ) VALUE ( ".$mandate_id.", '".$file_name."', ".$type.", '".$date."', ".$_SESSION['HDT_uid'].")";
		
		$ret = mysql_query($sql,$this->connection) or die (mysql_error());
		
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
