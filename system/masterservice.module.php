<?php
require_once('userlevels.inc.php');

require_once('user.component.php');

require_once('profil.component.php');

require_once('subcontactor.component.php');

require_once('master.component.php');

require_once('parcel.component.php');

require_once('order.component.php');

require_once('cities.component.php');

require_once('installations.component.php');

require_once('mandates.component.php');

require_once('clientoptions.component.php');

require_once('mandates_options.component.php');

require_once('pg_installation.component.php');

require_once('statuses.component.php');

require_once('google.component.php');

require_once('qrcode.component.php');

require_once('pdf.component.php');

require_once('mail.component.php');

require_once('statushistory.component.php');

require_once('clients.component.php');

require_once('subclients.component.php');

require_once('sms.component.php');

require_once('mandate_tracking.component.php');

require_once('days.component.php');

require_once('uploadfiles.component.php');

require_once('email_contents.component.php');

require_once('user_logged.component.php');

require_once('reports.component.php');

require_once('DbEngines/sys.db.php');

require_once('tools/szam_tostring/szam_tostring.php');

require_once('tools/CompactMandateList/compactmandatelist.php');

define('MODULE_NAME','masterservice');

class masterservice {
	
	public $params;
	
	public $action;
	
	public $connection;
	
	public $parcel_connection;
	
	private $is_login = false;
	
	private $is_master = false;
	
	private $user;
	
	private $profil;
	
	private $subcontactor;
	
	private $master;
	
	private $parcel;
	
	private $order;
	
	private $cities;
	
	private $installations;
	
	private $mandates;
	
	private $clientoptions;
	
	private $mandates_options;
	
	private $pg_installation;
	
	private $statuses;
	
	private $google;
	
	private $qrcode;
	
	private $pdf;
	
	private $email;
	
	private $statushistory;
	
	private $clients;
	
	private $subclients;
	
	private $sms;
	
	private $mandate_tracking;
	
	private $days;
	
	private $uploadfiles;
	
	private $email_contents;
	
	private $user_logged;
	
	private $reports;
	
	private $sys_db;
	
	private $lang = array();
	
	private $ajax_url;
	
	private $post;
	
	public $converter;
	
	private $compactmandatelist;
	
