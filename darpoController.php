<?php
require "routines.php";

function getdetails(){
	$table = new stdClass();
	$officecode = $_REQUEST['officecode'];
	$command = "select *, (select office.officename from office where office.idoffice=dcrform.municipal) as municipalname, (select office.officename from office where office.idoffice=dcrform.provincial) as provincialname, (select office.officename from office where office.idoffice=dcrform.regional) as regionalname from trackform left join dcrform on trackform.iddcrform = dcrform.iddcrform where trackform.status='DARPO' and trackform.officecode='$officecode' and trackform.enddate is null";
	$table->data = gettable($command);
	echo json_encode($table);
	
}
function getoffices(){
	$myobj = new stdClass();
	$officecode = $_REQUEST['officecode'];
	$command = "select * from office where officecode = '$officecode'";
	$provincial = getrow($command);
	$myobj->error = false;
	if (empty($provincial)){
		$myobj->error = true;
	} else {
		if ($provincial['officetype']!='PROVINCIAL'){
			$myobj->error = true;
		}
	}
	//die('province:'.$provincial['officetype']);
	if (!$myobj->error){
		$myobj->provincial = $provincial;
		$idofficeprovincial = $provincial['idoffice'];
		//$myobj->idofficemunicipal = $idofficemunicipal;
		$command = "select office.* from provincialoffice left join office on provincialoffice.idofficemunicipal = office.idoffice where provincialoffice.idofficeprovincial = $idofficeprovincial";
		$municipal = gettable($command);
		//$idofficeprovincial = $row['idofficeprovincial'];
		//$command = "select * from office where idoffice = $idofficeprovincial";
		//$provincial = getrow($command);
		//$myobj->provincial = $provincial;
		$myobj->municipal = $municipal;
		//$myobj->idofficemunicipal = $idofficemunicipal;
		$myobj->idofficeprovincial = $idofficeprovincial;
		$myobj->idofficeregional = 52;
		$command = "select * from office where idoffice = 52";
		$regional = getrow($command);
		$myobj->regional = $regional;
	}
	
	echo json_encode($myobj);
}
function updatetrack($userid){
	$myobj = new stdClass();
	$officecode = $_REQUEST['officecode'];
	$iddcrform = $_REQUEST['iddcrform'];
	$command ="CALL inserttrackform('$officecode', 'DARPO', '', $iddcrform, NULL, $userid)";
	$result = getrow($command);
	$myobj->result = "Ok";
	echo json_encode($myobj);
}
function savedetails($userid){
	$myobj = new stdClass();
	$details = htmlspecialchars($_REQUEST['details'],ENT_QUOTES,"UTF-8");
	$idtrackform = $_REQUEST['idtrackform'];
	$command ="CALL updatedetails($idtrackform , '$details', $userid)";
	getrow($command,false);
	$myobj->idtrackform=$idtrackform;
	echo json_encode($myobj);
}
function seen($userid){
	$myobj = new stdClass();
	$idtrackform = $_REQUEST['idtrackform'];
	$command ="CALL seen($idtrackform,$userid)";
	$result = getrow($command);
	$myobj->seen = $result['@seen'];
	echo json_encode($myobj);

}
function getcase(){
	$myobj = new stdClass();
	$iddcrform = $_REQUEST['iddcrform'];
	$municipal = $_REQUEST['municipal'];
	$regional = $_REQUEST['regional'];
	$provincial = $_REQUEST['provincial'];
	$command = "select trackform.*, users.fullname, (select officename from office where office.officecode = trackform.officecode) as officename,(select users.fullname from users where users.userid = trackform.seenby) as firstseen from trackform left join users on trackform.userid = users.userid where iddcrform =  $iddcrform order by idtrackform" ;
	
	$myobj->trackform = gettable($command);
	$myobj->municipal = $municipal;
	$myobj->regional = $regional;
	$myobj->provincial =$provincial;
	echo json_encode($myobj);
}

