<?php
require "routines.php";

function getdetails(){
	$officecode = $_REQUEST['officecode'];
	$command = "select *, (select office.officename from office where office.idoffice=dcrform.municipal) as municipalname, (select office.officename from office where office.idoffice=dcrform.provincial) as provincialname, (select office.officename from office where office.idoffice=dcrform.regional) as regionalname from trackform left join dcrform on trackform.iddcrform = dcrform.iddcrform where trackform.status='DISAPPROVAL' and trackform.officecode='$officecode' and trackform.enddate is null";
	$table->data = gettable($command);
	echo json_encode($table);
	
}


function appeals($userid){
	$idtrackform = $_REQUEST['idtrackform'];
	$iddcrform = $_REQUEST['iddcrform'];
	$regional = $_REQUEST['regional'];
	$officename = $_REQUEST['officename'];
	
	$details = htmlspecialchars($_REQUEST['details'],ENT_QUOTES,"UTF-8");
	$command = "select * from office where idoffice=$regional";
	$noffice = getrow($command);
	$officecode = $noffice['officecode'];
	$command ="CALL tagtrackform($idtrackform,'$details', $userid)";
	getrow($command,false);
	$command ="CALL inserttrackform('$officecode', 'APPEALS', '', $iddcrform, NULL, $userid)";
	getrow($command,false);
	$myobj->iddcrform=$iddcrform;
	$myobj->officecode=$officecode;
	$myobj->role='SECRETARIAT';
	$myobj->officename=$officename;
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


if ($trans=='appeals'){
	appeals($userid);
}



?>