	public function __construct($params = NULL,$ajaxcall = NULL) {
		
		$this->open_db();
		
		$this->ajax_url = 'http://' . ROOT_URL . '/system/' . MODULE_NAME . '.ajax.php';
		
		if($params != NULL) {
			$this->params = $params;
		}
		
		$this->sys_db = new SysDB(); 
		
		$this->user = new user($this);
		
		$this->profil = new profil($this);
		
		$this->parcel = new parcel($this);
		
		$this->subcontactor = new subcontactor($this);
		
		$this->master = new master($this);
		
		$this->order = new order();
		
		$this->cities = new cities();
		
		$this->installations = new installations($this);
		
		$this->mandates = new mandates($this);
		
		$this->clientoptions = new clientoptions($this);
		
		$this->mandates_options = new mandates_options($this);
		
		$this->pg_installation = new pg_installation();
		
		$this->statuses = new statuses();
		
		$this->google = new Google_tools();
		
		$this->qrcode = new Qrcode_tool($this);
		
		$this->pdf = new Pdf_tool($this);
		
		$this->email = new Email($this);
		
		$this->statushistory = new statushistory($this);
		
		$this->clients = new clients($this);
		
		$this->subclients = new subclients($this);
		
		$this->sms = new Sms($this);
		
		$this->mandate_tracking = new mandate_tracking($this);
		
		$this->days = new days($this);
		
		$this->uploadfiles = new uploadfiles($this);
		
		$this->email_contents = new email_contents($this);
		
		$this->user_logged = new user_logged($this);
		
		$this->reports = new reports($this);
		
		$this->converter = new Azaz();
		
		$this->compactmandatelist = new CompactMandateList($this);
		
		/*if(isset($_POST['username']) && isset($_POST['password'])) {
			$user_id = $this->user->login($_POST['username'],$_POST['password']);

			if($user_id !== false){
				$_SESSION['HDT_uid'] = $user_id;
				header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				exit;
			}
		}*/
		
		// A sessionban benne van az uid
		
		/*if(isset($_SESSION['uid'])) {
			$id = $this->parcel->login('','',$_SESSION['uid'],true);
			if($id != false){
				$_SESSION['HDT_uid'] = $id;
				//header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				//exit;
			}
		}*/
		
		// Éles parcel a sessionben r_user_id
		
		/*if(isset($_SESSION['r_user_id'])) {
			$id = $this->parcel->login('','',$_SESSION['r_user_id'],true);
			if($id != false){
				$_SESSION['HDT_uid'] = $id;
				//header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				//exit;
			}
		}*/
		
		/*if(isset($_POST['username']) && isset($_POST['password'])) {
			$id = $this->parcel->login($_POST['username'],$_POST['password']);
			if($id != false){
				$_SESSION['HDT_uid'] = $id;
				header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				exit;
			}
		}*/
		
		// Tracking login
		
		if(isset($_POST['username']) && isset($_POST['password'])) {
			//var_dump($_SESSION);
			//var_dump($_POST);exit;
			if((int)$_POST['login-type'] === 0) {
				$id = $this->user->login($_POST['username'],$_POST['password']);
				//var_dump($id);exit;
				if($id != false) {
				
					// Mesterszerviz felhasználó belépés
					
					if(isset($_SESSION['HDT_parcel_user'])) {
						unset($_SESSION['parcel_user']);
					}
					
					$_SESSION['HDT_uid'] = $id;
					
					header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
					exit;
				}
			} elseif((int)$_POST['login-type'] === 1) {
				
				if(isset($_SESSION['HDT_parcel_user'])) {
					unset($_SESSION['HDT_parcel_user']);
				}
				
				$id = $this->parcel->login($_POST['username'],$_POST['password']);
				//var_dump($_SESSION);
				//var_dump($id);exit;
				
				if($id != false) {
				
					// HDT felhasználó belépés
					
					$_SESSION['HDT_uid'] = $id;
					
					$_SESSION['HDT_parcel_user'] = $id;
					
					header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
					exit;
				}
				
			} elseif((int)$_POST['login-type'] === 2) {
			
				$id = $this->master->login($_POST['username'],$_POST['password']);
				//echo 'Mester';exit;
				if($id != false) {
				
					// Mester belépés
					
					if(isset($_SESSION['HDT_parcel_user'])) {
						unset($_SESSION['HDT_parcel_user']);
					}
					
					$_SESSION['HDT_uid'] = $id;
					
					$_SERVER['REQUEST_URI'] .= "&act=master";
					
					header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
					exit;
				}
			}
		}
		
		if(!isset($_SESSION['HDT_uid'])) {
			$this->action = 'login';
			$this->is_login = true;
		} 
		
		
		
		if(isset($_SESSION['HDT_uid']) && !isset($_GET['act']) && $ajaxcall === NULL) {
			//$this->action = 'default';
			if($this->get_component('user')->is_admin($_SESSION['HDT_uid']) || $this->get_component('user')->is_subcon_admin($_SESSION['HDT_uid']) || $this->get_component('user')->is_client_admin($_SESSION['HDT_uid'])){
				$this->action = 'reports';
			} elseif($this->get_component('user')->is_super($_SESSION['HDT_uid'])){
				$this->action = 'debug';
			} else {
				$this->action = 'debug';
			}
			header('Location: http://'.ROOT_URL.'?m=masterservice&act='.$this->action);
			exit;
		} elseif(isset($_SESSION['HDT_uid']) && isset($_GET['act'])) {
			$this->action = $_GET['act'];
		} 
		
		if(isset($_SESSION['HDT_master_user']) && !isset($_GET['act']) && $ajaxcall === NULL) {
			//var_dump($_GET);exit;
			if($_GET['act'] != 'master') {
				$_SERVER['REQUEST_URI'] = str_replace($_GET['act'],'master',$_SERVER['REQUEST_URI']);
				header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				exit;
			} else {
				$this->action = 'master';
				$this->is_master = true;
			}
		}
		// Mester csak a mesterfelületet érhesse el
		if(isset($_SESSION['HDT_master_user']) && isset($_GET['act']) && $_GET['act'] != 'master'){
			$_SERVER['REQUEST_URI'] = str_replace($_GET['act'],'master',$_SERVER['REQUEST_URI']);
			header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			exit;
		}
		
		// Tracking login
		
		if(isset($_GET['act']) && $_GET['act'] == 'mandate_tracking') {
			$this->action = $_GET['act'];
			$_SESSION['HDT_mandate_tracking'] = 0;
		}
		
		// Mentés érkezik, adatok POST-ban
		
		if(isset($_POST['save_db'])) {
			if(isset($_POST['act']) && $_POST['act'] == 'user') {
				if($this->user->user_add($_POST) === true) {
					//$this->action = 'listusers';
					header('Location: ' . $this->create_url('listusers'));
					exit;
				}
			} elseif(isset($_POST['act']) && $_POST['act'] == 'subcontactor') {
				if($this->subcontactor->subcontactor_add($_POST) === true) {
					header('Location: ' . $this->create_url('listsubcontactors'));
					exit;
				}
			} elseif(isset($_POST['act']) && $_POST['act'] == 'user_edit') {
				if($this->user->user_update($_POST) === true) {
					header('Location: ' . $this->create_url('listusers'));
					exit;
				}
			} elseif(isset($_POST['act']) && $_POST['act'] == 'subcontactor_edit') {
				if($this->subcontactor->subcontactor_update($_POST) === true) {
					header('Location: ' . $this->create_url('listsubcontactors'));
					exit;
				}
			} elseif(isset($_POST['act']) && $_POST['act'] == 'master') {
				if($this->master->master_add($_POST) === true) {
					header('Location: ' . $this->create_url('listmasters'));
					exit;
				}
			} elseif(isset($_POST['act']) && $_POST['act'] == 'master_edit') {
				if($this->master->master_update($_POST) === true) {
					header('Location: ' . $this->create_url('listmasters'));
					exit;
				}
			} elseif(isset($_POST['act']) && $_POST['act'] == 'installation') {
				if($this->installations->installation_add($_POST) === true) {
					header('Location: ' . $this->create_url('installations'));
					exit;
				}
			} elseif(isset($_POST['act']) && $_POST['act'] == 'installation_edit') {
				if($this->installations->installation_update($_POST) === true) {
					header('Location: ' . $this->create_url('installations'));
					exit;
				}
			} elseif(isset($_POST['act']) && $_POST['act'] == 'mandate') {
				//var_dump($_FILES);exit;
				if($this->mandates->mandate_add($_POST,$_FILES) === true) {
					header('Location: ' . $this->create_url('listmandates').'&mode=unconfirmed');
					exit;
				}
			} elseif(isset($_POST['act']) && $_POST['act'] == 'mandate_edit') {
				if($this->mandates->mandate_update($_POST) === true) {
					if(isset($_POST['get-mode'])) {
						header('Location: ' . $this->create_url('listmandates&mode='.$_POST['get-mode']));
					} else {
						header('Location: ' . $this->create_url('listmandates'));
					}
					exit;
				}
			} elseif(isset($_POST['act']) && $_POST['act'] == 'mandates_option') {
				if($this->mandates_options->mandates_option_add($_POST) === true) {
					header('Location: ' . $this->create_url('listmandates_options'));
					exit;
				}
			} elseif(isset($_POST['act']) && $_POST['act'] == 'edit_mandates_option') {
				if($this->mandates_options->mandates_option_update($_POST) === true) {
					header('Location: ' . $this->create_url('listmandates_options'));
					exit;
				}
			} elseif(isset($_POST['act']) && $_POST['act'] == 'clientoptions_edit') {
				if($this->clientoptions->clientoptions_update($_POST) === true) {
					header('Location: ' . $this->create_url('listclients'));
					exit;
				}
			} elseif(isset($_POST['act']) && $_POST['act'] == 'client') {
				if($this->clients->client_add($_POST) === true) {
					header('Location: ' . $this->create_url('listclients'));
					exit;
				}
			} elseif(isset($_POST['act']) && $_POST['act'] == 'client_edit') {
				if($this->clients->client_update($_POST) === true) {
					header('Location: ' . $this->create_url('listclients'));
					exit;
				}
			} elseif(isset($_POST['act']) && $_POST['act'] == 'subclient') {
				if($this->subclients->subclient_add($_POST) === true) {
					header('Location: ' . $this->create_url('listsubclients'));
					exit;
				}
			} elseif(isset($_POST['act']) && $_POST['act'] == 'subclient_edit') {
				if($this->subclients->subclient_update($_POST) === true) {
					header('Location: ' . $this->create_url('listsubclients'));
					exit;
				}
			} elseif(isset($_POST['act']) && $_POST['act'] == 'profile_edit') {
				if($this->user->user_update($_POST) === true) {
					//header('Location: ' . $this->create_url('listsubclients'));
					header('Location: http://' . ROOT_URL);
					exit;
				}
			} elseif(isset($_POST['act']) && $_POST['act'] == 'day') {
				if($this->days->day_add($_POST) === true) {
					header('Location: ' . $this->create_url('listdays'));
					exit;
				}
			} elseif(isset($_POST['act']) && $_POST['act'] == 'day_edit') {
				if($this->days->day_update($_POST) === true) {
					header('Location: ' . $this->create_url('listdays'));
					exit;
				}
			} elseif(isset($_POST['act']) && $_POST['act'] == 'email_contents') {
				if($this->email_contents->add_email_contents($_POST) === true) {
					header('Location: ' . $this->create_url('listemail_contents'));
					exit;
				}
			} elseif(isset($_POST['act']) && $_POST['act'] == 'email_contents_edit') {
				if($this->email_contents->update_email_contents($_POST) === true) {
					header('Location: ' . $this->create_url('listemail_contents'));
					exit;
				}
			}
		}
		
		// Language
		
		require_once(str_replace('system','',dirname(__FILE__)).DIRECTORY_SEPARATOR.'languages'.DIRECTORY_SEPARATOR.'hu'.DIRECTORY_SEPARATOR.'default.php');
		
		$this->lang = $lang;
		
		// Ajax hívás
		
		if($ajaxcall === true) {
			$this->post = $_POST;
			if(!isset($_POST['act'])){
				$ret = array('status' => 'error','msg' => 'Hiányzó ajax akció paraméter');
				echo json_encode($ret);
				exit;
			} else {
				$ajax_action = 'ajax_'.$_POST['act'];
				if(method_exists($this,$ajax_action)){
					return $this->$ajax_action();
				} else {
					$ret = array('status' => 'error','msg' => 'Hiányzó ajax methodus');
					echo json_encode($ret);
					exit;
				}
			}
			exit;
		}
		
		// Módosítás érkezik
		
		if(isset($_GET['act']) && $_GET['act'] == 'edituser') {
			if(!isset($_GET['id'])){
				header('Location:' . $this->create_url('listusers'));
				exit;
			}
		}
		if(isset($_GET['act']) && $_GET['act'] == 'editsubcontactor') {
			if(!isset($_GET['id'])){
				header('Location:' . $this->create_url('listsubcontactors'));
				exit;
			}
		}
		
	}
	
