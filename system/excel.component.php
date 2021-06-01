<?php
require_once('tools/phpexcel/PHPExcel.php');

class ExcelReport {
	
	private $excel;
	
	private $excel_writer;
	
	private $parent;
	
	public function __construct($parent = NULL) {
		
		$this->parent = $parent;
		
	}
	
}

?>