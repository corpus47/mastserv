<?php

require_once('DbEngines/sys.db.php');

class Application {
	
	private $module = NULL;
	
	public $params = array();
	
	
	public function __construct() {
		
		if(!isset($_SESSION['HDT_theme'])) {
			$_SESSION['HDT_theme'] = THEME;
		}

		if(isset($_GET['logout'])) {
			//session_destroy();
			$this->kill_session();
			header('Location: http://'.ROOT_URL);
			exit;
		}
		
		if(!isset($_GET['m'])) {
			header('Location: http://' . ROOT_URL . '?m='.DEFAULT_MODULE);
			exit;
		}
		
		$this->params["module_path"] = dirname(__FILE__).DIRECTORY_SEPARATOR;
		//$this->params['theme_path'] = str_replace('system','',dirname(__FILE__));
		$this->params["theme_path"] = str_replace('system','',dirname(__FILE__)) . 'templates' . DIRECTORY_SEPARATOR . $_SESSION['HDT_theme'] . DIRECTORY_SEPARATOR .'theme' . DIRECTORY_SEPARATOR;
		$this->params['view_path'] = str_replace('system','',dirname(__FILE__)) . 'templates' . DIRECTORY_SEPARATOR . $_SESSION['HDT_theme'] . DIRECTORY_SEPARATOR .'views' . DIRECTORY_SEPARATOR;
		
		$this->params["qr_path"] = str_replace('system','',dirname(__FILE__)) . 'qrcodes' . DIRECTORY_SEPARATOR;
		$this->params["qr_uri"] = ROOT_URL . "/qrcodes/";
		
		$this->params["pdf_path"] = str_replace('system','',dirname(__FILE__)) . 'pdfs' . DIRECTORY_SEPARATOR;
		$this->params["pdf_uri"] = ROOT_URL . "/pdfs/";
		
		$this->params["uploads_path"] = str_replace('system','',dirname(__FILE__)) . 'uploads' . DIRECTORY_SEPARATOR;
		$this->params["uploads_uri"] = ROOT_URL . "/uploads/";
		
		//$_SESSION['pdf_path'] = $this->params['pdf_path'];
		//$_SESSION['pdf_uri'] = $this->params['pdf_uri'];
		
		$this->params["theme_uri"] = ROOT_URL . "/templates/" . $_SESSION['HDT_theme'] . "/theme/";
		
		// GET feldolgozása
		
		$this->load_module($_GET["m"]);
		
		// Ajax feldolgozása
		
		if(isset($_POST['ajax'])) {
			unset($this->module);
			$_GET['act'] = $_POST['act'];
			$_GET['ajax'];
			$this->load_module($_POST['m']);
			
		}
	}
	
	private function kill_session() {
		//var_export($this->module);
		foreach($_SESSION as $key => $cookie) {
			if(strpos($key,'HDT_') !== false) {
				unset($_SESSION[$key]);
			}
		}
		//exit;
	}
	
	private function load_module($module_name) {
		if(!file_exists($this->params["module_path"].$module_name.'.module.php')) {
			die("Hiányzó modul: ".$module_name);
		} else {
			require_once($this->params["module_path"].$module_name.'.module.php');
			$this->module = new $module_name($this->params);
		}
	}
	
	
	private function view() {
		$this->module->view();
	}
	
	public function run() {
		$this->view();
	}
	
}
