<?php
if(!isset($_SESSION)){
session_start();
}
header('Content-Type: application/json; charset=utf-8');

require_once('../config.inc.php');
require_once('masterservice.module.php');

if(@isset($_POST) && (@$_POST['s_id'] != session_id())){
	die('ajax error - POST');
}

// HTTP Referer vizsgálata

//var_dump($_SERVER["HTTP_REFERER"]);

$referer = preg_replace("/\/\?.*$/","",$_SERVER["HTTP_REFERER"]);
$root_url = preg_replace('/\/$/','',ROOT_URL);
//var_dump($root_url);
//var_dump($referer);
$referer = str_replace("http://","",$referer);
$referer = str_replace("index.php","",$referer);

//if($referer !== ROOT_URL) {
if($referer !== $root_url) {
	die('ajax error - REFERER');
}

//$params = array();
		$params["module_path"] = dirname(__FILE__).DIRECTORY_SEPARATOR;
		//$params['theme_path'] = str_replace('system','',dirname(__FILE__));
		$params["theme_path"] = str_replace('system','',dirname(__FILE__)) . 'templates' . DIRECTORY_SEPARATOR . $_SESSION['HDT_theme'] . DIRECTORY_SEPARATOR .'theme' . DIRECTORY_SEPARATOR;
		$params['view_path'] = str_replace('system','',dirname(__FILE__)) . 'templates' . DIRECTORY_SEPARATOR . $_SESSION['HDT_theme'] . DIRECTORY_SEPARATOR .'views' . DIRECTORY_SEPARATOR;
		
		$params["qr_path"] = str_replace('system','',dirname(__FILE__)) . 'qrcodes' . DIRECTORY_SEPARATOR;
		$params["qr_uri"] = ROOT_URL . "/qrcodes/";
		
		$params["pdf_path"] = str_replace('system','',dirname(__FILE__)) . 'pdfs' . DIRECTORY_SEPARATOR;
		$params["pdf_uri"] = ROOT_URL . "/pdfs/";
		
		$params["uploads_path"] = str_replace('system','',dirname(__FILE__)) . 'uploads' . DIRECTORY_SEPARATOR;
		$params["uploads_uri"] = ROOT_URL . "/uploads/";
		
		$_SESSION['pdf_path'] = $params['pdf_path'];
		$_SESSION['pdf_uri'] = $params['pdf_uri'];
		
		$params["theme_uri"] = ROOT_URL . "/templates/" . $_SESSION['HDT_theme'] . "/theme/";
		
		//var_dump($params);
		
$module = new masterservice($params,true);

$ret = array("status" => "ok","msg" => "Normal exit");

echo json_encode($ret,true);

exit;