function forDARMO($userid){
	$myobj = new stdClass();
	$idtrackform = $_REQUEST['idtrackform'];
	$iddcrform = $_REQUEST['iddcrform'];
	$municipal = $_REQUEST['municipal'];
	$officename = $_REQUEST['officename'];
	$details = htmlspecialchars($_REQUEST['details'],ENT_QUOTES,"UTF-8");
	$command = "select * from office where idoffice=$municipal";
	$noffice = getrow($command);
	$officecode = $noffice['officecode'];
	$command ="CALL tagtrackform($idtrackform,'$details', $userid)";
	getrow($command,false);
	$command ="CALL inserttrackform('$officecode', 'DARMO', '', $iddcrform, NULL, $userid)";
	
	getrow($command,false);
	$myobj->iddcrform=$iddcrform;
	$myobj->officecode=$officecode;
	$myobj->officename=$officename;
	$myobj->role='DARMO';
	echo json_encode($myobj);
}
function forLegal($userid){
	$myobj = new stdClass();
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
	$command ="CALL inserttrackform('$officecode', 'LEGAL', '', $iddcrform, NULL, $userid)";
	
	getrow($command,false);
	$myobj->iddcrform=$iddcrform;
	$myobj->officecode=$officecode;
	$myobj->officename=$officename;
	$myobj->role='LEGAL';
	echo json_encode($myobj);
}
function forAmend($userid){
	$myobj = new stdClass();
	$idtrackform = $_REQUEST['idtrackform'];
	$iddcrform = $_REQUEST['iddcrform'];
	$provincial = $_REQUEST['provincial'];
	$officename = $_REQUEST['officename'];
	$details = htmlspecialchars($_REQUEST['details'],ENT_QUOTES,"UTF-8");
	$command = "select * from office where idoffice=$provincial";
	$noffice = getrow($command);
	$officecode = $noffice['officecode'];
	$command ="CALL tagtrackform($idtrackform,'$details', $userid)";
	getrow($command,false);
	$command ="CALL inserttrackform('$officecode', 'AMEND', '', $iddcrform, NULL, $userid)";
	getrow($command,false);
	$myobj->iddcrform=$iddcrform;
	$myobj->officecode=$officecode;
	$myobj->officename=$officename;
	$myobj->role='SECRETARIAT';
	echo json_encode($myobj);
}
function forPARO($userid){
	$myobj = new stdClass();
	$idtrackform = $_REQUEST['idtrackform'];
	$iddcrform = $_REQUEST['iddcrform'];
	$provincial = $_REQUEST['provincial'];
	$officename = $_REQUEST['officename'];
	$details = htmlspecialchars($_REQUEST['details'],ENT_QUOTES,"UTF-8");
	$command = "select * from office where idoffice=$provincial";
	$noffice = getrow($command);
	$officecode = $noffice['officecode'];
	$command ="CALL tagtrackform($idtrackform,'$details', $userid)";
	getrow($command,false);
	$command ="CALL inserttrackform('$officecode', 'PARO', 'FOR PARO APPROVAL', $iddcrform, NULL, $userid)";
	
	getrow($command,false);
	$myobj->iddcrform=$iddcrform;
	$myobj->officecode=$officecode;
	$myobj->officename=$officename;
	$myobj->role='PARO';
	echo json_encode($myobj);
}
function forCHIEFLEGAL($userid){
	$myobj = new stdClass();
	$idtrackform = $_REQUEST['idtrackform'];
	$iddcrform = $_REQUEST['iddcrform'];
	$provincial = $_REQUEST['provincial'];
	$officename = $_REQUEST['officename'];
	$details = htmlspecialchars($_REQUEST['details'],ENT_QUOTES,"UTF-8");
	$command = "select * from office where idoffice=$provincial";
	$noffice = getrow($command);
	$officecode = $noffice['officecode'];
	$command ="CALL tagtrackform($idtrackform,'$details', $userid)";
	getrow($command,false);
	$command ="CALL inserttrackform('$officecode', 'CHIEFLEGAL', 'FOR CHIEF LEGAL RECOMMENDATION', $iddcrform, NULL, $userid)";
	
	getrow($command,false);
	$myobj->iddcrform=$iddcrform;
	$myobj->officecode=$officecode;
	$myobj->officename=$officename;
	$myobj->role='CHIEFLEGAL';
	echo json_encode($myobj);
}
function forCARPO($userid){
	$myobj = new stdClass();
	$idtrackform = $_REQUEST['idtrackform'];
	$iddcrform = $_REQUEST['iddcrform'];
	$provincial = $_REQUEST['provincial'];
	$officename = $_REQUEST['officename'];
	$details = htmlspecialchars($_REQUEST['details'],ENT_QUOTES,"UTF-8");
	$command = "select * from office where idoffice=$provincial";
	$noffice = getrow($command);
	$officecode = $noffice['officecode'];
	$command ="CALL tagtrackform($idtrackform,'$details', $userid)";
	getrow($command,false);
	$command ="CALL inserttrackform('$officecode', 'CARPO', 'FOR CARPO INVESTIGATION', $iddcrform, NULL, $userid)";
	
	getrow($command,false);
	$myobj->iddcrform=$iddcrform;
	$myobj->officecode=$officecode;
	$myobj->officename=$officename;
	$myobj->role='CARPO';
	echo json_encode($myobj);
}
function appeals($userid){
	$myobj = new stdClass();
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
	$command ="CALL inserttrackform2('$officecode', 'APPEALS','DARPO', '', $iddcrform, NULL, $userid)";
	getrow($command,false);
	$myobj->iddcrform=$iddcrform;
	$myobj->officecode=$officecode;
	$myobj->role='SECRETARIAT';
	$myobj->officename=$officename;
	echo json_encode($myobj);
}
function disapproveclearance($userid){
	$myobj = new stdClass();
	$idtrackform = $_REQUEST['idtrackform'];
	$iddcrform = $_REQUEST['iddcrform'];
	$officecode = $_REQUEST['officecode'];
	$details = htmlspecialchars($_REQUEST['details'],ENT_QUOTES,"UTF-8");
	$command ="CALL tagtrackform($idtrackform,'$details', $userid)";
	getrow($command,false);
	$command ="CALL inserttrackform('$officecode', 'COMPLETED', '', $iddcrform, NULL, $userid)";
	getrow($command,false);
	$command = "CALL tagdisapproved($iddcrform, $userid)";
	getrow($command,false);
	$myobj->iddcrform=$iddcrform;
	$myobj->officecode=$officecode;
	echo json_encode($myobj);
}
function DARPO(){
	$myobj = new stdClass();
	$address = $_REQUEST['address'];
	$applicant = $_REQUEST['fullname'];
	$referenceno = '';
	$datefiled = date("Y-m-d");
	$urgent ='';
	$email = $_REQUEST['email'];
	$cellno = $_REQUEST['cellno'];
	$telno ='';
	$transaction = "LANDClearance";
	$details="Request for Land Transfer Clearance";
	$preparedby = $_REQUEST['fullname'];
	$amortization ='';
	$nia='';
	$loancert ='';
	$taxclearance ='';
	$affidavittransferor='';
	$affidavitaggregate='';
	$taxreturn='';
	$rescert='';
	$assessor='';
	$municipal=0;
	$provincial=$_REQUEST['selProvincial'];
	$regional=52;
	$propertylocation = htmlspecialchars(strtoupper($_REQUEST['propertylocation']),ENT_QUOTES,"UTF-8");
	$latitude=0;
	$longtitude=0;
	$registeredowners='';
	$titleno='';
	$area=0;
	$tdno='';
	$instrument='';
	$transferor='';
	$transferees='';
	$placeconveyance='';
	$docno='';
	$pageno='';
	$bookno='';
	$yrnotarized=0;
	$notarypublic='';
	$userid = $_REQUEST['userid'];
	$officecode=$_REQUEST['officecode'];
	$command ="CALL insertdcrform('$address', '$applicant', '$referenceno', '$datefiled', '$urgent', '$email', '$cellno', '$telno', '$transaction',";
	$command .= "'$details', '$preparedby', '$amortization', '$nia','$loancert', '$taxclearance', '$affidavittransferor', '$affidavitaggregate', '$taxreturn',";
    $command .=	"'$rescert', '$assessor', $municipal, $provincial, $regional, '$propertylocation',$longtitude,$latitude,";
	$command .= "'$registeredowners',  '$titleno', $area, '$tdno', '$instrument', '$transferor','$transferees', null,";
	$command .= "'$placeconveyance', '$docno', '$pageno', '$bookno', $yrnotarized, '$notarypublic',$userid)";
	$result = getrow($command);
	$myobj->iddcrform = $result['@id'];
	$myobj->applicant = $applicant;
	$myobj->trans='DARPO';
	$myobj->email = $email;
	$myobj->officecode=$officecode;
	$command ="CALL inserttrackform('$officecode', 'DARPO','', $myobj->iddcrform, NULL ,$userid)";
	$r=getrow($command);
	
	$keyname = 'iddcrform';
	$keyvalue = $myobj->iddcrform;
	$label = 'Land Title';
    $file = $_FILES['pdftitle'];
	$picture = $file['name'];
	$myobj->status = -1;
    if ($picture != ""){   
        $source =$file['tmp_name'];
		$stamp = getdate();
		$picture = htmlspecialchars(strtoupper($picture),ENT_QUOTES,"UTF-8");
		$imagename = $stamp['year'].$stamp['mon'].$stamp['mday'].$stamp['hours'].$stamp['minutes'].$stamp['seconds']."_".$picture;
		$save = 'attachments/'.$imagename; //This is the new file you saving
		
		if (move_uploaded_file($source, $save)){
			$myobj->status = 1;
			$label = $picture;
			$command = "CALL insertattachments('$keyname',$keyvalue, '$label', '$imagename',$userid)";
			$row = getrow($command);
		} 
	}
	$label = 'Land Transfer Clearance Request Letter';
    $file = $_FILES['pdfapplication'];
	$picture = $file['name'];
	$myobj->status = -1;
    if ($picture != ""){   
        $source =$file['tmp_name'];
		$stamp = getdate();
		$picture = htmlspecialchars(strtoupper($picture),ENT_QUOTES,"UTF-8");
		$imagename = $stamp['year'].$stamp['mon'].$stamp['mday'].$stamp['hours'].$stamp['minutes'].$stamp['seconds']."_".$picture;
		$save = 'attachments/'.$imagename; //This is the new file you saving
		
		if (move_uploaded_file($source, $save)){
			$myobj->status = 1;
			$label = $picture;
			$command = "CALL insertattachments('$keyname',$keyvalue, '$label', '$imagename',$userid)";
			$row = getrow($command);
		} 
	}
	$label = 'Deed or Document to be transfered';
    $file = $_FILES['pdfdeed'];
	$picture = $file['name'];
	$myobj->status = -1;
    if ($picture != ""){   
        $source =$file['tmp_name'];
		$stamp = getdate();
		$picture = htmlspecialchars(strtoupper($picture),ENT_QUOTES,"UTF-8");
		$imagename = $stamp['year'].$stamp['mon'].$stamp['mday'].$stamp['hours'].$stamp['minutes'].$stamp['seconds']."_".$picture;
		$save = 'attachments/'.$imagename; //This is the new file you saving
		
		if (move_uploaded_file($source, $save)){
			$myobj->status = 1;
			$label = $picture;
			$command = "CALL insertattachments('$keyname',$keyvalue, '$label', '$imagename',$userid)";
			$row = getrow($command);
		} 
	}
	$label = 'Affidavit of Aggregate Land Holding';
    $file = $_FILES['pdflandholding'];
	$picture = $file['name'];
	$myobj->status = -1;
    if ($picture != ""){   
        $source =$file['tmp_name'];
		$stamp = getdate();
		$picture = htmlspecialchars(strtoupper($picture),ENT_QUOTES,"UTF-8");
		$imagename = $stamp['year'].$stamp['mon'].$stamp['mday'].$stamp['hours'].$stamp['minutes'].$stamp['seconds']."_".$picture;
		$save = 'attachments/'.$imagename; //This is the new file you saving
		
		if (move_uploaded_file($source, $save)){
			$myobj->status = 1;
			$label = $picture;
			$command = "CALL insertattachments('$keyname',$keyvalue, '$label', '$imagename',$userid)";
			$row = getrow($command);
		} 
	}
	$label = 'Affidavit of Retention';
    $file = $_FILES['pdfretention'];
	$picture = $file['name'];
	$myobj->status = -1;
    if ($picture != ""){   
        $source =$file['tmp_name'];
		$stamp = getdate();
		$picture = htmlspecialchars(strtoupper($picture),ENT_QUOTES,"UTF-8");
		$imagename = $stamp['year'].$stamp['mon'].$stamp['mday'].$stamp['hours'].$stamp['minutes'].$stamp['seconds']."_".$picture;
		$save = 'attachments/'.$imagename; //This is the new file you saving
		
		if (move_uploaded_file($source, $save)){
			$myobj->status = 1;
			$label = $picture;
			$command = "CALL insertattachments('$keyname',$keyvalue, '$label', '$imagename',$userid)";
			$row = getrow($command);
		} 
	}
	$label = 'Municipal Assessor\'s Certificate';
    $file = $_FILES['pdfmunicipalcert'];
	$picture = $file['name'];
	$myobj->status = -1;
    if ($picture != ""){   
        $source =$file['tmp_name'];
		$stamp = getdate();
		$picture = htmlspecialchars(strtoupper($picture),ENT_QUOTES,"UTF-8");
		$imagename = $stamp['year'].$stamp['mon'].$stamp['mday'].$stamp['hours'].$stamp['minutes'].$stamp['seconds']."_".$picture;
		$save = 'attachments/'.$imagename; //This is the new file you saving
		
		if (move_uploaded_file($source, $save)){
			$myobj->status = 1;
			$label = $picture;
			$command = "CALL insertattachments('$keyname',$keyvalue, '$label', '$imagename',$userid)";
			$row = getrow($command);
		} 
	}
	$label = 'City Assessor\'s Certificate';
    $file = $_FILES['pdfcitycert'];
	$picture = $file['name'];
	$myobj->status = -1;
    if ($picture != ""){   
        $source =$file['tmp_name'];
		$stamp = getdate();
		$picture = htmlspecialchars(strtoupper($picture),ENT_QUOTES,"UTF-8");
		$imagename = $stamp['year'].$stamp['mon'].$stamp['mday'].$stamp['hours'].$stamp['minutes'].$stamp['seconds']."_".$picture;
		$save = 'attachments/'.$imagename; //This is the new file you saving
		
		if (move_uploaded_file($source, $save)){
			$myobj->status = 1;
			$label = $picture;
			$command = "CALL insertattachments('$keyname',$keyvalue, '$label', '$imagename',$userid)";
			$row = getrow($command);
		} 
	}
	$label = 'Provincial Assessor\'s Certificate';
    $file = $_FILES['pdfprovincialcert'];
	$picture = $file['name'];
	$myobj->status = -1;
    if ($picture != ""){   
        $source =$file['tmp_name'];
		$stamp = getdate();
		$picture = htmlspecialchars(strtoupper($picture),ENT_QUOTES,"UTF-8");
		$imagename = $stamp['year'].$stamp['mon'].$stamp['mday'].$stamp['hours'].$stamp['minutes'].$stamp['seconds']."_".$picture;
		$save = 'attachments/'.$imagename; //This is the new file you saving
		
		if (move_uploaded_file($source, $save)){
			$myobj->status = 1;
			$label = $picture;
			$command = "CALL insertattachments('$keyname',$keyvalue, '$label', '$imagename',$userid)";
			$row = getrow($command);
		} 
	}
	$label = 'MARPO Certificate';
    $file = $_FILES['marpocert'];
	$picture = $file['name'];
	$myobj->status = -1;
    if ($picture != ""){   
        $source =$file['tmp_name'];
		$stamp = getdate();
		$picture = htmlspecialchars(strtoupper($picture),ENT_QUOTES,"UTF-8");
		$imagename = $stamp['year'].$stamp['mon'].$stamp['mday'].$stamp['hours'].$stamp['minutes'].$stamp['seconds']."_".$picture;
		$save = 'attachments/'.$imagename; //This is the new file you saving
		
		if (move_uploaded_file($source, $save)){
			$myobj->status = 1;
			$label = $picture;
			$command = "CALL insertattachments('$keyname',$keyvalue, '$label', '$imagename',$userid)";
			$row = getrow($command);
		} 
	}
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
if ($trans=='offices'){
	getoffices();
}
if ($trans=='updatetrack'){
		updatetrack($userid);
}
if ($trans=='forDARMO'){
	forDARMO($userid);
}
if ($trans=='forLegal'){
	forLegal($userid);
}
if ($trans=='forPARO'){
	forPARO($userid);
}
if ($trans=='forCHIEFLEGAL'){
	forCHIEFLEGAL($userid);
}
if ($trans=='forCARPO'){
	forCARPO($userid);
}
if ($trans=='forAmend'){
	forAmend($userid);
}
if ($trans=='savedetails'){
	savedetails($userid);
}
if ($trans=='seen'){
	seen($userid);
}
if ($trans=='getcase'){
	getcase();
}
if ($trans=='disapproveclearance'){
	disapproveclearance($userid);
}
if ($trans=='appeals'){
	appeals($userid);
}

if ($trans=='DARPO'){
	DARPO();
}

?>