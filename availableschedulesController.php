<?php
require "routines.php";
function getdetails(){
	$officecode = $_REQUEST['officecode'];
	$command ="select schedule.* from schedule left join office on schedule.idoffice = office.idoffice where office.officecode = '$officecode' and schedule.slots - schedule.availed > 0"; 
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