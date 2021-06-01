<?php

require_once('DbEngines/pg_installation.db.php');

class pg_installation {
	
	private $pg_installation_db;
	
	public function __construct(){
		
		$this->pg_installation_db = new Pg_installationDB();

	}
	
}
?>
