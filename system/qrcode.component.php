<?php
require_once('tools/phpqrcode/qrlib.php');

class Qrcode_tool {
	
	private $parent;
	
	public function __construct($parent = null) {
	
		$this->parent = $parent;
		
	}
	
	public function generate_mandate_master_qr($mandate_id = NULL, $return_path = false) {
	
		$params = $this->parent->get_component('params');

		$mandate_component = $this->parent->get_component('mandates');
		
		$mandate = $mandate_component->load_mandate($mandate_id);
		
		$qr_filename =  str_replace("/","_",$mandate['Mandate_serial']) . ".png";
		
		$qrcode_path = $params['qr_path'] . $qr_filename;
		
		$qr_href = 'http://' . $params['qr_uri'] . $qr_filename;
		
		$qr_link = '<img class="qr-container" src="'.$qr_href.'" />';
		
		$qr_code = "http://" . ROOT_URL . '/?m=masterservice&act=master&mandate_id='.$mandate_id;
		
		if(!file_exists($qrcode_path)){
			QRcode::png($qr_code, $qrcode_path,'L', 2, 2);
		}
		
		if(file_exists($qrcode_path)) {
			if($return_path == true) {
				return $qrcode_path;
			} else {
				return $qr_link;
			}
		} else {
			return false;
		}
		
	}	
	
}
?>
