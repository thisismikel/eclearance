

<?php
$receiver = $_REQUEST['receiver'];
$fullname = $_REQUEST['fullname'];
$idappointment = $_REQUEST['idappointment'];
$cellno = $_REQUEST['cellno'];
$to = $receiver;
$subject = "DAR XI Online Appointment";

$message = '
<html>
<head>
<title>DAR XI Online Appointment</title>
</head>
<body>
<h1><img src="https://darxi.net/DARLogo.jpg"></h1>
<p>Hi '.$fullname.',</p>
<p>Your online appointment schedule has been re-scheduled.  Please click the link below for the details of your new appointment schedule.</p>
<p>Link : <a href="https://darxi.net/verifyappointment.php?idappointment='.$idappointment.'">https://darxi.net/verifyappointment.php?idappointment='.$idappointment.'</a></p>
<p></p>
<br/><br/><br/>
Regards,<br/><br/>
DAR XI
<p>This is a system generated email.  Please do not reply.<p>
<p>&nbsp;</p>
<div><i>This email and its attachments are intended solely for the addressee(s) as indicated above and may contain confidential and/or privileged information which may be legally protected from disclosure. If you are not the intended recipient or if this message has been addressed to you in error, please immediately alert the sender by reply email and then delete this message and its attachments. Please be advised that any use, dissemination, copying, or storage of this message or its attachments is strictly prohibited.</i></div>

</body>
</html>
';
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: DAR XI <noreply@darxi.net>' . "\r\n";


mail($to,$subject,$message,$headers);


if ($cellno){
					
					$ch2 = curl_init();
					$url2= "https://myYeastar/home/send";
					$numbers = $cellno;
					$msg = "Hi ".$fullname.", Your DAR XI online appointment schedule has been re-scheduled.  Please click the link https://darxi.net/verifyappointment.php?idappointment=".$idappointment; 
					$data2 = http_build_query(array("cellno"=>$numbers,"message"=>$msg));
					$getUrl2 = $url2."?".$data2;
					curl_setopt($ch2,CURLOPT_SSL_VERIFYPEER,FALSE);
					curl_setopt($ch2,CURLOPT_FOLLOWLOCATION,TRUE);
					curl_setopt($ch2,CURLOPT_RETURNTRANSFER,TRUE);
					curl_setopt($ch2,CURLOPT_URL,$getUrl2);
					curl_setopt($ch2,CURLOPT_TIMEOUT,80);
					$response2 = curl_exec($ch2);
					if (curl_error($ch2)){
						echo 'Request Error: '. curl_error($ch2);
					}
					curl_close($ch2);
	}
echo 'success';
?>