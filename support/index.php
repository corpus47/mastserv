<?php
session_start();
$session = $_SESSION;

require_once('support.php');

$support = new Support();

require_once 'header.php';
require_once 'navbar.php';
require_once 'sidebar.php';

require_once 'content.php';

require_once 'footer.php'
?>