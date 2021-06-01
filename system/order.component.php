<?php
require_once('DbEngines/order.db.php');

class order {

	private $order_db;
	
	public function __construct(){
		
		$this->order_db = new OrderDB();
	}

}