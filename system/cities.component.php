<?php

require_once('DbEngines/cities.db.php');

class cities {

	private $cities_db;
	
	public function __construct(){
		
		$this->cities_db = new CitiesDB();
	}
	
	public function zipcodes_select($zipcode = NULL) {
		
		$result = $this->cities_db->list_cities();
		
		ob_start();
		
		//$result_array = array();
		
		while($row = mysql_fetch_assoc($result)) {
			if($zipcode != NULL && $zipcode == $row['iranyitoszam']){
				$selected = 'selected';
			} else {
				$selected = '';
			}
			?><option value="<?php echo $row['helyiseg'];?>" <?php echo $selected;?>><?php echo $row['iranyitoszam'];?></option><?php
			
			//$result_array[] = $row;
		}
		//var_dump($result_array);
		//exit;
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}
	
	public function cities_select($zipcode = NULL) {
		
		$result = $this->cities_db->list_cities();
		
		$options = array();
		
		while($row = mysql_fetch_assoc($result)) {
			$options[$row["iranyitoszam"]] = '<option value="'.$row["helyiseg"].'">'.$row["iranyitoszam"].'</option>';
		}
		
		if(count($options) > 0) {
			?>
			<select id="cities-list" name="cities-list" class="form-control select2_single short-field"><?php
				if($zipcode != NULL) {
					echo $options[$zipcode]."\r\n";
					unset($options[$zipcode]);
				}
				foreach($options as $option) {
					echo $option . "\r\n";
				}
			?>
			</select>
			<?php
		}
		
		return;
		
	}
	
}
