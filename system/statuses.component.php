<?php

define('MANDATE_WAITING',0);

define('MANDATE_GET_MASTER',1);

define('MANDATE_COORDINATE',2);

define('INSTALLATION_BEGIN',3);

define('INSTALLATION_SUSPEND',4);

define('INSTALLATION_SUCCESS',5);

define('CASH_ON_MASTER',6);

class statuses {
	
	private $status = array();
	
	private $statuses = array();
	
	public function __construct() {
		
		$this->statuses[MANDATE_WAITING] = array('label'=>'Kiszállási időpont egyeztetés szükséges!', 'color'=>'#333333','hook'=>'MANDATE_WAITING');
		$this->statuses[MANDATE_GET_MASTER] = array('label'=>'Mester a megbízást megkapta', 'color'=>'#FF0000','hook'=>'MANDATE_GET_MASTER');
		$this->statuses[MANDATE_COORDINATE] = array('label'=>'Installáció vevővel egyeztetve', 'color'=>'#FF8000','hook'=>'MANDATE_COORDINATE');
		$this->statuses[INSTALLATION_BEGIN] = array('label'=>'Munka megkezdve', 'color'=>'#0080FF','hook'=>'INSTALLATION_BEGIN');
		$this->statuses[INSTALLATION_SUSPEND] = array('label'=>'Munka felfüggesztve', 'color'=>'#FF00BF','hook'=>'INSTALLATION_SUSPEND');
		$this->statuses[INSTALLATION_SUCCESS] = array('label'=>'Munka lezárva', 'color'=>'#088A08','hook'=>'INSTALLATION_SUCCESS');
		$this->statuses[CASH_ON_MASTER] = array('label'=>'Fizetés a mesternél', 'color'=>'#008647','hook'=>'CASH_ON_MASTER');
	
	}
	
	public function add_status($status = NULL) {
		
		return;
		
	}
	
	public function list_statuses($array = false,$master = false) {
		
		if($array == true && $master == false) {
			return $this->statuses;
		} elseif($array == true && $master == true) {
			unset($this->statuses[MANDATE_WAITING]);
			unset($this->statuses[MANDATE_GET_MASTER]);
			unset($this->statuses[MANDATE_COORDINATE]);
			
			return $this->statuses;
			
		}
		// Ha mester felület
		
		foreach($this->statuses as $status) {
			?><span style="color:<?php echo $status['color']?>;"><?php echo $status['label'];?></span><br /><?php
		}	
	}
	
	public function get_status($code = NULL) {
		return $this->statuses[$code];
	}
}

?>
