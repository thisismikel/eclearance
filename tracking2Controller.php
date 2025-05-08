<?php
require "routines.php";


function getdetails(){
	$field = $_REQUEST['field'];
	if ($field=='trackingno'){
		$searchtxt = $_REQUEST['searchtxt'];
		$command = "select dcrform.*,trackform.*, office.officename, (select office.officename from office where office.idoffice=dcrform.municipal) as municipalname, (select office.officename from office where office.idoffice=dcrform.provincial) as provincialname, (select office.officename from office where office.idoffice=dcrform.regional) as regionalname from trackform left join dcrform on trackform.iddcrform = dcrform.iddcrform left join office on trackform.officecode = office.officecode where dcrform.iddcrform = $searchtxt and trackform.enddate is null";
	}
	else {
		$searchtxt = htmlspecialchars(strtoupper($_REQUEST['searchtxt']),ENT_QUOTES,"UTF-8").'%';
		$command = "select dcrform.*,trackform.*, office.officename, (select office.officename from office where office.idoffice=dcrform.municipal) as municipalname, (select office.officename from office where office.idoffice=dcrform.provincial) as provincialname, (select office.officename from office where office.idoffice=dcrform.regional) as regionalname from trackform left join dcrform on trackform.iddcrform = dcrform.iddcrform left join office on trackform.officecode = office.officecode where dcrform.applicant like '$searchtxt' and trackform.enddate is null";

	}
	
	$table->data = gettable($command);
	echo json_encode($table);
	
}


$myobj = validatetoken();
if (empty($myobj->userid)){
    header('Location:index.html?message='.urlencode("Invalid User"));
} else {$userid = $myobj->userid;}
$trans = $_REQUEST['trans'];


if ($trans=='searchtxt'){
	getdetails();
}

?>