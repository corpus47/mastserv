<?php

error_reporting(E_ALL ^ E_DEPRECATED);

require_once('parcel.db.php');


class MandatesDB {

	public $connection;
	private $parcel_db;
	private $subcontactor_db;
	
	private $parent;
	
	public function __construct($parent) {
		
		$this->parent = $parent;
		//$this->connection = $parent->connection;
		   
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
	
	public function save_comments($inserted = null) {
	
		if($inserted == NULL) {
			return false;
		}
		
		$this->open_db();
		
		$sql = "UPDATE mandates SET master_comment = '".$inserted['mast_comment']."', customer_comment = '".$inserted['cust_comment']."' WHERE ID = ". $inserted['id'];
		
		$ret = mysql_query($sql,$this->connection) or die (mysql_error());
		
		$this->close_db();
		
		return $ret;
		
	}
	
	public function get_mandate($id) {
		
		if($id == NULL){
			return false;
		}
		
		$this->open_db();
		
		$sql = "SELECT * FROM mandates WHERE ID = ". $id;
		
		$ret = mysql_fetch_assoc(mysql_query($sql,$this->connection)) or die (mysql_error());
		
		$this->close_db();
		
		return $ret;
		
	}
	
	public function get_details($id = NULL) {
		
		if($id != NULL) {
			
			$this->open_db();
			
			$sql = "SELECT * FROM mandates_details WHERE MandateID = ". $id;
			
			$ret = mysql_query($sql,$this->connection) or die (mysql_error());
			
			$this->close_db();
			
			if($ret != false) {
			
				$return = array();
				while($row = mysql_fetch_assoc($ret)){
					$return[] = $row;
				}
				
				return $return;
			}
			
		}
		
	}
	
	public function add_mandate_detail($mandate_id = NULL, $product_name = NULL, $installation_id = NULL) {
	
		$this->open_db();
		
		$sql = "INSERT INTO mandates_details ( MandateID, ProductName, InstallationID ) VALUE ( ".$mandate_id.", '".$product_name."', ".$installation_id.") ";
		
		$ret =  mysql_query($sql,$this->connection) or die (mysql_error());
		
		$this->close_db();
	
	}
	
	public function add_mandate($inserted = NULL,$files = NULL) {
		
		
		//var_dump($_FILES);
		//var_dump($inserted);
		//var_dump($files);
		//var_export($inserted['installation']);
		//exit;
		
		if($inserted != NULL && is_array($inserted)) {
			
			$this->open_db();
			
			$installations = array();
			
			/*foreach($inserted['mandate-installations'] as $key=>$item) {
				$installations[] = $key;
			}*/
			
			$inserted['mandate-product-name'] = array();
			
			$installations = $inserted['installation'];
			
			if(!isset($inserted['hdt_order_id'])) {
				$sql = "INSERT INTO mandates (  PartnerID, CustomerName, CustomerZipcode, CustomerCity, CustomerAddress, CustomerPhone, CustomerEmail, Mandate_products, Mandate_installations, Kiszallitas, HDT_status, Master_status, SubcontactorID, MasterID ) VALUE ( " . $inserted['mandate-partner-id'].", '" . $inserted['mandate-customer-name'] . "', '" . $inserted['mandate-customer-zipcode'] . "', '" . $inserted['mandate-customer-city'] . "', '" . $inserted['mandate-customer-address'] . "', '" . $inserted['mandate-customer-phonenum'] . "', '".$inserted['mandate-customer-email']."', '" . serialize($inserted['mandate-product-name']) . "', '" . serialize($installations) . "', '" . $inserted['mandate-kiszallitas'] . "', NULL, 0, ".$inserted['SubcontactorID'].", NULL)";
			} else {
			$sql = "INSERT INTO mandates (  HDT_order_id, PartnerID, CustomerName, CustomerZipcode, CustomerCity, CustomerAddress, CustomerPhone, CustomerEmail, Mandate_products, Mandate_installations, Kiszallitas, HDT_status, Master_status, SubcontactorID, MasterID ) VALUE ( ".$inserted['hdt_order_id'].", " . $inserted['mandate-hdt-partner-id'].", '" . $inserted['mandate-customer-name'] . "', '" . $inserted['mandate-customer-zipcode'] . "', '" . $inserted['mandate-customer-city'] . "', '" . $inserted['mandate-customer-address'] . "', '" . $inserted['mandate-customer-phonenum'] . "', '". $inserted['mandate-customer-email']."', '" . serialize($inserted['mandate-product-name']) . "', '" . serialize($installations) . "', '" . $inserted['mandate-kiszallitas'] . "', NULL, 0, ".$inserted['SubcontactorID'].", NULL)";
			}
			
			$ret =  mysql_query($sql,$this->connection) or die (mysql_error());
			
			$id = mysql_insert_id($this->connection);
			
			$this->close_db();
			
			// Create serial
			
			//$partner = $this->parcel_db->get_partner($inserted['mandate-partner-id']);
			$partner = $this->parent->get_component('subclients')->load_subclient($inserted['mandate-partner-id']);
			
			//$client = $this->parcel_db->get_client($partner['partner_group_id']);
			$client = $this->parent->get_component('clients')->load_client($partner['ClientID']);
			//var_dump($client);exit;
			$this->open_db();
			
			//var_dump($partner);exit;
			
			//$serial = "MS/".$client['partner_group_prefix']."/".$partner['partner_code']."/".date("Ymd",time())."/".$id;
			$serial = "MS/".$client['Prefix']."/".$partner['Prefix']."/".date("Ymd",time())."/".$id;
			
			//$serial = "MS/".date("Ymd",time())."/".$id;
			
			$sql = "UPDATE mandates SET Mandate_serial = '".$serial."' WHERE ID = ".$id;
			
			//var_dump($sql);exit;
			$ret =  mysql_query($sql,$this->connection) or die (mysql_error());
			
			//var_dump($ret);exit;
			
			$this->close_db();
			
			$this->parent->get_component('mandate_tracking')->add_track($id);
			
			$this->parent->get_component('sms')->new_mandate_sms($id);
			
			$inserted['id'] = $id;

			$this->parent->get_component('email')->mandate_add_email($inserted);
			
			//var_dump($files);exit;
			
			if($files['fileToUpload']['name'] != "") {
				//var_dump($files);exit;
				$this->parent->get_component('uploadfiles')->addmandate_upload($files,$inserted);
			}
			
			/*foreach($inserted['mandate-product-name'] as $key=>$name){
				
				$this->add_mandate_detail($id, $name, $inserted['mandate-installations'][$key]);
			}*/
	
		}
		
		return $ret;
		
	}
	
	public function update_mandate($inserted = NULL) {
		//var_dump($inserted['installation']);exit;
		if($inserted != NULL && is_array($inserted)) {
		
			$this->open_db();
			
			$installations = array();
			
			/*foreach($inserted['mandate-installations'] as $key=>$item) {
				$installations[] = $key;
			}*/
			
			$inserted['mandate-product-name'] = array();
				
			$installations = $inserted['installation'];
			
			if($inserted['subcontactor-id'] == "") {
				$inserted['subcontactor-id'] = 'NULL';
			}
			
			if($inserted['master-status'] == "") {
				$inserted['master-status'] = 'NULL';
			}
			
			if($inserted['master-id'] != NULL && $inserted['master-id'] != 0) {
				$inserted['master-status'] = MANDATE_GET_MASTER;
			}
			
			if(isset($inserted['kiszallas-date']) && $inserted['kiszallas-date'] == "") {
				$kiszallas_date = "Kiszallas_Date = NULL";
			} else {
				$kiszallas_date = "Kiszallas_Date = '".$inserted['kiszallas-date']."'";
			}

			/*$sql = "UPDATE mandates SET CustomerName = '".$inserted['mandate-customer-name']."', CustomerZipcode = '".$inserted['mandate-customer-zipcode']."', CustomerCity = '".$inserted['mandate-customer-city']."', CustomerAddress = '".$inserted['mandate-customer-address']."', CustomerPhone = '".$inserted['mandate-customer-phonenum']."', CustomerEmail = '".$inserted['mandate-customer-email']."', Mandate_products = '".serialize($inserted['mandate-product-name'])."', Mandate_installations = '".serialize($installations)."', Kiszallitas = '".$inserted['mandate-kiszallitas']."', Kiszallas_Date = '".$inserted['kiszallas-date']."', HDT_status = NULL, Master_status = ".$inserted['master-status'].", SubcontactorID = ".$inserted['subcontactor-id'].", MasterID = ".$inserted['master-id']." WHERE ID = " . $inserted['id'];*/
			
			$sql = "UPDATE mandates SET CustomerName = '".$inserted['mandate-customer-name']."', CustomerZipcode = '".$inserted['mandate-customer-zipcode']."', CustomerCity = '".$inserted['mandate-customer-city']."', CustomerAddress = '".$inserted['mandate-customer-address']."', CustomerPhone = '".$inserted['mandate-customer-phonenum']."', CustomerEmail = '".$inserted['mandate-customer-email']."', Mandate_products = '".serialize($inserted['mandate-product-name'])."', Mandate_installations = '".serialize($installations)."', Kiszallitas = '".$inserted['mandate-kiszallitas']."', ".$kiszallas_date.", HDT_status = NULL, Master_status = ".$inserted['master-status'].", SubcontactorID = ".$inserted['subcontactor-id'].", MasterID = ".$inserted['master-id']." WHERE ID = " . $inserted['id'];
			
			//var_dump($inserted['mandate-product-name']);
			//var_dump($inserted);

			var_dump($sql);

			$ret = mysql_query($sql,$this->connection) or die (mysql_error());
			
			$this->close_db();
			
			return $ret;
		}
	}
	
	public function update_mandate_status($id = NULL, $status = NULL) {
		
		if($id != NULL && $status != NULL) {
			
			$this->open_db();
			
			$sql = "UPDATE mandates SET Master_status = " . $status . " WHERE ID = " . $id;
			
			$ret = mysql_query($sql,$this->connection) or die (mysql_error());
			
			$this->close_db();
			
			return $ret;
			
		}
		
	}
	
	public function unlock($id = NULL) {
		// Felszabadítás
		if($id == NULL) {
			return;
		}
		
		$this->open_db();
		
		$sql = "SELECT mandates.Locked FROM mandates WHERE ID = " . $id;
		
		$user = mysql_fetch_assoc(mysql_query($sql,$this->connection)) or die(mysql_error());
		
		if($user['Locked'] == $_SESSION["HDT_uid"]) {
		
			$sql = "UPDATE mandates SET Locked = 0";
			
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
			
			$sql = "UPDATE mandates SET Locked = " . $_SESSION['HDT_uid'] . " WHERE ID = ". $id;
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
		
		$sql = "SELECT mandates.Locked FROM mandates WHERE ID = " . $id;
		
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
	
	public function list_mandates($filter = NULL) {
	
		$this->open_db();
		
		$sql = "SELECT * FROM mandates";
		
		if($filter != NULL) {

			$sql .= $this->build_where($filter);
		}
		
		//$sql .= " LIMIT 20";
		
		//var_export($sql);
		
		$result = mysql_query($sql,$this->connection) or die(mysql_error());

		$this->close_db();
		
		return $result;
	}
	
	public function confirm($post) {
	
		$this->open_db();
		
		$sql = "UPDATE mandates SET MasterID = " . $post['master_id'] . ", Master_status = " . $post['status'] . ", Kiszallas_Date = '".$post['datum']."' WHERE ID = " . $post['id'];
		
		$result = mysql_query($sql,$this->connection);
		
		$this->close_db();
		
		return $result;
	
	}
	
	public function unconfirm($post) {
		
		$this->open_db();
		
		$sql = "UPDATE mandates SET MasterID = NULL, Master_status = NULL, Kiszallas_Date = NULL WHERE ID = " . $post['id'];
		
		$result = mysql_query($sql,$this->connection);
		
		$this->close_db();
		
		return $result;
	
	}
	
	private function if_ekezetes($string = null) {
		
		$ekezetes = array("Á","É","Í","Ő","Ö","Ű","Ú","Ó");
		
		$string = mb_strtoupper($string,'UTF-8');
		
		$ret = false;
		
		foreach($ekezetes as $ekezet) {
			if(strpos($string,$ekezet) !== false) {
				$ret = true;
			}
		}
		return $ret;
	}
	
	public function searched_sql($search = NULL) {
		
		$sql = "SHOW COLUMNS FROM mandates";
		
		$sql_search = "";
		
		
		
		$rs = mysql_query($sql);
			while($r = mysql_fetch_array($rs)){
				$colum = $r[0];
				$search = preg_replace('/(\d)\./i','${1}-',$search);
				if($this->if_ekezetes($search) == false) {
					if(strpos($r[1],'varchar') !== false || strpos($r[1],'date') !== false):
						mb_internal_encoding('UTF-8');
						$sql_search_fields[] = $colum." LIKE '%".mb_strtoupper($search,'UTF-8')."%'";
						//$sql_search_fields[] = $colum." LIKE '%".mb_strtoupper($search)."%'";
					endif;
				} else {
					if(strpos($r[1],'varchar') !== false):
						mb_internal_encoding('UTF-8');
						$sql_search_fields[] = $colum." LIKE '%".mb_strtoupper($search,'UTF-8')."%'";
						//$sql_search_fields[] = $colum." LIKE '%".mb_strtoupper($search)."%'";
					endif;
				}
				
			}

		$sql_search .= implode(" OR ", $sql_search_fields);
		
		return $sql_search;
		
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
	
	public function has_arrived($post) {
		
		$this->open_db();
		
		$sql = "UPDATE mandates SET Kiszallitva = 1 WHERE HDT_order_id = " . $post['order_id'];
		
		$result = mysql_query($sql,$this->connection);
		
		//var_dump($sql);exit;
		
		$this->close_db();
		
		return $result;
		
	}
		
	public function has_storno($post) {
		
		$this->open_db();
		
		$sql = "UPDATE mandates SET is_parcel_storno = 1 WHERE HDT_order_id = " . $post['order_id'];
		
		$result = mysql_query($sql,$this->connection);
		
		//var_dump($sql);exit;
		
		$this->close_db();
		
		return $result;
		
	}
		
}