	public function get_params() {
		//var_dump($this->params);
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
	
	//Open database
	
	private function open_db() {
		
		$this->connection = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die(mysql_error());
		mysql_set_charset('utf8',$this->connection);
		mysql_select_db(DB_NAME, $this->connection) or die(mysql_error());
		
		//var_dump($this->connection);exit;
		
	}
	
	//Close database
	
	private function close_db(){
		mysql_close($this->connection);
	}
	
	// Ajax funkciók
	
	// Ajax téma változtatás
	private function ajax_changetheme() {
		
		if($_POST['theme'] == 'new') {
			$_SESSION['HDT_theme'] = 'new';
		} else {
			$_SESSION['HDT_theme'] = 'default';
		}
		//$_SESSION['HDT_theme'] = $theme;

		$ret = json_encode(array('status'=>'ok','msg'=>'Theme: '. $_POST['theme'],'http_referer' => $_SERVER["HTTP_REFERER"]));
		
		echo $ret;
		
		exit;
	}
	
	private function ajax_idlelogout() {
		
		$this->user_logged->set_logout();
		
		unset($_SESSION['HDT_uid']);
		
		if(isset($_SESSION['HDT_master_user'])) {
			unset($_SESSION['HDT_master_user']);
		}
		
		$ret = json_encode(array('status' => 'ok'));
		
		echo $ret;
		
		exit;
	}
	
	private function ajax_checkunique_user() {

		$ret = false;
		if(isset($_POST['fieldname']) && isset($_POST['value'])) {
			$ret = $this->user->check_unique($_POST['fieldname'],$_POST['value']);
		}
		if($ret != false) {
			echo json_encode(array('status' => 'false'));
		} else {
			echo json_encode(array('status' => 'true'));
		}
		
		exit;
	}
	
	private function ajax_checkunique_mandates_option() {
		
		$ret = false;
		if(isset($_POST['fieldname']) && isset($_POST['value'])) {
			$ret = $this->mandates_options->check_unique($_POST['fieldname'],$_POST['value']);
		}
		if($ret != false) {
			echo json_encode(array('status' => 'false'));
		} else {
			echo json_encode(array('status' => 'true'));
		}
		
		exit;
	}
	
	private function ajax_checkunique_subcontactor() {

		$ret = false;
		if(isset($_POST['fieldname']) && isset($_POST['value'])) {
			$ret = $this->subcontactor->check_unique($_POST['fieldname'],$_POST['value']);
		}
		if($ret != false) {
			echo json_encode(array('status' => 'false'));
		} else {
			echo json_encode(array('status' => 'true'));
		}
		
		exit;
	}
	
	private function ajax_checkunique_client() {

		$ret = false;
		if(isset($_POST['fieldname']) && isset($_POST['value'])) {
			$ret = $this->clients->check_unique($_POST['fieldname'],$_POST['value']);
		}
		if($ret != false) {
			echo json_encode(array('status' => 'false'));
		} else {
			echo json_encode(array('status' => 'true'));
		}
		
		exit;
	}
	
	private function ajax_checkunique_subclient() {
	
		$ret = false;
		if(isset($_POST['fieldname']) && isset($_POST['value'])) {
			$ret = $this->subclients->check_unique($_POST['fieldname'],$_POST['value']);
		}
		if($ret != false) {
			echo json_encode(array('status' => 'false'));
		} else {
			echo json_encode(array('status' => 'true'));
		}
	
		exit;
	}
	
	private function ajax_user_set_active() {
		
		$ret = json_encode(array('status'=>'error'));
		
		if(isset($_POST['id']) && isset($_POST['value'])) {
			if($this->user->set_user_active($_POST['id'],$_POST['value']) == true){
				$ret = json_encode(array('status'=>'ok'));
			} else {
				$ret = json_encode(array('status'=>'error'));
			}
		}
		
		echo $ret;
		
		exit;
	}
	
	private function ajax_subcontactor_set_active() {
		
		$ret = json_encode(array('status'=>'error'));
		
		if(isset($_POST['id']) && isset($_POST['value'])) {
			if($this->subcontactor->set_subcontactor_active($_POST['id'],$_POST['value']) == true){
				$ret = json_encode(array('status'=>'ok'));
			} else {
				$ret = json_encode(array('status'=>'error'));
			}
		}
		
		echo $ret;
		
		exit;
	}
	
	private function ajax_master_set_active() {
		
		$ret = json_encode(array('status'=>'error'));
		
		if(isset($_POST['id']) && isset($_POST['value'])) {
			if($this->master->set_master_active($_POST['id'],$_POST['value']) == true){
				$ret = json_encode(array('status'=>'ok'));
			} else {
				$ret = json_encode(array('status'=>'error'));
			}
		}
		
		echo $ret;
		
		exit;
	}
	
	private function ajax_client_set_active() {
		
		$ret = json_encode(array('status'=>'error'));
		
		if(isset($_POST['id']) && isset($_POST['value'])) {
			if($this->clients->set_client_active($_POST['id'],$_POST['value']) == true){
				$ret = json_encode(array('status'=>'ok'));
			} else {
				$ret = json_encode(array('status'=>'error'));
			}
		}
		
		echo $ret;
		
		exit;
	}
	
	private function ajax_subclient_set_active() {
	
		$ret = json_encode(array('status'=>'error'));
	
		if(isset($_POST['id']) && isset($_POST['value'])) {
			if($this->subclients->set_subclient_active($_POST['id'],$_POST['value']) == true){
				$ret = json_encode(array('status'=>'ok'));
			} else {
				$ret = json_encode(array('status'=>'error'));
			}
		}
	
		echo $ret;
	
		exit;
	}
	
	private function ajax_checkunique_installation() {

		$ret = false;
		if(isset($_POST['fieldname']) && isset($_POST['value'])) {
			$ret = $this->installations->check_unique($_POST['fieldname'],$_POST['value']);
		}
		if($ret != false) {
			echo json_encode(array('status' => 'false'));
		} else {
			echo json_encode(array('status' => 'true'));
		}
		
		exit;
	}
	
	private function ajax_add_installation_item() {
		$ret = false;
		if(isset($_POST['installation-new-item-name']) && isset($_POST['installation-new-item-cost'])) {
			$ret = $this->installations->add_new_item($_POST['installation-new-item-name'],$_POST['installation-new-item-cost']);
		}
	}
	
	private function ajax_add_subcontactor() {
		
		if($this->subcontactor->subcontactor_add($post)!== false ) {
			$ret = json_encode(array('status' => 'ok'));
		} else {
			$ret = json_encode(array('status' => 'error'));
		}
		
		echo $ret;
		
		exit;
		
	}
	
	private function ajax_loaditems_installation() {

		echo json_encode($this->installations->installations_items_fields($_POST));
		exit;
	}
	
	private function ajax_client_installations_cats_select() {
	
		if(isset($_POST['id'])) {
			$ret['status'] = 'ok';
			$ret['content'] = $this->installations->client_installations_cats_select($_POST['id']);
	
			echo json_encode($ret);
		}
	
		exit;
	}
	
	private function ajax_client_installations_select() {

		if(isset($_POST['id'])) {
			$ret['status'] = 'ok';
			$ret['content'] = $this->installations->clients_installations_select($_POST['id'],$_POST['cat_id'],$_POST['costs']);
				
			echo json_encode($ret);
		}
		
		exit;
	}
	
	private function ajax_installations_select() {
		
		if(isset($_POST['id'])) {
			$ret['status'] = 'ok';
			$ret['content'] = $this->installations->installations_select($_POST['id']);
			
			echo json_encode($ret);
		}
		
		exit;		
	}
	
	private function ajax_loaditems_mandate() {
	
		echo json_encode($this->mandates->mandate_product_fields($_POST));
		exit;
	}
	
	private function ajax_master_to_mandate() {
	
		$ret['status'] = "ok";
		$ret['content'] = $this->master->master_to_mandate($_POST);
		
		echo json_encode($ret);
		
		exit;
	}
	
	private function ajax_confirm_mandate() {
		
		echo json_encode($this->mandates->confirm($_POST));
		
		exit;
		
	}
	
	private function ajax_unconfirm_mandate() {
		
		echo json_encode($this->mandates->unconfirm($_POST));
		
		exit;
		
	}
	
	private function ajax_load_statuses() {
		
		$ret['status'] = 'ok';
		$ret['content'] = $this->mandates->load_statuses($_POST);
		
		echo json_encode($ret);
		
		exit;
		
	}
	
	private function ajax_change_status() {
		
		$ret['status'] = "ok";
		$ret['content'] = $this->mandates->change_status($_POST);
		
		echo json_encode($ret);
		
		exit;
	}
	
	private function ajax_load_history() {
		
		$ret['status'] = "ok";
		$ret['content'] = $this->statushistory->load_history($_POST);
		
		echo json_encode($ret);
		
		exit;
	}
	
	private function ajax_load_routing() {
		
		$ret['status'] = "ok";
		$ret['content'] = $this->mandates->load_routing($_POST);
		
		echo json_encode($ret);
		
		exit;
		
	}
	
	private function ajax_load_geocords() {
		
		$ret['status'] = "ok";
		$ret['content'] = $this->mandates->load_geocords($_POST);
		
		echo json_encode($ret);
		
		exit;
		
	}
	
	private function ajax_load_client_table_rows() {
		
		$ret['status'] = "ok";
		$ret['content'] = $this->clients->clients_table_rows($_POST);

		echo json_encode($ret);
		
		exit;
		
	}
	
	private function ajax_client_delete() {
		
		$ret['status'] = "ok";
		$ret['content'] = $this->clients->client_delete($_POST);
		
		echo json_encode($ret);
		
		exit;
	}
	
	private function ajax_user_delete() {
		
		$ret['status'] = "ok";
		$ret['content'] = $this->user->user_delete($_POST);
		
		echo json_encode($ret);
		
		exit;
	}
	
	private function ajax_subclient_delete() {
	
		$ret['status'] = "ok";
		$ret['content'] = $this->subclients->subclient_delete($_POST);
	
		echo json_encode($ret);
	
		exit;
	}
	
	private function ajax_client_unlock() {
	
		$ret['status'] = "ok";
		$ret['content'] = $this->clients->client_unlock($_POST);
	
		echo json_encode($ret);
	
		exit;
	}
	
	private function ajax_user_unlock() {
	
		$ret['status'] = "ok";
		$ret['content'] = $this->user->user_unlock($_POST);
	
		echo json_encode($ret);
	
		exit;
	}
	
	private function ajax_subclient_unlock() {
	
		$ret['status'] = "ok";
		$ret['content'] = $this->subclients->subclient_unlock($_POST);
	
		echo json_encode($ret);
	
		exit;
	}
	
	private function ajax_mandate_unlock() {
	
		$ret['status'] = "ok";
		$ret['content'] = $this->mandates->mandate_unlock($_POST);
	
		echo json_encode($ret);
	
		exit;
	}
	
	private function ajax_load_parcel_client() {
		
		if(isset($_POST['subclients']) && $_POST['subclients'] == true) {
			$ret['status'] = "ok";
			$ret['client_content'] = $this->parcel->load_client($_POST['id']);
			$ret['subclients_content'] = $this->parcel->load_subclients($_POST['id']);
		} else {
			$ret['status'] = "ok";
			$ret['content'] = $this->parcel->load_client($_POST['id']);
		}
	
		echo json_encode($ret);
	
		exit;
	}
	
	private function ajax_generate_worksheet() {
		
		$ret = $this->pdf->generate_ajax_pdf_link($_POST['id']);
		
		echo json_encode($ret);
		
		exit;
	}
	
	private function ajax_get_installation_cost() {
		
		$ret = $this->mandates->get_installation_cost($_POST);
		
		echo json_encode($ret);
		
		exit;
		
	}
	
	private function ajax_mandates_table_source() {
		//var_dump($_POST);exit;
		$ret = $this->mandates->mandates_table_source($_POST);
		
		echo json_encode($ret);
		
		exit;
	}
	
	private function ajax_refresh_confirm_calendar() {
		
		$ret = $this->mandates->confirm_calendar($_POST);
		
		echo json_encode($ret);
		
		exit;
		
	}
	
	private function ajax_mandate_file_upload() {

		//$ret = $this->mandates->file_upload($_POST,$_FILES);
		
		$ret = $this->uploadfiles->upload_file($_POST,$_FILES);
		
		echo json_encode($ret);
		
		exit;
	}
	
	private function ajax_load_mandate_filelist() {
	
		$ret = $this->uploadfiles->files_table($_POST);
		
		echo json_encode($ret);
		
		exit;
	}
	
	private function ajax_email_contents_delete() {
	
		$ret = $this->email_contents->email_contents_delete($_POST);
		
		echo json_encode($ret);
		
		exit;
	
	}
	
	private function ajax_delete_attachment_file() {
	
		$ret = $this->uploadfiles->delete_attachment_file($_POST);
		
		echo json_encode($ret);
		
		exit;
	
	}
	
	private function ajax_check_upload_file() {
		
		$ret = $this->uploadfiles->check_upload_file($_POST);
		
		echo json_encode($ret);
		
		exit;
		
	}
	
	private function ajax_save_handwrite_image() {
		
		$ret = $this->uploadfiles->save_handwrite_image($_POST);
		
		echo json_encode($ret);
		
		exit;
		
	}
	
	private function ajax_save_mandate_comments() {
		
		$ret = $this->mandates->save_mandate_comments($_POST);
		
		echo json_encode($ret);
		
		exit;
		
	}
	
	private function ajax_addcheck() {
		
		$ret = $this->user_logged->add_check();
		//var_dump($ret);
		echo json_encode($ret);
		
		exit;
	}
	
	private function ajax_compact_mandate_view() {
		
		$ret = $this->mandates->load_compact_view($_POST);
		
		echo json_encode($ret);
		
		exit;
		
	}
	
	/*private function ajax_checklogged() {
		
		$username = $_POST['username'];
		
		$password = $_POST['password'];
		
		if($_POST['logintype'] == 2){
			$master = true;
		} else {
			$master = false;
		}
		
		$ret = $this->user_logged->add_check($username,$password,$master);
		//var_dump($ret);
		echo json_encode($ret);
		
		exit;
		
	}*/
	
	// end ajax
	
	private function kill_session() {
		
	}
	
	
	
	private function debug() {
		?><div class="container-fluid"><pre><?php
		var_dump($_SESSION);
		var_dump(session_id());
		var_dump(unserialize(RULES));
		foreach($_SESSION as $key => $cookie) {
			//var_dump($key);var_dump($cookie);
			//var_dump(strpos($key,'gdgdgdsh'));
		}
		?></pre></dev><?php
	}
	
	public function create_url($action = NULL) {
		if($action != NULL) {
			return "http://" . ROOT_URL . "/?m=masterservice&act=" . $action;
			//return "http://" . ROOT_URL . "?m=masterservice&act=" . $action;
		} else {
			return "javascript:void(0);";
		}
	}
	
	private function js_to_view() {

		$js_content = '';
		
		if(!@file_exists($this->params['view_path'] . 'js/' . $this->action . '.js')) {
			return $js_content;
		} else {
			$file_content = file_get_contents($this->params['view_path'] . 'js/' . $this->action . '.js');
			
			if($this->action == 'login') {
				
				// Login page
				
				$js_content .= "<!-- Js to login view -->". "\r\n" ; 
				$file_content = str_replace('[{%AJAX_URL%}]',$this->ajax_url,$file_content);
				$js_content .= str_replace('[{%SESSION_ID%}]',session_id(),$file_content);
				
			} elseif($this->action == 'profil') {
				
				// Profil page
				//exit;
				//$js_content .= "<!-- Js to login view -->". "\r\n" ; 
				//$file_content = str_replace('[{%AJAX_URL%}]',$this->ajax_url,$file_content);
				//$js_content .= str_replace('[{%SESSION_ID%}]',session_id(),$file_content);
				$js_content = $file_content;
				
				
			} elseif($this->action == 'listusers') {
				
				// listUsers
				
				$js_content = str_replace('[{%THEME_URI%}]',$this->params['theme_uri'],$file_content);
				
			} elseif($this->action == 'listsubcontactors') {
				
				// listUsers
				
				$js_content = str_replace('[{%THEME_URI%}]',$this->params['theme_uri'],$file_content);
				
			} elseif($this->action == 'addsubcontactor') {
				
				$js_content = $file_content;
				
			} elseif($this->action == 'adduser') {
				
				$js_content = $file_content;
				
			} elseif($this->action == 'edituser') {
				
				$js_content = $file_content;
				
			} elseif($this->action == 'editsubcontactor') {
				
				$js_content = $file_content;
				
			} elseif($this->action == 'listmasters') {
				
				$js_content = $file_content;
				
			} elseif($this->action == 'addmaster') {
				
				$js_content = $file_content;
				
			} elseif($this->action == 'editmaster') {
				
				$js_content = $file_content;
				
			} elseif($this->action == 'addorder') {
				
				$js_content = $file_content;
				
			} elseif($this->action == 'installations') {
				
				$js_content = $file_content;
				
			} elseif($this->action == 'addinstallation') {
				
				$js_content = $file_content;
				
			} elseif($this->action == 'editinstallation') {
				
				$js_content = $file_content;
				
			} elseif($this->action == 'addmandate') {
				
				$js_content = $file_content;
				
			} elseif($this->action == 'listmandates') {
				
				$js_content = $file_content;
				
			} elseif($this->action == 'editmandate') {
				
				$js_content = $file_content;
				
			} elseif($this->action == 'listclients') {
				
				$js_content = $file_content;
				
			} elseif($this->action == 'editclient') {
				
				$js_content = $file_content;
				
			}  elseif($this->action == 'addclient') {
				
				$js_content = $file_content;
			
			} elseif($this->action == 'listmandates_options') {
				
				$js_content = $file_content;
				
			} elseif($this->action == 'addmandates_option') {
				
				$js_content = $file_content;
				
			} elseif($this->action == 'editmandates_options') {
				
				$js_content = $file_content;
				
			} elseif($this->action == 'master') {
			
				$js_content = $file_content;
				
			} elseif($this->action == 'addsubclient') {
			
				$js_content = $file_content;
				
			} elseif($this->action == 'editsubclient') {
			
				$js_content = $file_content;
				
			} elseif($this->action == 'listsubclients') {
			
				$js_content = $file_content;
				
			} elseif($this->action == 'mandate_tracking') {
			
				$js_content = $file_content;
				
			} elseif($this->action == 'adday') {
			
				$js_content = $file_content;
				
			} elseif($this->action == 'listdays') {
			
				$js_content = $file_content;
				
			} elseif($this->action == 'editday') {
			
				$js_content = $file_content;
				
			} elseif($this->action == 'listemail_contents') {
			
				$js_content = $file_content;
				
			} elseif($this->action == 'addemail_contents') {
			
				$js_content = $file_content;
				
			} elseif($this->action == 'editemail_contents') {
			
				$js_content = $file_content;
				
			} elseif($this->action == 'reports') {
			
				$js_content = $file_content;
				
			}

			return $js_content;
		}
	}
	
	public function view() {
		
		// Load header
		$header_content = $this->params['theme_path'] . 'header.php';

		if(file_exists($header_content)) {
			require_once($header_content);
		} else {
			$this->debug();
			echo "Hiányzó template file: header";
			$this->close_db();
			exit(-1);
		}
		
		//if($this->action != 'login') {
		//if($this->action != 'login' && !isset($_SESSION['HDT_master_user'])) {
		//if(($this->action != 'login' && !isset($_SESSION['HDT_master_user'])) || ($this->action != 'mandate_tracking' && !isset($_SESSION ['HDT_mandate_tracking']))) {
		if($this->action != 'login' && !isset($_SESSION['HDT_master_user']) && $this->action != 'mandate_tracking') {
			// Load menupanel
			$menupanel_content = $this->params['theme_path'] . 'menu.panel.php';
			if(file_exists($menupanel_content)) {
				require_once($menupanel_content);
			} else {
				echo "Hiányzó template file: menupanel";
				$this->close_db();
				exit(-1);
			}
		
		}
		
		// View content

		$view_content = $this->params['view_path'] .$this->action.'.view.php';
		if(!file_exists($view_content)) {
			$view_content = $this->params['view_path'] . '404.view.php';
		}
		require_once($view_content);
		
		// Load footer
		$footer_content = $this->params['theme_path'] . 'footer.php';
		if(file_exists($footer_content)) {
			require_once($footer_content);
		} else {
			echo "Hiányzó template file: footer";
			$this->close_db();
			exit(-1);
		}
		
		$this->close_db();
		
	}
	
}
