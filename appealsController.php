<?php
require "routines.php";

function getdetails(){
	$officecode = $_REQUEST['officecode'];
	$command = "select *, (select office.officename from office where office.idoffice=dcrform.municipal) as municipalname, (select office.officename from office where office.idoffice=dcrform.provincial) as provincialname, (select office.officename from office where office.idoffice=dcrform.regional) as regionalname from trackform left join dcrform on trackform.iddcrform = dcrform.iddcrform where trackform.status='APPEALS' and trackform.officecode='$officecode' and trackform.enddate is null";
	$table->data = gettable($command);
	echo json_encode($table);
	
}


$myobj = validatetoken();
if (empty($myobj->userid)){
    header('Location:index.html?message='.urlencode("Invalid User"));
} else {$userid = $myobj->userid;}
$trans = $_REQUEST['trans'];

if ($trans=='getdetails'){
	getdetails();
}


?>