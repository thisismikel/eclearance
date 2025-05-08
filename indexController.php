<?php
require "routines.php";

function getapp(){
	$myobj = new stdClass();
	$iddcrform = $_REQUEST['trackingNo'];
	$transaction = $_REQUEST['transactiontype'];
	$command = "select trackform.startdate, trackform.enddate, trackform.status, office.officename ,if(trackform.status='COMPLETED',dcrform.status,'') as remarks from trackform left join dcrform on trackform.iddcrform= dcrform.iddcrform left join office on trackform.officecode = office.officecode where trackform.iddcrform = $iddcrform and dcrform.transaction ='$transaction' order by trackform.idtrackform desc";
	$myobj->data = gettable($command);
	echo json_encode($myobj);
	
}
function checkemail(){
	$myobj = new stdClass();
	$email = $_REQUEST['regemail'];
	$trans = $_REQUEST['req'];
	$command = "select * from users where emailaddress = '$email'";
	$row=getrow($command);
	$myobj->email = '';
	if (!empty($row['emailaddress'])){
		$myobj->email = $email;
		$myobj->fullname = $row['fullname'];
		$myobj->userid = $row['userid'];
		$myobj->address = $row['address'];
		$myobj->cellno = $row['cellno'];
		$token = savetk($row['userid'], $row['fullname'], $row['image'], $row['office'], $row['role'], $email,'');
		$myobj->token = $token['@id'];
		$myobj->trans = $trans;
		if ($trans == 'MARO'){
			$msg = "Hi ".$row['fullname']." your OTP for DAR XI MARO certification request is ".$myobj->token."."; 
			$title = "DAR XI MARO Certification";
			$subject = "DAR XI MARO Certification";
		}
		if ($trans == 'DARPO'){
			$msg = "Hi ".$row['fullname']." your OTP for DAR XI Land Transfer Clearance request is ".$myobj->token."."; 
			$title = "DAR XI Land Transfer Clearance";
			$subject = "DAR XI Land Transfer Clearance";
		}
		If ($trans == 'APPOINTMENT'){
			$msg = "Hi ".$row['fullname']." your OTP for DAR XI online appointment is ".$myobj->token."."; 
			$title = "DAR XI OPTN for Online Appointment";
			$subject = "DAR XI OPTN for Online Appointment";
		}
		if (!empty($email)){
			$receiver = $email;
			
			$fullname = $row['fullname'];
			//$idappointment = $_REQUEST['idappointment'];
			$to = $receiver;
			

			$message = '
				<html>
				<head><title>'.$title.'</title></head>
				<body>
					<h1><img src="https://darxi.net/DARLogo.jpg"></h1>
					<p>'.$msg.'</p>
					<p></p>
					<br/><br/><br/>
					Regards,<br/><br/>
					DAR XI
				</body>
				</html>';
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= 'From: DAR XI <noreply@darxi.net>' . "\r\n";
			if (stristr(PHP_OS, 'WIN')) {
				//mail($to,$subject,$message,$headers);
			} else {
				mail($to,$subject,$message,$headers);
			}
		}
		if (!empty($row['cellno'])){
	
					$ch2 = curl_init();
					$url2= "https://myYeastar.com/home/send";
					$numbers = $row['cellno'];
					
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
		
		
	} 
	echo json_encode($myobj);
}
function notifyclient(){
	$myobj = new stdClass();
	$email = $_REQUEST['regemail'];
	$trans = $_REQUEST['req'];
	$iddcrform = $_REQUEST['iddcrform'];
	$command = "select * from users where emailaddress = '$email'";
	$row=getrow($command);
	$myobj->email = '';
	if (!empty($row['emailaddress'])){
		$myobj->email = $email;
		$myobj->fullname = $row['fullname'];
		$myobj->userid = $row['userid'];
		$myobj->address = $row['address'];
		$myobj->cellno = $row['cellno'];
		$myobj->trans = $trans;
		if ($trans == 'MARO'){
			$maro = $_REQUEST['maro'];
			$msg = "Hi ".$row['fullname'].",  This is to acknowledge that you successfully requested for MARO Certfication at ".$maro.". Your MARO will notify you on the start of investigation of your request.  Your MARO will also notify you once your certification is ready for release.   Your tracking number is ".$iddcrform."."; 
			$title = "DAR XI MARO Certification";
			$subject = "DAR XI MARO Certification";
		}
		if ($trans == 'DARPO'){
			$darpo = $_REQUEST['darpo'];
			$msg = "Hi ".$row['fullname'].",  This is to acknowledge that you requested for Land Transfer Clearance online at ".$darpo.". Your DARPO will notify you for further instructions.   Your tracking number is ".$iddcrform."."; 
			$title = "DAR XI Request for Land Transfer Clearance";
			$subject = "DAR XI Request for Land Transfer Clearance";
		}
		if (!empty($email)){
			$receiver = $email;
			
			$fullname = $row['fullname'];
			//$idappointment = $_REQUEST['idappointment'];
			$to = $receiver;
			

			$message = '
				<html>
				<head><title>'.$title.'</title></head>
				<body>
					<h1><img src="https://darxi.net/DARLogo.jpg"></h1>
					<p>'.$msg.'</p>
					<p></p>
					<br/><br/><br/>
					Regards,<br/><br/>
					DAR XI
				</body>
				</html>';
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= 'From: DAR XI <noreply@darxi.net>' . "\r\n";
			if (stristr(PHP_OS, 'WIN')) {} else 
				{mail($to,$subject,$message,$headers);}
		}
		if (!empty($row['cellno'])){
	
					$ch2 = curl_init();
					$url2= "https://myYeastar.com/home/send";
					$numbers = $row['cellno'];
					
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
		
		
	} 
	echo json_encode($myobj);
}
function getschedules(){
	$idoffice = $_REQUEST['idoffice'];
	$command = "select * from schedule where slots > availed and idoffice = $idoffice and scheduledate > Now() order by scheduledate";
	$table->data = gettable($command);
	echo json_encode($table);
	
}
function saveschedule(){
	$userid = $_REQUEST['userid'];
	$idscheduledate = $_REQUEST['selschedule'];
	$scheduledate = $_REQUEST['xschedule'];
	$reciever = $_REQUEST['receiver'];
	$fullname = $_REQUEST['fullname'];
	$cellno = $_REQUEST['cellno'];
	$command ="CALL insertappointment($idscheduledate, $userid)";
	$row = getrow($command);
	$myobj->scheduledate = $scheduledate;
	$myobj->receiver = $receiver;
	$myobj->fullname = $fullname;
	$myobj->idappointment = $row['@id'];
	$myobj->idschedule = $idscheduledate;
	$myobj->cellno = $cellno;
	//$command ="CALL `updateappointment`($idappointment, $myobj->idappointment, 'Appointment schedule date transfered', $userid)";
	echo json_encode($myobj);
}
function regmail(){
	$myobj = new stdClass();
	$email = $_REQUEST['clientemail'];
	$fullname = htmlspecialchars(strtoupper($_REQUEST['clientfullname']),ENT_QUOTES,"UTF-8");
	$mobileno = htmlspecialchars(strtoupper($_REQUEST['clientmobileno']),ENT_QUOTES,"UTF-8");
	$address = 	htmlspecialchars(strtoupper($_REQUEST['clientaddress']),ENT_QUOTES,"UTF-8");
	$command = "call checkemail('$email')";
	$myobj->duplicate = 0;
	
	$row = getrow($command);
	
	if (!empty($row['emailaddress'])){
		$myobj->duplicate = 1;
	} else {
		  
		$row=savetk(2, 'The Administrator', '', 'XI', 'Administrator', 'info@darxi.net','');
		$tk=$row['@id'];
		$xlink = 'https://darxi.net/onlineconfirmation.php?fullname='.urlencode($fullname).'&mobileno='.urlencode($mobileno).'&address='.urlencode($address).'&tk='.$tk.'&email='.urlencode($email);
		$to = $email;
		$subject = "DAR XI Online Registration Confirmation";
		$message = '<html><head><title>DAR XI Online Registration Confirmationt</title></head><body>
		<h1><img src="https://darxi.net/DARLogo.jpg"></h1>
		<p>Hi '.$fullname.',</p>
		<p>This is your online registration confirmation.  Please <a href="'.$xlink.'" target="_blank">click here</a> to proceed on your registration.</p>
		<p></p>
		<br/><br/><br/>
		Regards,<br/><br/>
		DAR XI
		</body>
		</html>
		';
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= 'From: DAR XI <noreply@darxi.net>' . "\r\n";
		if (stristr(PHP_OS, 'WIN')) {} else 
			{mail($to,$subject,$message,$headers);}
		$myobj->link = $xlink;
	}
	$myobj->fullname=$fullname;
	echo json_encode($myobj);
}


$trans = $_REQUEST['trans'];

if ($trans=='getapp'){
	getapp();
}

if ($trans=='checkemail'){
	checkemail();
}
if ($trans=='getschedules'){
	getschedules();
}
if ($trans=='saveschedule'){
	saveschedule();
}
if ($trans=='regmail'){
	regmail();
}
if ($trans=='notifyclient'){
	notifyclient();
}

?>