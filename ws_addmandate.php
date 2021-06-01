<?php
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
//$post_data = "username=hdt_webserv&password=Ts4e62Pr&act=add_mandate&array[1]=egy&array[2]=ketto";
$post_data = "username=hdt_webserv&password=Ts4e62Pr&act=add_mandate";

$order_data = array(
			"hdt_order_id" => "4496",
			"mandate-hdt-partner-id" => "13",
			"mandate-customer-name" => "Webservice Próba új",
			"mandate-customer-zipcode" => "1122",
			"mandate-customer-city" => "Budapest",
			"mandate-customer-address" => "Utca utca",
			"mandate-customer-phonenum" => "06101234567",
			"mandate-customer-email" => "teszt@teszt.hu",
			"mandate-kiszallitas" => "2017.05.01"
); 

$details_product_name = array(
	"Teszt termék",
	"Teszt termék 2"
);
$details_installations = array(
	"5",
	"6"
);

foreach($order_data as $key=>$data) {
	$post_data .= "&".$key."=".$data;
}

foreach($details_product_name as $key=>$data) {
	$post_data .= "&mandate-product-name[".$key."]=".$data;
}
foreach($details_installations as $key=>$data) {
	$post_data .= "&mandate-installations[".$key."]=".$data;
}

//var_dump($post_data);exit;

$ch = curl_init();
 
curl_setopt($ch, CURLOPT_URL, "http://localhost/masterservice/index.php?m=webservice" );
//curl_setopt($ch, CURLOPT_URL, "http://parceldev.homedt.hu/homedt/index.php?m=webservice" );  
curl_setopt($ch, CURLOPT_POST, 1 ); 
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
$postResult = curl_exec($ch); 
var_export($postResult);
if (curl_errno($ch)) { 
   print curl_error($ch); 
} 
curl_close($ch);

?>
