<?php

define("HOME_URL","/masterservice");

define("ROOT_URL",($_SERVER['HTTP_HOST'].HOME_URL));

define('DEFAULT_MODULE','masterservice');

define("THEME","default");


// Databases

if($_SERVER['HTTP_HOST'] === 'localhost') {
	
	define('DB_HOST','localhost');
	define('DB_USER','root');
	define('DB_PASSWORD','sc1959op');
	define('DB_NAME','hdt');

	define('PARCEL_DB_HOST','localhost');
	define('PARCEL_DB_USER','root');
	define('PARCEL_DB_PASSWORD','sc1959op');
	define('PARCEL_DB_NAME','parceldev');

} elseif($_SERVER['HTTP_HOST'] === '195.228.35.4') { 

	define('DB_HOST','localhost');
	define('DB_USER','root');
	define('DB_PASSWORD','sc1959op');
	define('DB_NAME','hdt');
	
	define('PARCEL_DB_HOST','localhost');
	define('PARCEL_DB_USER','root');
	define('PARCEL_DB_PASSWORD','sc1959op');
	define('PARCEL_DB_NAME','parceldev');
	
} else {
	
	define('DB_HOST','localhost');
	define('DB_USER','root');
	define('DB_PASSWORD','sc1959op');
	define('DB_NAME','hdt');
	
	define('PARCEL_DB_HOST','localhost');
	define('PARCEL_DB_USER','root');
	define('PARCEL_DB_PASSWORD','sc1959op');
	define('PARCEL_DB_NAME','parceldev');
	
}

// User levels

define('ADMIN',0);
define('PARTNER_ADMIN',1);
define('EDITOR',2);
define('MASTER',3);
define('IMPORT',4);
define('EXPORT',5);

// Rules template

$rules = array(
			'masterservice' => array(
									'listusers' => '',
								),
		);
		
define('RULES',serialize($rules));
