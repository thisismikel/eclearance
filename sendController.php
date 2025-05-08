<?php
require "routines.php";
function main1(){
$officecode = $_REQUEST['officecode'];
$role = $_REQUEST['role'];
$iddcrform = $_REQUEST['iddcrform'];
$officename = $_REQUEST['officename'];
if (empty($officename)){
	$officename='';
} else {
	$officename = 'Office: '.$officename;
}
if (!empty($role)){
	$command = "select * from users where users.office = '$officecode' and users.role = '$role'";
}
else {
	$command = "select * from users where users.office = '$officecode'";
}

$table=gettable($command);

foreach($table as $user){
	
	
	
	$message = '
		<html>
		<head>
			<title>DAR XI Electronic Document Tracking</title>
		</head>
		<body>
			<h1><img src="https://darxi.net/DARLogo.jpg"></h1>
			<p>Hi '.$user['fullname'].',</p>
			<p>&nbsp;</p>
            <p>This is to inform you that you received a letter from DAR '.$officename.' Electronic Document Tracking Application and requires your immediate response.</p>
            <p>Please log-in to the application to check further details and update the status of the letter.</p>
            <p>Contact DAR Region XI for assistance on how to access your account.</p>
            <p>&nbsp;</p>
            <p>Very truly yours,</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>DAR XI Electronic Document Tracking Team</p>
            <p>This is a system generated email.  Please do not reply.<p>
            <p>&nbsp;</p>
            <div><i>This email and its attachments are intended solely for the addressee(s) as indicated above and may contain confidential and/or privileged information which may be legally protected from disclosure. If you are not the intended recipient or if this message has been addressed to you in error, please immediately alert the sender by reply email and then delete this message and its attachments. Please be advised that any use, dissemination, copying, or storage of this message or its attachments is strictly prohibited.</i></div>
		</body>
		</html>
	';
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	$headers .= 'From: DAR XI <info@darxi.net>' . "\r\n";
	$to = $user['emailaddress'];
	$subject = "DAR Document Tracking No.: " .$iddcrform;
	mail($to,$subject,$message,$headers);
	
	
	
	
	
	if (!empty($user['cellno'])){
					$username = $user['fullname'];
					$ch2 = curl_init();
					$url2= "https://myYeastart/home/send";
					
					$numbers = $user['cellno'];
					$msg = "Good Day ".$username.", you just received a message from DAR Document Tracking Application ".$officename." that requires your immediate response.  Please log-in to check further details. Please contact DAR Region XI for assistance on how to access your account. Thank you."; 
					$data2 = http_build_query(array("cellno"=>$numbers,"message"=>$msg));
					$getUrl2 = $url2."?".$data2;
					curl_setopt($ch2,CURLOPT_SSL_VERIFYPEER,FALSE);
					curl_setopt($ch2,CURLOPT_FOLLOWLOCATION,TRUE);
					curl_setopt($ch2,CURLOPT_RETURNTRANSFER,TRUE);
					curl_setopt($ch2,CURLOPT_URL,$getUrl2);
					curl_setopt($ch2,CURLOPT_TIMEOUT,80);
					//$response2 = curl_exec($ch2);
					if (curl_error($ch2)){
						echo 'Request Error: '. curl_error($ch2);
					}
					curl_close($ch2);
	}
}
echo 'Send Successful';
}
function completed(){
$officecode = $_REQUEST['officecode'];
$iddcrform = $_REQUEST['iddcrform'];
$officename = $_REQUEST['officename'];

	$command = "select * from dcrform where dcrform.iddcrform = $iddcrform";

$row=getrow($command);
if (!empty($row['email'])){
	
	
	$message = '
		<html>
		<head>
			<title>DAR XI Electronic Document Tracking</title>
		</head>
		<body>
			<h1><img src="https://darxi.net/DARLogo.jpg"></h1>
			<p>Hi '.$row['applicant'].',</p>
			<p>&nbsp;</p>
            <p>Please be informed that your application with tracking no: '.$iddcrform.' has been approved.  This e-mail shall serve as  your notice.  Please claim at '.$officename.' your Decision today, and your Certificate of Finality after 15 days.</p>
			<p>Please contact DAR Region XI for assistance and further details.</p> 
            <p>&nbsp;</p>
            <p>Very truly yours,</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>DAR Electronic Document Tracking Team</p>
            <p>This is a system generated email.  Please do not reply.<p>
            <p>&nbsp;</p>
            <div><i>This email and its attachments are intended solely for the addressee(s) as indicated above and may contain confidential and/or privileged information which may be legally protected from disclosure. If you are not the intended recipient or if this message has been addressed to you in error, please immediately alert the sender by reply email and then delete this message and its attachments. Please be advised that any use, dissemination, copying, or storage of this message or its attachments is strictly prohibited.</i></div>
		</body>
		</html>
	';
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	$headers .= 'From: DAR XI <info@darxi.net>' . "\r\n";
	$to = $row['email'];
	$subject = "DAR XI Document Tracking No.: " .$iddcrform;
	mail($to,$subject,$message,$headers);
	
	
	
	
}
if (!empty($row['cellno'])){
	$applicant = $row['applicant'];
					$ch2 = curl_init();
					$url2= "https://sms.davaocity.gov.ph/home/send";
					$numbers = $row['cellno'];
					$msg = "Good Day ".$applicant.", please be informed that your application with tracking number: ". $iddcrform ." has been approved.  This SMS shall serve as your notice.  Please claim from DAR ".$officename." your Descision today and your Certificate of Finality after 15 days."; 
					$data2 = http_build_query(array("cellno"=>$numbers,"message"=>$msg));
					$getUrl2 = $url2."?".$data2;
					curl_setopt($ch2,CURLOPT_SSL_VERIFYPEER,FALSE);
					curl_setopt($ch2,CURLOPT_FOLLOWLOCATION,TRUE);
					curl_setopt($ch2,CURLOPT_RETURNTRANSFER,TRUE);
					curl_setopt($ch2,CURLOPT_URL,$getUrl2);
					curl_setopt($ch2,CURLOPT_TIMEOUT,80);
					//$response2 = curl_exec($ch2);
					if (curl_error($ch2)){
						echo 'Request Error: '. curl_error($ch2);
					}
					curl_close($ch2);
}

echo 'Send Completed';
}
function completed2(){
$officecode = $_REQUEST['officecode'];
$iddcrform = $_REQUEST['iddcrform'];
$officename = $_REQUEST['officename'];

	$command = "select * from dcrform where dcrform.iddcrform = $iddcrform";

$row=getrow($command);
if (!empty($row['email'])){
	
	
	$message = '
		<html>
		<head>
			<title>DAR XI Electronic Document Tracking</title>
		</head>
		<body>
			<h1><img src="https://darxi.net/DARLogo.jpg"></h1>
			<p>Hi '.$row['applicant'].',</p>
			<p>&nbsp;</p>
            <p>Please be informed that your application with tracking no: ' . $iddcrform . ' has been approved.  This e-mail shall serve as  your notice.  Please claim at ' . $officename . ' your DAR Land Transfer Clearance today. </p>
            <p>Please contact DAR Region XI for assistance and further details.</p> 
            <p>&nbsp;</p>
            <p>Very truly yours,</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>DAR XI Electronic Document Tracking Team</p>
            <p>This is a system generated email.  Please do not reply.<p>
            <p>&nbsp;</p>
            <div><i>This email and its attachments are intended solely for the addressee(s) as indicated above and may contain confidential and/or privileged information which may be legally protected from disclosure. If you are not the intended recipient or if this message has been addressed to you in error, please immediately alert the sender by reply email and then delete this message and its attachments. Please be advised that any use, dissemination, copying, or storage of this message or its attachments is strictly prohibited.</i></div>
		</body>
		</html>
	';
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	$headers .= 'From: DAR XI <info@darxi.net>' . "\r\n";
	$to = $row['email'];
	$subject = "DAR Document Tracking No.: " .$iddcrform;
	
	mail($to,$subject,$message,$headers);
	
	
	
}
if (!empty($row['cellno'])){
	$applicant = $row['applicant'];
					$ch2 = curl_init();
					$url2= "https://sms.davaocity.gov.ph/home/send";
					$numbers = $row['cellno'];
					$msg = "Good Day ".$applicant.", please be informed that your application with tracking number: ". $iddcrform ." has been approved.  This SMS shall serve as your notice.  Please claim from DAR ".$officename." your DAR Land Transfer Clearance."; 
					$data2 = http_build_query(array("cellno"=>$numbers,"message"=>$msg));
					$getUrl2 = $url2."?".$data2;
					curl_setopt($ch2,CURLOPT_SSL_VERIFYPEER,FALSE);
					curl_setopt($ch2,CURLOPT_FOLLOWLOCATION,TRUE);
					curl_setopt($ch2,CURLOPT_RETURNTRANSFER,TRUE);
					curl_setopt($ch2,CURLOPT_URL,$getUrl2);
					curl_setopt($ch2,CURLOPT_TIMEOUT,80);
					//$response2 = curl_exec($ch2);
					if (curl_error($ch2)){
						echo 'Request Error: '. curl_error($ch2);
					}
					curl_close($ch2);
}

echo 'Send Completed';
}
function disapproved2(){
$officecode = $_REQUEST['officecode'];
$iddcrform = $_REQUEST['iddcrform'];
$officename = $_REQUEST['officename'];

	$command = "select * from dcrform where dcrform.iddcrform = $iddcrform";

$row=getrow($command);
if (!empty($row['email'])){
	
	
	$message = '
		<html>
		<head>
			<title>DAR XI Electronic Document Tracking</title>
		</head>
		<body>
			<h1><img src="https://darxi.net/DARLogo.jpg"></h1>
			<p>Hi '.$row['applicant'].',</p>
			<p>&nbsp;</p>
            
			<p>Please be informed that your application with tracking no: ' . $iddcrform . ' has been disapproved due to incorrect or lacking of requirements.</div>" +
            <p>Please contact DAR Region XI for assistance and further details.</p>
			 
            <p>&nbsp;</p>
            <p>Very truly yours,</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>DAR XI Electronic Document Tracking Team</p>
            <p>This is a system generated email.  Please do not reply.<p>
            <p>&nbsp;</p>
            <div><i>This email and its attachments are intended solely for the addressee(s) as indicated above and may contain confidential and/or privileged information which may be legally protected from disclosure. If you are not the intended recipient or if this message has been addressed to you in error, please immediately alert the sender by reply email and then delete this message and its attachments. Please be advised that any use, dissemination, copying, or storage of this message or its attachments is strictly prohibited.</i></div>
		</body>
		</html>
	';
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	$headers .= 'From: DAR XI <info@darxi.net>' . "\r\n";
	$to = $row['email'];
	$subject = "DAR Document Tracking No.: " .$iddcrform;
	mail($to,$subject,$message,$headers);
	
	
}
if (!empty($row['cellno'])){
	$applicant = $row['applicant'];
					$ch2 = curl_init();
					$url2= "https://sms.davaocity.gov.ph/home/send";
					$numbers = $row['cellno'];
					$msg = "Good Day ".$applicant.", please be informed that your application with tracking number: ". $iddcrform ." has been disapproved due to incorrect or lacking of requirements."; 
					$data2 = http_build_query(array("cellno"=>$numbers,"message"=>$msg));
					$getUrl2 = $url2."?".$data2;
					curl_setopt($ch2,CURLOPT_SSL_VERIFYPEER,FALSE);
					curl_setopt($ch2,CURLOPT_FOLLOWLOCATION,TRUE);
					curl_setopt($ch2,CURLOPT_RETURNTRANSFER,TRUE);
					curl_setopt($ch2,CURLOPT_URL,$getUrl2);
					curl_setopt($ch2,CURLOPT_TIMEOUT,80);
					//$response2 = curl_exec($ch2);
					if (curl_error($ch2)){
						echo 'Request Error: '. curl_error($ch2);
					}
					curl_close($ch2);
}

echo 'Send Completed';
}
function disapproved3(){
$iddcrform = $_REQUEST['iddcrform'];
$officecode = $_REQUEST['officecode']." "." PROVINCIAL OFFICE";
$details = htmlspecialchars($_REQUEST['details'],ENT_QUOTES,"UTF-8");

	$command = "select * from dcrform where dcrform.iddcrform = $iddcrform";

$row=getrow($command);
if (!empty($row['email'])){
	
	
	$message = '
		<html>
		<head>
			<title>DAR XI Electronic Document Tracking</title>
		</head>
		<body>
			<h1><img src="https://darxi.net/DARLogo.jpg"></h1>
			<p>Hi '.$row['applicant'].',</p>
			<p>&nbsp;</p>
            
			<p>Please be informed that your application with tracking no: ' . $iddcrform . ' has been disapproved due to incorrect or lacking of requirements: </p>" +
            <p>'.$details.'</p>
			<p>Please contact DAR Region XI, '.$officecode.' for assistance and further details.</p>
			 
            <p>&nbsp;</p>
            <p>Very truly yours,</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>DAR XI Electronic Document Tracking Team</p>
            <p>This is a system generated email.  Please do not reply.<p>
            <p>&nbsp;</p>
            <div><i>This email and its attachments are intended solely for the addressee(s) as indicated above and may contain confidential and/or privileged information which may be legally protected from disclosure. If you are not the intended recipient or if this message has been addressed to you in error, please immediately alert the sender by reply email and then delete this message and its attachments. Please be advised that any use, dissemination, copying, or storage of this message or its attachments is strictly prohibited.</i></div>
		</body>
		</html>
	';
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	$headers .= 'From: DAR XI <info@darxi.net>' . "\r\n";
	$to = $row['email'];
	$subject = "DAR Document Tracking No.: " .$iddcrform;
	if (stristr(PHP_OS, 'WIN')) {} else 
		{mail($to,$subject,$message,$headers);}
	
	
}
if (!empty($row['cellno'])){
	$applicant = $row['applicant'];
					$ch2 = curl_init();
					$url2= "https://sms.davaocity.gov.ph/home/send";
					$numbers = $row['cellno'];
					$msg = "Good Day ".$applicant.", please be informed that your application with tracking number: ". $iddcrform ." has been disapproved due to incorrect or lacking of requirements: ".$details; 
					$data2 = http_build_query(array("cellno"=>$numbers,"message"=>$msg));
					$getUrl2 = $url2."?".$data2;
					curl_setopt($ch2,CURLOPT_SSL_VERIFYPEER,FALSE);
					curl_setopt($ch2,CURLOPT_FOLLOWLOCATION,TRUE);
					curl_setopt($ch2,CURLOPT_RETURNTRANSFER,TRUE);
					curl_setopt($ch2,CURLOPT_URL,$getUrl2);
					curl_setopt($ch2,CURLOPT_TIMEOUT,80);
					//$response2 = curl_exec($ch2);
					if (curl_error($ch2)){
						echo 'Request Error: '. curl_error($ch2);
					}
					curl_close($ch2);
}

echo 'Send Completed';
}
function completed3(){
$officecode = $_REQUEST['officecode'];
$iddcrform = $_REQUEST['iddcrform'];
$officename = $_REQUEST['officename'];

	$command = "select * from dcrform where dcrform.iddcrform = $iddcrform";

$row=getrow($command);
if (!empty($row['email'])){
	
	
	$message = '
		<html>
		<head>
			<title>DAR XI Electronic Document Tracking</title>
		</head>
		<body>
			<h1><img src="https://darxi.net/DARLogo.jpg"></h1>
			<p>Hi '.$row['applicant'].',</p>
			<p>&nbsp;</p>
            <p>Please be informed that your application with tracking no: ' . $iddcrform . ' has been approved.  This e-mail shall serve as  your notice.  Please claim at ' . $officename . ' your MARPO Certificate today. </p>
            <p>Please contact DAR Region XI for assistance and further details.</p> 
            <p>&nbsp;</p>
            <p>Very truly yours,</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>DAR XI Electronic Document Tracking Team</p>
            <p>This is a system generated email.  Please do not reply.<p>
            <p>&nbsp;</p>
            <div><i>This email and its attachments are intended solely for the addressee(s) as indicated above and may contain confidential and/or privileged information which may be legally protected from disclosure. If you are not the intended recipient or if this message has been addressed to you in error, please immediately alert the sender by reply email and then delete this message and its attachments. Please be advised that any use, dissemination, copying, or storage of this message or its attachments is strictly prohibited.</i></div>
		</body>
		</html>
	';
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	$headers .= 'From: DAR XI <info@darxi.net>' . "\r\n";
	$to = $row['email'];
	$subject = "DAR Document Tracking No.: " .$iddcrform;
	if (stristr(PHP_OS, 'WIN')) {} else 
		{mail($to,$subject,$message,$headers);}
	
	
	
}
if (!empty($row['cellno'])){
	$applicant = $row['applicant'];
					$ch2 = curl_init();
					$url2= "https://sms.davaocity.gov.ph/home/send";
					$numbers = $row['cellno'];
					$msg = "Good Day ".$applicant.", please be informed that your application with tracking number: ". $iddcrform ." has been approved.  This SMS shall serve as your notice.  Please claim from DAR ".$officename." your MARPO Certification."; 
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

echo 'Send Completed';
}
function notifyac(){
$officecode = $_REQUEST['officecode'];
$iddcrform = $_REQUEST['iddcrform'];
$officename = $_REQUEST['officename'];

	$command = "select * from dcrform where dcrform.iddcrform = $iddcrform";

$row=getrow($command);
if (!empty($row['email'])){
	
	
	$message = '
		<html>
		<head>
			<title>DAR XI Electronic Document Tracking</title>
		</head>
		<body>
			<h1><img src="https://darxi.net/DARLogo.jpg"></h1>
			<p>Hi '.$row['applicant'].',</p>
			<p>&nbsp;</p>
            <p>Please be informed that you may now submit the hard or printed copies of the documents you submitted electronically and pay a filling fee of Php2,000.00 on your application for Land Transfer Clearance with tracking no: ' . $iddcrform . ' at DAR ' . $officename . '.  You may schedule your appointment by visiting our site at https://darxi.net </p>
            <p>Please contact DAR Region XI for assistance and further details.  Please disregard this message if you already paid and submitted the original documents</p> 
            <p>&nbsp;</p>
            <p>Very truly yours,</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>DAR XI Electronic Document Tracking Team</p>
            <p>This is a system generated email.  Please do not reply.<p>
            <p>&nbsp;</p>
            <div><i>This email and its attachments are intended solely for the addressee(s) as indicated above and may contain confidential and/or privileged information which may be legally protected from disclosure. If you are not the intended recipient or if this message has been addressed to you in error, please immediately alert the sender by reply email and then delete this message and its attachments. Please be advised that any use, dissemination, copying, or storage of this message or its attachments is strictly prohibited.</i></div>
		</body>
		</html>
	';
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	$headers .= 'From: DAR XI <info@darxi.net>' . "\r\n";
	$to = $row['email'];
	$subject = "DAR Document Tracking No.: " .$iddcrform;
	if (stristr(PHP_OS, 'WIN')) {} else 
		{mail($to,$subject,$message,$headers);}
	
	
	
}
if (!empty($row['cellno'])){
	$applicant = $row['applicant'];
					$ch2 = curl_init();
					$url2= "https://sms.davaocity.gov.ph/home/send";
					$numbers = $row['cellno'];
					$msg = "Good Day ".$applicant.", please be informed that you may now submit the hard or printed copies of the documents you submitted electronically and pay a filling fee of Php2,000.00 on your application for Land Transfer Clearance with tracking no: " . $iddcrform . " at DAR " . $officename . ".  You may schedule your appointment by visiting our site at https://darxi.net. Please disregard this message if you already paid and submitted the original documents"; 
					//please be informed that your application with tracking number: ". $iddcrform ." has been approved.  This SMS shall serve as your notice.  Please claim from DAR ".$officename." your DAR Land Transfer Clearance."; 
					$data2 = http_build_query(array("cellno"=>$numbers,"message"=>$msg));
					$getUrl2 = $url2."?".$data2;
					curl_setopt($ch2,CURLOPT_SSL_VERIFYPEER,FALSE);
					curl_setopt($ch2,CURLOPT_FOLLOWLOCATION,TRUE);
					curl_setopt($ch2,CURLOPT_RETURNTRANSFER,TRUE);
					curl_setopt($ch2,CURLOPT_URL,$getUrl2);
					curl_setopt($ch2,CURLOPT_TIMEOUT,80);
					//$response2 = curl_exec($ch2);
					if (curl_error($ch2)){
						echo 'Request Error: '. curl_error($ch2);
					}
					curl_close($ch2);
}

echo 'Send Completed';
}
$trans='';
if (array_key_exists('trans', $_REQUEST)){
			$trans = $_REQUEST['trans'];
		}

if (empty($trans)){
	main1();
}
if ($trans=="completed"){
	completed();
}
if ($trans=="completed2"){
	completed2();
}
if ($trans=="completed3"){
	completed3();
}
if ($trans=="disapproved"){
	disapproved();
}
if ($trans=="disapproved2"){
	disapproved2();
}
if ($trans=="disapproved3"){
	disapproved3();
}
if ($trans=="notifyac"){
	notifyac();
}

?>