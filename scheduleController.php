<?php
require "routines.php";
function getdetails(){
	$officecode = $_REQUEST['officecode'];
	$command ="select schedule.*, office.officecode from schedule left join office on schedule.idoffice = office.idoffice where office.officecode = '$officecode'"; 
	$table->data = gettable($command);
	echo json_encode($table);
}
function getofficename($officecode){
	$command = "select * from office where officecode = '$officecode'";
	$row = getrow($command);
	echo json_encode($row);
}
function insertschedule($userid){
	$idoffice = $_REQUEST['idoffice'];
	$scheduledate = $_REQUEST['scheduledate'];
	$slots = $_REQUEST['slots'];
	$updatedby = $_REQUEST['fullname'];
	$command ="CALL insertschedule($idoffice, '$scheduledate', $slots, '$updatedby', $userid)";
	$row = getrow($command);
	$myobj->idschedule = $row['@id'];
	$myobj->scheduledate = $scheduledate;
	echo json_encode($myobj);
	

}
function updateschedule($userid){
	$idschedule = $_REQUEST['idschedule'];
	$idoffice = $_REQUEST['idoffice'];
	$scheduledate = $_REQUEST['scheduledate'];
	$slots = $_REQUEST['slots'];
	$updatedby = $_REQUEST['fullname'];
	$command ="CALL updateschedule($idschedule, '$scheduledate', $slots, '$updatedby', $userid)";
	//die($command);
	getrow($command, false);
	$myobj->idschedule = $idschedule;
	$myobj->scheduledate = $scheduledate;
	echo json_encode($myobj);
	

}
function deleteschedule($userid){
	$idschedule=$_REQUEST['idschedule'];
	$command = "Call deleteschedule($idschedule, $userid)";
	getrow($command,false);
	$myobj->idschedule=$idschedule;
	echo json_encode($myobj);
	
}
function getappointments(){
	$idschedule = $_REQUEST['idschedule'];
	$command ="select appointment.*, users.fullname, users.emailaddress, users.cellno from appointment left join users on appointment.userid = users.userid where appointment.idschedule = $idschedule"; 
	$table->data = gettable($command);
	echo json_encode($table);

}
function saveschedule($userid2){
	$userid = $_REQUEST['userid'];
	$selschedule = $_REQUEST['selschedule'];
	$idschedule = $_REQUEST['idschedule'];
	$scheduledate = $_REQUEST['xschedule'];
	$cellno = $_REQUEST['cellno'];
	$receiver = $_REQUEST['receiver'];
	$fullname = $_REQUEST['fullname'];
	$previdappointment = $_REQUEST['previdappointment'];
	$command ="CALL insertappointment($selschedule, $userid)";
	$row = getrow($command);
	$myobj->scheduledate = $scheduledate;
	$myobj->receiver = $receiver;
	$myobj->fullname = $fullname;
	$myobj->idappointment = $row['@id'];
	$myobj->idschedule = $idschedule;
	$myobj->cellno = $cellno;
	$command = "CALL updateappointment($previdappointment, $myobj->idappointment, 'Appointment schedule date transfered',$userid2)";
	$row = getrow($command,false);
	echo json_encode($myobj);
}
$myobj = validatetoken();
if (empty($myobj->userid)){
    header('Location:index.html?message='.urlencode("Invalid User"));
} else {
	$userid = $myobj->userid;
	$officecode = $myobj->office;
}
$trans = $_REQUEST['trans'];

if ($trans=='getdetails'){
	getdetails();
}
if ($trans=='getappointments'){
	getappointments();
}
if ($trans=='getofficename'){
	getofficename($officecode);
}
if ($trans=='ADD'){
	insertschedule($userid);
}
if ($trans=='saveschedule'){
	saveschedule($userid);
}
if ($trans=='UPDATE'){
	updateschedule($userid);
}
if ($trans=='DELETE'){
	deleteschedule($userid);
}
?>