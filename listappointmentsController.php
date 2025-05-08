<?php
require "routines.php";
function getdetails(){
	$officecode = $_REQUEST['officecode'];
	$command ="select appointment.*, schedule.scheduledate, users.fullname from appointment left join schedule on appointment.idschedule = schedule.idschedule left join office on schedule.idoffice = office.idoffice left join users on appointment.userid = users.userid where office.officecode = '$officecode'"; 
	$table->data = gettable($command);
	echo json_encode($table);
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
?>