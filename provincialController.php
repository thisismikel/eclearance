<?php
require "routines.php";
function insertdetails($userid){
	$myobj = new stdClass();
	$idofficemunicipal = $_REQUEST['municipal'];
	$idofficeprovincial = $_REQUEST['idofficeprovincial'];
	$command ="CALL insertprovincialoffice($idofficeprovincial, $idofficemunicipal, $userid)";
	$result = getrow($command);
	$myobj->idofficemunicipal = $result['@id'];
	$myobj->idofficeprovincial = $idofficeprovincial;
	$myobj->trans='ADD';
	echo json_encode($myobj);
}

function getdetails(){
	$table = new stdClass();
	$command = "select * from office where officetype ='PROVINCIAL' order by `officecode`";
	$table->data = gettable($command);
	
	echo json_encode($table);
	
}
function getchilddetails(){
	$table = new stdClass();
	$idoffice = $_REQUEST['idoffice'];
	$command = "select provincialoffice.*, office.officename, office.officecode from provincialoffice left join office on provincialoffice.idofficemunicipal = office.idoffice where idofficeprovincial=$idoffice order by `officecode`";
	$table->data = gettable($command);
	
	echo json_encode($table);
	
}
function getmunicipal(){
	$table = new stdClass();
	$idofficeprovincial = $_REQUEST['idofficeprovincial'];
	$command = "select * from office where officetype ='MUNICIPAL' and idoffice  not in (select provincialoffice.idofficemunicipal from provincialoffice) order by `officename`";
	$table->municipal = gettable($command);
	$table->idofficeprovincial=$idofficeprovincial;
	echo json_encode($table);
}

function deletedetail($userid){
	$myobj = new stdClass();
	$idprovincialoffice = $_REQUEST['idprovincialoffice'];
	$idofficeprovincial = $_REQUEST['idofficeprovincial'];
	$command = "CALL deleteprovincialoffice($idprovincialoffice, $userid)";
	$myobj->idofficeprovincial=$idofficeprovincial;
	getrow($command,false);
	echo json_encode($myobj);
}
$myobj = validatetoken();
if (empty($myobj->userid)){
    header('Location:index.html?message='.urlencode("Invalid User"));
} else {$userid = $myobj->userid;}
$trans = $_REQUEST['trans'];
if ($trans=='ADD'){
	insertdetails($userid);
}

if ($trans=='getdetails'){
	getdetails();
}
if ($trans=='delete'){
	deletedetail($userid);
}
if ($trans=='getmunicipal'){
	getmunicipal();
}
if ($trans=='getchilddetails'){
	getchilddetails();
}
?>