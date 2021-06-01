<?php

// User levels
	
define('SUPER_USER',0); // Legfőbb user
	
define('ADMIN_USER',1); // Adminisztrátor user HDT
	
define('SUBCON_ADMIN',2); // Alvállaklozók adminisztrálása
	
//define('MASTER_USER',3); // Csak mester
	
define('EXPORT_USER',3); // Export
	
define('IMPORT_USER',4); // Import

define('HDT_USER',5); // HDT felhasználó

define('CLIENT_ADMIN',6); // Megbízó adminisztrálás, nem alaqpértelmezett

define('SUBCLIENT_ADMIN',7); // Almegbízó adminisztrálás

define('SUBCLIENT_USER',8); // Almegbízó felhasználó 

$user_levels = array(
					'SuperUser',
					'Rendszeradminisztrátor',
					'Alvállalkozó adminisztrátor',
					'Exportáló felhasználó',
					'Importáló felhasználó',
					'HDT felhasználó',
					'Megbízó adminisztrátor',
					'Almegbízó adminisztrátor',
					'Almegbízó felhasználó'
		    		);

define('USER_LEVELS',serialize($user_levels));
		    
