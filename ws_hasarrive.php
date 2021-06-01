<?php
error_reporting(E_ALL);
$post_data = "username=hdt_webservice&password=Ts4e62Pr&act=parcel_arrived&order_id=20001";

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