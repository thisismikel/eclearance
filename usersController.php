<?php
require "routines.php";
function getmenu(){
	$string = file_get_contents("menu.json");
	$json_a = json_decode($string, true);
	echo json_encode($json_a);
}
function priviledges(){
	$userid = $_REQUEST['userid'];
	$token = $_REQUEST['tk'];
	$command = "select * from access where userid=$userid";
	$myobj = new stdClass();
	$myobj->userid = $userid;
	$myobj->token = $token;
	$string = file_get_contents("menu.json");
	$myobj->menu = $string;
	$json_a = json_decode($string, true);
	$myobj->menu = $json_a;
	$myobj->access = gettable($command);
	
	echo json_encode($myobj);	
}
function insertpriviledges($userid){
	$menu = $_REQUEST['menu'];
	$userid1 = $_REQUEST['userid'];
	$command = "DELETE FROM access WHERE userid = $userid1 and idaccess > 0";
	$myobj = new stdClass();
	$myobj->id = 1;
	$r = getrow($command,false);
	foreach ($menu as $title){
		$title2 = htmlspecialchars($title,ENT_QUOTES,"UTF-8");
		$command = "CALL insertaccess($userid1, '$title2', $userid)";
		$r = getrow($command,false);
		
	}
	
	echo json_encode($myobj);
}

function insertdetails($userid){
	$fullname=strtoupper($_REQUEST['fullname']);
    $fullname=htmlspecialchars($fullname,ENT_QUOTES, "UTF-8");
    $remarks=strtoupper($_REQUEST['remarks']);
    $remarks=htmlspecialchars($remarks,ENT_QUOTES, "UTF-8");
    $emailaddress =$_REQUEST['emailaddress'];
    $password =htmlspecialchars($_REQUEST['password'],ENT_QUOTES, "UTF-8");
    $role=strtoupper($_REQUEST['role']);      
    $role=htmlspecialchars($role,ENT_QUOTES,"UTF-8");
    $location=strtoupper($_REQUEST['office']);
    $cellno=htmlspecialchars($_REQUEST['cellno'],ENT_QUOTES,"UTF-8");
	$address=htmlspecialchars($_REQUEST['address'],ENT_QUOTES,"UTF-8");
	$image=$_REQUEST['image1'];
	$sigimage=$_REQUEST['sig1'];
	$file = $_FILES['imagefile'];
	$sigfile = $_FILES['sigfile'];
	$picture = $file['name'];
	$signature = $sigfile['name'];
    if ($picture != ""){   
        $source =$file['tmp_name'];
		$stamp = getdate();
		$imagename = $stamp['year'].$stamp['mon'].$stamp['mday'].$stamp['hours'].$stamp['minutes'].$stamp['seconds']."_".$picture;
		$save = "userimages/" . $imagename; //This is the new file you saving
		move_uploaded_file($source, $save);
		$check = 1;
		//$conn_id = ftp_connect(localhost); 
		//$login_result = ftp_login($conn_id, "dcr", "DCR@2019"); 
		//if ((!$conn_id) || (!$login_result)) { $check = 0;}
		//if ($check == 1) {
		//	$upload = ftp_put($conn_id, $save, $source, FTP_BINARY); 
		//}
		$image = $imagename;
		//if (!$upload) { $check = 0;$image = '';}
		//ftp_close($conn_id);
	} else {$image='';}
	if ($signature != ""){   
        $source =$sigfile['tmp_name'];
		$stamp = getdate();
		$signature = $stamp['year'].$stamp['mon'].$stamp['mday'].$stamp['hours'].$stamp['minutes'].$stamp['seconds']."_".$signature;
		$save = "signatures/" . $signature; //This is the new file you saving
		move_uploaded_file($source, $save);
		$check = 1;
		
	} else {$signature='';}
	$command = "CALL adduser('$password','$fullname', '$role', '$remarks', '$location', '$cellno', '$emailaddress','$image', '$signature','$address',$userid)";
	$myobj->userid = -1;
	require "connect.php";
	//die($command);
	if ($result=$conn->query($command)) {
		$row = $result->fetch_assoc();
		$myobj = new stdClass();
		$myobj->userid = $row['@id'];
		$myobj->fullname = $fullname;
		$myobj->role=$role;
		$myobj->active='Y';
		$myobj->remarks=$remarks;
		$myobj->location=$location;
		$myobj->cellno=$cellno;
		$myobj->emailaddress=$emailaddress;
		$myobj->image=$image;
		$myobj->signature =$signature;
		$myobj->ip='';
		$myobj->login='';
		$myobj->image=$image;
		$myobj->trans='ADD';
	} 
	$conn->close();
	unset($conn);
	echo json_encode($myobj);
}
function updatedetails($userid1){
	$userid = $_REQUEST['userid'];
	$password = htmlspecialchars($_REQUEST['password'],ENT_QUOTES,"UTF-8");
	$fullname = htmlspecialchars(strtoupper($_REQUEST['fullname']),ENT_QUOTES,"UTF-8");
	$role=htmlspecialchars(strtoupper($_REQUEST['role']),ENT_QUOTES,"UTF-8");
	$active=$_REQUEST['active'];
	$remarks=$_REQUEST['remarks'];
	$location=$_REQUEST['office'];
	$cellno=$_REQUEST['cellno'];
	$address=htmlspecialchars($_REQUEST['address'],ENT_QUOTES,"UTF-8");
	$image=$_REQUEST['image1'];
	$sigimage=$_REQUEST['sig1'];
	$file = $_FILES['imagefile'];
	$sigfile = $_FILES['sigfile'];
	$picture = $file['name'];
	$signature = $sigfile['name'];
	
    if ($picture != ""){   
        $source =$file['tmp_name'];
		$stamp = getdate();
		$imagename = $stamp['year'].$stamp['mon'].$stamp['mday'].$stamp['hours'].$stamp['minutes'].$stamp['seconds']."_".$picture;
		$save = "userimages/" . $imagename; //This is the new file you saving
		move_uploaded_file($source, $save);
		$check = 1;
		//$save = "html/userimages/" . $imagename;
		//$conn_id = ftp_connect($_SERVER['HTTP_HOST']); 
		//$login_result = ftp_login($conn_id, "recovery", "recovery@2021"); 
		//if ((!$conn_id) || (!$login_result)) { $check = 0;}
		//if ($check == 1) {
			//$upload = ftp_put($conn_id, $save, $source, FTP_BINARY); 
		//}
		$image = $imagename;
		//if (!$upload) { $check = 0;$image = '';}
		//ftp_close($conn_id);
	} else {
		$image = str_replace("userimages/","", $image);
	}
	if ($signature != ""){   
        $source =$sigfile['tmp_name'];
		$stamp = getdate();
		$signature = $stamp['year'].$stamp['mon'].$stamp['mday'].$stamp['hours'].$stamp['minutes'].$stamp['seconds']."_".$signature;
		$save = "signatures/" . $signature; //This is the new file you saving
		move_uploaded_file($source, $save);
		$check = 1;
		
	} else {$signature=str_replace("signatures/","", $sigimage);}
	if (empty($password)) {
		$command = "CALL edituser('$fullname', '$role', '$remarks', '$location', '$cellno','$active','$image','$signature' ,'$address', $userid ,$userid1)";
	} else {	
		$command = "CALL edituser2('$password', '$fullname', '$role', '$remarks', '$location', '$cellno','$active', '$image','$signature','$address', $userid, $userid1)";
	}
	$myobj = new stdClass();
	$myobj->userid = -1;
	require "connect.php";
	//die($command);
	if ($result=$conn->query($command)) {
		$myobj->userid = $userid;
		$myobj->fullname = $fullname;
		$myobj->role=$role;
		$myobj->active='Y';
		$myobj->remarks=$remarks;
		$myobj->location=$location;
		$myobj->cellno=$cellno;
		$myobj->image=$image;
		$myobj->signature=$signature;
		$myobj->trans ='UPDATE';
	} 
	$conn->close();
	unset($conn);
	echo json_encode($myobj);

}
function getdetails(){
	$table = new stdClass();
	$command = "select users.*, office.officename, office.officecode as office from users left join office on users.office = office.officecode order by fullname";
	$table->data = gettable($command);
	echo json_encode($table);
	
	
}
function getoffices(){
	$table = new stdClass();
	$command = "select * from office where active = 'Y' order by `officecode`";
	$table->data = gettable($command);
	echo json_encode($table);
	
}
function getprofile(){
	$myobj = new stdClass();
	$tk = $_REQUEST['tk'];
	$command ="select userid from tk where idtk = $tk";
	$myobj->result = getrow($command);
	$userid = $myobj->result['userid'];
	$command = "select * from users where userid = $userid";
	$myobj->user = getrow($command);
	echo json_encode($myobj);
}

