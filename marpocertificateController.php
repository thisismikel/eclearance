<?php
require "routines.php";


function getdetails(){
	$table = new stdClass();
	$idoffice= $_REQUEST['idoffice'];
	$command = "select dcrform.*,trackform.*,dcrform.status as status2,(select office.officename from office where office.idoffice=dcrform.municipal) as municipalname, (select office.officename from office where office.idoffice=dcrform.provincial) as provincialname, (select office.officename from office where office.idoffice=dcrform.regional) as regionalname from trackform left join dcrform on trackform.iddcrform = dcrform.iddcrform left join office on dcrform.municipal = office.idoffice where trackform.status = 'COMPLETED' and dcrform.transaction = 'MAROCert' and trackform.enddate is null and dcrform.municipal = $idoffice";
	$table->data = gettable($command);
	echo json_encode($table);
	
}

function getchart(){
	$table = new stdClass();
	$command = "select office.officecode, count(*) as documents from trackform left join dcrform on trackform.iddcrform = dcrform.iddcrform left join office on dcrform.municipal = office.idoffice where trackform.status = 'COMPLETED' group by office.officecode order by office.officecode";
	$table->chart = gettable($command);
	echo json_encode($table);
}

function printmarpocert(){
	$iddcrform = $_REQUEST['iddcrform'];
	$command = "select * from dcrform  where dcrform.iddcrform = $iddcrform";
	$row = getrow($command);
	$filename = 'marpo/'.$row['marpocert'];;
//die($filename);
    $fileinfo = pathinfo($filename);
    $sendname = $fileinfo['filename'] . '.' . strtoupper($fileinfo['extension']);

    header('Content-Type: application/pdf');
    header('Content-Length: ' . filesize($filename));
    readfile($filename);
}

function getidoffice(){
	$myobj = new stdClass();
	$officecode = $_REQUEST['officecode'];
	$command = "select * from office where officecode = '$officecode'";
	$row = getrow($command);
	$myobj->idoffice = $row['idoffice'];
	echo json_encode($myobj);
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
if ($trans=='printmarpocert'){
	printmarpocert();
}
if ($trans=='getidoffice'){
	getidoffice();
}


?>