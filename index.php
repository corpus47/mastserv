<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

require_once('config.inc.php');
require_once('system/application.php');

define('DEBUG',true);

$app = new Application();

$app->run();

?>