function getmaro(){
	$table = new stdClass();
	$command = "SELECT municipalbrgy.*, office.officename, office.idoffice, provincialoffice.idofficeprovincial FROM municipalbrgy left join office on municipalbrgy.officecode = office.officecode left join provincialoffice on office.idoffice = provincialoffice.idofficemunicipal order by municipalbrgy.officecode, municipalbrgy.barangay";
	//"SELECT * FROM office where officetype = 'MUNICIPAL' and active = 'Y'";
	$table->data = gettable($command);
	echo json_encode($table);
}
function getprovincial(){
	$table = new stdClass();
	$command = "SELECT office.officename, office.idoffice, office.officecode FROM  office where office.officetype = 'PROVINCIAL' order by office.officename";
	//"SELECT * FROM office where officetype = 'MUNICIPAL' and active = 'Y'";
	$table->data = gettable($command);
	echo json_encode($table);
}

$myobj = validatetoken();
if (empty($myobj->userid)){
    header('Location:index.html?message='.urlencode("Invalid User"));
} else {$userid = $myobj->userid;}
$trans = $_REQUEST['trans'];
if ($trans=='ADD'){
	insertdetails($userid);
}
if ($trans=='UPDATE'){
	updatedetails($userid);
}
if ($trans=='getdetails'){
	getdetails();
}
if ($trans=='getmaro'){
	getmaro();
}
if ($trans=='getprovincial'){
	getprovincial();
}
if ($trans=='getmenu'){
	getmenu();
}
if ($trans=='priviledges') {
	priviledges();
}
if ($trans=='access'){
	insertpriviledges($userid);
}
if ($trans=='getoffices'){
	getoffices();
}
if ($trans=='getprofile'){
	getprofile();
}
?>