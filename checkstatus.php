<?php
session_start();
require "routines.php";
$myobj = new stdClass();
$myobj->userid = -1;
$token = $_REQUEST['token'];

if (!empty($token)){
	$ip = getUserIpAddr();
	$ldate = date("Y-m-d");
	$command = "select * from tk where idtk = $token and ip = '$ip' and ldate = '$ldate'";
	$data = getrow($command);
	$myobj->userid = $data['userid'];
	$myobj->fullname = $data['fullname'];
	$myobj->image=  $data['image'];
	$myobj->office = $data['office'];
	$myobj->signature = $data['signature'];
	$myobj->role = $data['role'];
	//$myobj->sylist = json_decode($data['sylist'], true);;
	$myobj->token = $token;
	
}
echo json_encode($myobj);
?>