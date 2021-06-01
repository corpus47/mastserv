<?php
require_once('userlevels.inc.php');

require_once('user.component.php');

require_once('subcontactor.component.php');

require_once('master.component.php');

require_once('parcel.component.php');

require_once('order.component.php');

require_once('cities.component.php');

require_once('installations.component.php');

require_once('mandates.component.php');

require_once('google.component.php');

require_once('DbEngines/sys.db.php');

define('MODULE_NAME','webservice');

class webservice {

	private $logged = false;
	
	private $act;
	
	private $google;
	
	public function __construct() {
	
		$this->sys_db = new SysDB(); 
		
		$this->user = new user();
		
		$this->parcel = new parcel();
		
		$this->subcontactor = new subcontactor($this);
		
		$this->master = new master();
		
		$this->order = new order();
		
		$this->cities = new cities();
		
		$this->installations = new installations();
		
		$this->mandates = new mandates($this);
		
		$this->google = new Google_tools();
		
		
		
		if(isset($_POST['username']) && isset($_POST['password'])) {
			
			$this->logged = $this->user->login($_POST['username'],$_POST['password'],true);
					
			if($this->logged == false) {
				header('Content-type: application/json');
		
				$ret['status'] = 'error';
				$ret['message'] = 'Invalid username or password';
			
				header('Content-type: application/json');
				echo json_encode($ret);
			
				exit;
			}
			
		} else {
		
			header('Content-type: application/json');
		
			$ret['status'] = 'error';
			$ret['message'] = 'No username or password';
			
			header('Content-type: application/json');
			echo json_encode($ret);
			
			exit;
		}
		
		if(isset($_POST['act'])) {
			$this->act = $_POST['act'];
		}
		
	}
	
	public function view() {
	
		$action = $this->act;
		
		if(method_exists($this,$action)){
			$this->$action();
		}
		
		exit;
		
	}
	
	public function get_component($name = NULL) {
		if($name != NULL) {
			if(isset($this->$name)) {
				return $this->$name;
			} else {
				return "Nincs ilyen : " . $name;
			}
		}
	}
	
	private function installations_select() {
		header('Content-type: text/'.$_POST['type'].'; charset=utf-8');
		//echo $this->installations->installations_select($_POST);
		echo $this->installations->ws_installations($_POST);
	}
	
	private function add_mandate() {

		header('Content-type: text/html');
		//var_dump($_POST);
		echo $this->mandates->mandate_add($_POST);
	}
	
	private function parcel_arrived() {
		header('Content-type: text/html');
		echo $this->mandates->delivery_has_arrived($_POST);
	}
	
	private function parcel_storno() {
		header('Content-type: text/html');
		echo $this->mandates->delivery_has_storno($_POST);
	}
}
?>
