<?php
require "routines.php";
$email=$_REQUEST['email'];
$message ='';
$myobj = new stdClass();
$myobj->userid = -1;
if (!empty($email)){
	$password=$_REQUEST['password'];
	$ip = $_SERVER['REMOTE_ADDR'];
	$curdate = date("Y-m-d");
	$command="CALL validateuser('$email', '$password', '$ip')";
	require "connect.php";
	
	if ($result=$conn->query($command)) {
		$row_cnt = $result->num_rows;
		if ($row_cnt>0) {
        	$row = $result->fetch_assoc();
			$myobj->userid = $row['userid'];
			$myobj->fullname = $row['fullname'];
			if (!empty($row['image'])){
				$myobj->image = $row['image'];
			} else {
				$myobj->image = 'person.jpg';
			}
			$myobj->email = $row['emailaddress'];
			$myobj->office = $row['office'];
			$myobj->role = $row['role'];
			$myobj->signature = $row['signature'];
			$conn->close();
			unset($conn);	
			
			$tk = savetk($myobj->userid, $myobj->fullname, $myobj->image, $myobj->office, $myobj->role, $myobj->email, $myobj->signature);
			$myobj->token = $tk['@id'];
		}
		
	} else { 
		$conn->close();
		unset($conn);
	}
} 

echo json_encode($myobj);
?>