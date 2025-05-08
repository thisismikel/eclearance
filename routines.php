<?php
function getrow($command, $none=true){
	require "connect.php";
	$row = array();
	if ($result=$conn->query($command)){
		if ($none) {$row = $result->fetch_assoc();}
	} else $row = array('error');
	$conn->close();
	unset($conn);
	return $row;
}
function gettable($command){
	$table = array();
	require "connect.php";
	if ($result=$conn->query($command)){
		$table = $result->fetch_all(MYSQLI_ASSOC);
	} 
	$conn->close();
	unset($conn);
	return $table;
}
function getmultitable($command){
	$acom = explode(";",$command);
	require "connect.php";
	$tables = array();
	$i = 'test';
	$conn->multi_query($command);
	//die($acom[0]."-".$acom[1]."-".$acom[2]."-".$acom[3]);
	for ($x = 0; $x < count($acom)-1; $x++){
			if ($result=$conn->store_result()) {
				$tables[] = $result->fetch_all(MYSQLI_ASSOC);
			} else { $tables[] =array(error=>$acom[$x]);}
			$conn->next_result();
	}
	$conn->close();
	unset($conn);
	return $tables;
}
function getUserIpAddr(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        //ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
function savetk($userid, $fullname, $image, $office, $role, $email, $signature){
	$ip = getUserIpAddr();
	$ldate = date("Y-m-d");
	$command = "CALL inserttk($userid, '$fullname', '$image', '$office', '$role', '$email', '$ldate', '$ip','$signature')";
	$token = getrow($command);
	return $token;
}
function validatetoken(){
	$myobj = new stdClass();
	$token = $_REQUEST['tk'];
	
	if (!empty($token)){
	$ip = getUserIpAddr();
	$ldate = date("Y-m-d");
	$command = "select * from tk where idtk = $token and ip = '$ip' and ldate = '$ldate'";
	$data = getrow($command);
	$myobj->userid = $data['userid'];
	$myobj->fullname = $data['fullname'];
	$myobj->image=  $data['image'];
	$myobj->office = $data['office'];
	$myobj->role = $data['role'];
	$myobj->token = $token;
	$myobj->error = false;
	//$myobj->tkform = $data['tkform'];
	
} else {$myobj->error = true;}
return $myobj;
}
function gentkform($token, $userid){
		$command ="CALL gentkform('$token', $userid)";
		$row = getrow($command);
		$tkform = $row['@tkform'];
 return $tkform;
}
function cleartkform($token, $userid){
	$command = "CALL cleartkform('$token', $userid)";
	getrow($command,false);
}
date_default_timezone_set("Asia/Manila");
?>