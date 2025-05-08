<?php
require "routines.php";


function getdetails(){
	
	$command = "select dcrform.*,trackform.*, office.officename, (select office.officename from office where office.idoffice=dcrform.municipal) as municipalname, (select office.officename from office where office.idoffice=dcrform.provincial) as provincialname, (select office.officename from office where office.idoffice=dcrform.regional) as regionalname from trackform left join dcrform on trackform.iddcrform = dcrform.iddcrform left join office on trackform.officecode = office.officecode where trackform.enddate is null and trackform.status != 'COMPLETED'";
	$table->data = gettable($command);
	echo json_encode($table);
	
}

function getchart(){
	$command = "select officecode, count(*) as documents from trackform where trackform.enddate is null and trackform.status  != 'COMPLETED' group by trackform.officecode order by trackform.officecode";
	$table->chart = gettable($command);
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
if ($trans=='getchart'){
	getchart();
}


?>