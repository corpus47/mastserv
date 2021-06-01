<?php

require_once('DbEngines/clientoptions.db.php');
require_once('DbEngines/pg_installation.db.php');

class clientoptions {
	
	private $clientoptions_db;
	
	private $parent;
	
	public function __construct($parent = NULL){
		
		$this->parent = $parent;
		
		$this->clientoptions_db = new ClientoptionsDB();

	}
	
	public function clientoptions_add($inserted) {
		$ret = $this->clientoptions_db->add_clientoptions($inserted);
		if($ret == false) {
			$_SESSION['HDT_error_message'] = "Sikertelen ügyfél opciók felvitel!";
			return false;	
		} else {
			$_SESSION['HDT_ok_message'] = "Sikeres ügyfél opciók felvitel!";
			return true;
		}
	}
	
	public function subclientoptions_update($inserted) {
		
		$ret = $this->clientoptions_db->update_subclientoptions($inserted);
		if($ret == false) {
			$_SESSION['HDT_error_message'] = "Sikertelen ügyfél opciók frissítés!";
			return false;	
		} else {
			$_SESSION['HDT_ok_message'] = "Sikeres ügyfél opciók frissítés!";
			return true;
		}
		
	}
	
	public function clientoptions_update($inserted) {
		
		$ret = $this->clientoptions_db->update_clientoptions($inserted);
		if($ret == false) {
			$_SESSION['HDT_error_message'] = "Sikertelen ügyfél opciók frissítés!";
			return false;	
		} else {
			$_SESSION['HDT_ok_message'] = "Sikeres ügyfél opciók frissítés!";
			return true;
		}
		
	}
	
	public function get_option($installation_id = NULL, $client_id = NULL, $to = NULL, $prod_val = NULL, $prod_piece = 1) {
		
		$filter = array("Subclient_ID = " . $client_id, "installation_id = " . $installation_id);

		$res = $this->clientoptions_db->get_option($filter);
		
		$subclient = $this->parent->get_component('subclients')->load_subclient($client_id);
		
		$from = $subclient['Zipcode'].' '.$subclient['City'].' '.$subclient['Address'];
		
		//$to = '4761 Porcsalma Petőfi út 6';
		
		$google = $this->parent->get_component('google');
		
		$data = $google->get_distance($from,$to);
		
		$dist = 0;
		
		foreach($data->rows[0]->elements as $road) {
		    //var_dump($road->distance->value);
		    $dist = $road->distance->value;
		}
		
		$dist = round($dist/1000,0);
		
		//var_dump($dist);
		
		
		while($row = mysql_fetch_assoc($res)) {
			
			$inst = $row;
			
			$option = $this->parent->get_component('mandates_options')->load_mandates_option($row['mandates_option_id']);
			if($dist <= (int)$option['Distance']) {
				break; 
			}
		}
		//var_dump($inst);
		
		if($inst['percent'] == 1){
			$ret = round($prod_val/100)*$inst['value'];
		} else {
			$ret = $inst['value'];
		}
		
		return $ret * $prod_piece;
	}
	
}
