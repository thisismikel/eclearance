<?php
require "routines.php";
$myobj = validatetoken();
if (empty($myobj->userid)){
    header('Location:index.html?message='.urlencode("Invalid User"));
} else {$userid = $myobj->userid;}
$email = $_REQUEST['email'];
$fullname = htmlspecialchars(strtoupper($_REQUEST['fullname']),ENT_QUOTES,"UTF-8");
$mobileno = htmlspecialchars(strtoupper($_REQUEST['mobileno']),ENT_QUOTES,"UTF-8");
$address = 	htmlspecialchars(strtoupper($_REQUEST['address']),ENT_QUOTES,"UTF-8");
$tk = $_REQUEST['tk'];
$command = "CALL adduser('','$fullname', 'CLIENT', 'ONLINE REGISTRATION', '', '$mobileno', '$email','', '','$address',2)";
$nrow = getrow($command);
if (!empty($mobileno)){
					$msg = "Hi ".$fullname." your DAR XI registration is confirmed.  You may now proceed on your online transactions.  Thank you";
					$ch2 = curl_init();
					$url2= "https://sms.davaocity.gov.ph/home/send";
					$numbers = $mobileno;
					
					$data2 = http_build_query(array("cellno"=>$numbers,"message"=>$msg));
					$getUrl2 = $url2."?".$data2;
					curl_setopt($ch2,CURLOPT_SSL_VERIFYPEER,FALSE);
					curl_setopt($ch2,CURLOPT_FOLLOWLOCATION,TRUE);
					curl_setopt($ch2,CURLOPT_RETURNTRANSFER,TRUE);
					curl_setopt($ch2,CURLOPT_URL,$getUrl2);
					curl_setopt($ch2,CURLOPT_TIMEOUT,80);
					$response2 = curl_exec($ch2);
					//if (curl_error($ch2)){
					//	echo 'Request Error: '. curl_error($ch2);
					//}
					curl_close($ch2);
}

//header('Location:index.html?trans=onlinereg&userid='.$nrow['@id'].'&token='.$tk.'&receiver='.urldecode($email).'&fullname='.urlencode($fullname));
header('Location:index.html');
?>