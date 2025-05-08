<?php
require "routines.php";
function getattachments(){
	$keyname = $_REQUEST['keyname'];
	$keyvalue= $_REQUEST['keyvalue'];
	$command = "select * from attachments where keyname = '$keyname' and keyvalue = $keyvalue";
	require "connect.php";
	$data=array();
	if ($result=$conn->query($command)){
		$data = $result->fetch_all(MYSQLI_ASSOC);
	}
	$conn->close();
	unset($conn);
	$myJSON = json_encode($data);
	echo $myJSON;
	
}
function save($userid){
	$myObj = new stdClass();
	$keyname = $_POST['keyname'];
	$keyvalue = $_POST['keyvalue'];
	$label =$_POST['label'];
	$label = htmlspecialchars(strtoupper($label),ENT_QUOTES,"UTF-8");
    $file = $_FILES['imagefile'];
	$picture = $file['name'];
    if ($picture != ""){   
        $source =$file['tmp_name'];
		$stamp = getdate();
		$imagename = $stamp['year'].$stamp['mon'].$stamp['mday'].$stamp['hours'].$stamp['minutes'].$stamp['seconds']."_".$picture;
		$save = 'attachments/'.$imagename; //This is the new file you saving
		
		move_uploaded_file($source, $save);
		$check = 1;
		//$conn_id = ftp_connect('localhost'); 
		//$login_result = ftp_login($conn_id, "dcr", "DCR@2019"); 
		//if ((!$conn_id) || (!$login_result)) { $check = 0; $myObj->message1 = "Failed to login";}
		//if ($check == 1) {
		//	$upload = ftp_put($conn_id, $save, $source, FTP_BINARY); 
		//}
		//if (!$upload) { $check = 0; $myObj->message2 = "Failed to upload";} else {ftp_chmod($conn_id, 0775, $save);}
		//ftp_close($conn_id); 
		$label = $picture;
		$command = "CALL insertattachments('$keyname',$keyvalue, '$label', '$imagename',$userid)";
		$myObj->idattachments = -1;
		$myObj->imagename = $imagename;
		$myObj->label  = $label;
		if ($check == 1){
		require "connect.php";
		if ($result=$conn->query($command)){
			$row = $result->fetch_assoc();
			$myObj->idattachments = $row['@id'];
		}
		$conn->close();
		unset($conn);
		} else {$myObj->message = "Failed to save ".$save;}
		$myJSON = json_encode($myObj);
		echo $myJSON;
	}
}
function deleteimage($userid){
	$src = $_REQUEST['src'];
	$id = $_REQUEST['id'];
	$myObj->id = -1;
	require "connect.php";
	$command ="call deleteattachment($id,$userid)";
	if ($result=$conn->query($command)){
			$row = $result->fetch_assoc();
			$myObj->id = $row['@id'];
	}
	$conn->close();
	unset($conn);
	unlink($src);
	$myJSON = json_encode($myObj);
	echo $myJSON;
}
$myobj = validatetoken();
if (empty($myobj->userid)){
	//echo "error";
    header('Location:index.html?message='.urlencode("Invalid User"));
} else {$userid = $myobj->userid;}
$trans2 = $_REQUEST['trans2'];
if ($trans2=='attachment'){
	save($userid);
}
if ($trans2=='getattachments'){
	getattachments();
}
if ($trans2=='delimage'){
	deleteimage($userid);
}

?>
