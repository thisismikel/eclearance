<?php
require "routines.php";


function getdetails(){
	$table = new stdClass();
	$command = "select dcrform.*,trackform.*,dcrform.status as status2,(select office.officename from office where office.idoffice=dcrform.municipal) as municipalname, (select office.officename from office where office.idoffice=dcrform.provincial) as provincialname, (select office.officename from office where office.idoffice=dcrform.regional) as regionalname from trackform left join dcrform on trackform.iddcrform = dcrform.iddcrform left join office on dcrform.municipal = office.idoffice where trackform.status = 'COMPLETED' and dcrform.transaction = 'LANDClearance' and trackform.enddate is null";
	$table->data = gettable($command);
	echo json_encode($table);
	
}

function getchart(){
	$table = new stdClass();
	$command = "select office.officecode, count(*) as documents from trackform left join dcrform on trackform.iddcrform = dcrform.iddcrform left join office on dcrform.municipal = office.idoffice where trackform.status = 'COMPLETED' group by office.officecode order by office.officecode";
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