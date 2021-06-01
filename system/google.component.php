<?php

class Google_tools {
	
	public function __construct() {
	
	}
	
	public function get_geocodes($address= NULL){
	
		if($address != NULL) {
			
			//$address = urlencode("4761 Porcsalma Petőfi út 6.");
			
			$address = urlencode($address);
			
			//$url = 'http://maps.googleapis.com/maps/api/geocode/json?address=hungary,+'.$zipcode.'&sensor=false';
			$url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$address.'&sensor=true';
			$ch = curl_init();
			// Disable SSL verification
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			// Will return the response, if false it print the response
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			// Set the url
			curl_setopt($ch, CURLOPT_URL,$url);
			// Execute
			$result = curl_exec($ch);
			//%var_dump($result);exit;
			// Closing
			curl_close($ch);
			$result_array = json_decode($result, true);
			foreach($result_array["results"] as $res){
				return $res["geometry"]["location"];
			}

			//$url = "http://maps.google.com/maps/api/geocode/json?address=".$address;
 
    		// get the json response
    		//$resp_json = file_get_contents($url);
     
    		// decode the json
    		//$resp = json_decode($resp_json, true);
    		
    		//var_dump($resp);
		}
	}
	
	public function get_distance($from = NULL, $to = NULL) {
		
		$from = urlencode($from);
		$to = urlencode($to);
		
		$data = file_get_contents("http://maps.googleapis.com/maps/api/distancematrix/json?origins=$from&destinations=$to&language=hu-HU&sensor=false");
		
		return json_decode($data);
	}
	
}

?>
