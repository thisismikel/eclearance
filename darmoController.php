<?php
require "routines.php";

function getdetails(){
	$table = new stdClass();
	$officecode = $_REQUEST['officecode'];
	$command = "select *,  (select office.officename from office where office.idoffice=dcrform.municipal) as municipalname, (select office.officename from office where office.idoffice=dcrform.provincial) as provincialname, (select office.officename from office where office.idoffice=dcrform.regional) as regionalname from trackform left join dcrform on trackform.iddcrform = dcrform.iddcrform where trackform.status='DARMO' and trackform.officecode='$officecode' and trackform.enddate is null";
	$table->data = gettable($command);
	echo json_encode($table);
	
}
function getoffices(){
	$myobj = new stdClass();
	$officecode = $_REQUEST['officecode'];
	$command = "select * from office where officecode = '$officecode'";
	$municipal = getrow($command);
	$myobj->error = false;
	if (empty($municipal)){
		$myobj->error = true;
	} else {
		if ($municipal['officetype']!='MUNICIPAL'){
			$myobj->error = true;
		}
	}
	if (!$myobj->error){
		$myobj->municipal = $municipal;
		$idofficemunicipal = $municipal['idoffice'];
		$myobj->idofficemunicipal = $idofficemunicipal;
		$command = "select * from provincialoffice where idofficemunicipal = $idofficemunicipal";
		$row = getrow($command);
		$idofficeprovincial = $row['idofficeprovincial'];
		$command = "select * from office where idoffice = $idofficeprovincial";
		$provincial = getrow($command);
		$myobj->provincial = $provincial;
		$myobj->idofficemunicipal = $idofficemunicipal;
		$myobj->idofficeprovincial = $row['idofficeprovincial'];
		$myobj->idofficeregional = 52;
		$command = "select * from office where idoffice = 52";
		$regional = getrow($command);
		$myobj->regional = $regional;
	}
	
	echo json_encode($myobj);
}

function insertdetails($userid){
	$myobj = new stdClass();
	$address=htmlspecialchars(strtoupper($_REQUEST['address']),ENT_QUOTES,"UTF-8");
	if (empty($address)) {$address='';}
	$referenceno=htmlspecialchars(strtoupper($_REQUEST['referenceno']),ENT_QUOTES,"UTF-8");
	if (empty($referenceno)) {$referenceno='';}
	$applicant=htmlspecialchars(strtoupper($_REQUEST['applicant']),ENT_QUOTES,"UTF-8");
	if (empty($applicant)) {$applicant='';}
	$datefiled=$_REQUEST['datefiled'];
	$urgent=$_REQUEST['urgent'];
	if (empty($urgent)) {$urgent='';}
	$email=$_REQUEST['email'];
	if (empty($email)) {$email='';}
	$cellno=$_REQUEST['cellno'];
	if (empty($cellno)) {$cellno='';}
	$telno=$_REQUEST['telno'];
	if (empty($telno)) {$telno='';}
	$transaction=$_REQUEST['transaction1'];
	if (empty($transaction)) {$transaction='';}
	$amortization=$_REQUEST['amortization'];
	if (empty($amortization)) {$amortization='';}	
	$details=htmlspecialchars($_REQUEST['remarks'],ENT_QUOTES,"UTF-8");
	if (empty($details)) {$details='';}
	$nia=$_REQUEST['nia'];
	if (empty($nia)) {$nia='';}	
	$loancert=$_REQUEST['loancert'];
	if (empty($loancert)) {$loancert='';}	
	$taxclearance=$_REQUEST['taxclearance'];
	if (empty($taxclearance)) {$taxclearance='';}
	$affidavittransferor=$_REQUEST['affidavittransferor'];
	if (empty($affidavittransferor)) {$affidavittransferor='';}
	$affidavitaggregate=$_REQUEST['affidavitaggregate'];
	if (empty($affidavitaggregate)) {$affidavitaggregate='';}
	$taxreturn=$_REQUEST['taxreturn'];
	if (empty($taxreturn)) {$taxreturn='';}
	$rescert=$_REQUEST['rescert'];
	if (empty($rescert)) {$rescert='';}
	$assessor=$_REQUEST['assessor'];
	if (empty($assessor)) {$assessor='';}
	$propertylocation = htmlspecialchars(strtoupper($_REQUEST['propertylocation']),ENT_QUOTES,"UTF-8");
	if (empty($propertylocation)) {$propertylocation='';}
	$longtitude=$_REQUEST['longtitude'];
	if (empty($longtitude)) {$longtitude=0;}
	$latitude=$_REQUEST['latitude'];
	if (empty($latitude)) {$latitude=0;}
	$officecode = $_REQUEST['officecode'];
	$preparedby=htmlspecialchars(strtoupper($_REQUEST['preparedby']),ENT_QUOTES,"UTF-8");
	$municipal = $_REQUEST['municipal'];
	$provincial = $_REQUEST['provincial'];
	$regional = $_REQUEST['regional'];
	$officecode=$_REQUEST['officecode'];
	$registeredowners = htmlspecialchars(strtoupper($_REQUEST['registeredowners']),ENT_QUOTES,"UTF-8");
	$titleno = htmlspecialchars(strtoupper($_REQUEST['titleno']),ENT_QUOTES,"UTF-8");
	$area = $_REQUEST['area'];
	$tdno = htmlspecialchars(strtoupper($_REQUEST['tdno']),ENT_QUOTES,"UTF-8");
	$instrument = htmlspecialchars(strtoupper($_REQUEST['instrument']),ENT_QUOTES,"UTF-8");
	$transferor = htmlspecialchars(strtoupper($_REQUEST['transferor']),ENT_QUOTES,"UTF-8");
	$transferees = htmlspecialchars(strtoupper($_REQUEST['transferees']),ENT_QUOTES,"UTF-8");
	$dateconveyance = $_REQUEST['dateconveyance'];
	$placeconveyance = htmlspecialchars(strtoupper($_REQUEST['placeconveyance']),ENT_QUOTES,"UTF-8");
	$docno = htmlspecialchars(strtoupper($_REQUEST['docno']),ENT_QUOTES,"UTF-8");
	$pageno = htmlspecialchars(strtoupper($_REQUEST['pageno']),ENT_QUOTES,"UTF-8");
	$bookno = htmlspecialchars(strtoupper($_REQUEST['bookno']),ENT_QUOTES,"UTF-8");
	$yrnotarized = $_REQUEST['yrnotarized'];
	$notarypublic = htmlspecialchars(strtoupper($_REQUEST['notarypublic']),ENT_QUOTES,"UTF-8");
	$command ="CALL insertdcrform('$address', '$applicant', '$referenceno', '$datefiled', '$urgent', '$email', '$cellno', '$telno', '$transaction',";
	$command .= "'$details', '$preparedby', '$amortization', '$nia','$loancert', '$taxclearance', '$affidavittransferor', '$affidavitaggregate', '$taxreturn',";
    $command .=	"'$rescert', '$assessor', $municipal, $provincial, $regional, '$propertylocation',$longtitude,$latitude,";
	$command .= "'$registeredowners',  '$titleno', $area, '$tdno', '$instrument', '$transferor','$transferees', '$dateconveyance',";
	$command .= "'$placeconveyance', '$docno', '$pageno', '$bookno', $yrnotarized, '$notarypublic',$userid)";
	//die($command);
	$result = getrow($command);
	$myobj->iddcrform = $result['@id'];
	$myobj->applicant = $applicant;
	$myobj->trans='ADD';
	$command ="CALL inserttrackform('$officecode', 'DARMO','', $myobj->iddcrform, NULL ,$userid)";
	
	$result = getrow($command);
	$myobj->idtrackform = $result['@id'];
	$myobj->officecode = $officecode;
	$myobj->trans ='ADD';
	$idtrackform = $myobj->idtrackform;
	$command ="CALL seen($idtrackform,$userid)";
	$result = getrow($command);
	echo json_encode($myobj);
}
function MARO(){
	$myobj = new stdClass();
	$address = $_REQUEST['address'];
	$applicant = $_REQUEST['fullname'];
	$referenceno = '';
	$datefiled = date("Y-m-d");
	$urgent ='';
	$email = $_REQUEST['email'];
	$cellno = $_REQUEST['cellno'];
	$telno ='';
	$transaction = "MAROCert";
	$details="Request for MARO Certification";
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
	$municipal=$_REQUEST['selMaro'];
	$provincial=$_REQUEST['idofficeprovincial'];
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
	$myobj->trans='MARO';
	$myobj->email = $email;
	$myobj->officecode=$officecode;
	$command ="CALL inserttrackform('$officecode', 'DARMO','', $myobj->iddcrform, NULL ,$userid)";
	$r=getrow($command);
	
	$keyname = 'iddcrform';
	$keyvalue = $myobj->iddcrform;
	$label = 'Land Title';
    $file = $_FILES['pdftitle'];
	$picture = $file['name'];
	$myobj->status = -1;
	$myobj->error ='';
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
		} else { $myobj->error = $label.", "; }
	}
	$label = 'MARPO Request Letter';
    $file = $_FILES['marpoletter'];
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
		} else { $myobj->error = $myobj->error.$label.", "; }
	}
	$label = 'Tax Declaration';
    $file = $_FILES['td'];
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
		} else { $myobj->error = $myobj->error.$label.", "; }
	}
	$label = 'Affidavit of Non Tenancy';
    $file = $_FILES['nontenancy'];
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
		} else { $myobj->error = $myobj->error.$label.", "; }
	}
	$label = 'Deed of Conveyance';
    $file = $_FILES['deed'];
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
		} else { $myobj->error = $myobj->error.$label.", "; }
	}
	$label = 'Special Power of Attorney';
    $file = $_FILES['spa'];
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
		} else { $myobj->error = $myobj->error.$label.", "; }
	}
	echo json_encode($myobj);

}
function updatedetails($userid){
	$myobj = new stdClass();
	$iddcrform=$_REQUEST['iddcrform'];
	$address=htmlspecialchars(strtoupper($_REQUEST['address']),ENT_QUOTES,"UTF-8");
	if (empty($address)) {$address='';}
	$referenceno=htmlspecialchars(strtoupper($_REQUEST['referenceno']),ENT_QUOTES,"UTF-8");
	if (empty($referenceno)) {$referenceno='';}
	$applicant=htmlspecialchars(strtoupper($_REQUEST['applicant']),ENT_QUOTES,"UTF-8");
	if (empty($applicant)) {$applicant='';}
	$datefiled=$_REQUEST['datefiled'];
	if (array_key_exists("urgent",$_REQUEST))
			$urgent=$_REQUEST['urgent']; else $urgent='';
	$email=$_REQUEST['email'];
	if (empty($email)) {$email='';}
	$cellno=$_REQUEST['cellno'];
	if (empty($cellno)) {$cellno='';}
	$telno=$_REQUEST['telno'];
	if (empty($telno)) {$telno='';}
	$transaction=$_REQUEST['transaction1'];
	if (empty($transaction)) {$transaction='LANDClearance';}
	if (array_key_exists("amortization",$_REQUEST))
		$amortization = $_REQUEST['amortization']; else $amortization='';
	$details=htmlspecialchars($_REQUEST['remarks'],ENT_QUOTES,"UTF-8");
	if (empty($details)) {$details='';}
	if (array_key_exists("nia",$_REQUEST))
		$nia=$_REQUEST['nia']; else $nia='';
	if (array_key_exists("loancert",$_REQUEST))
		$loancert=$_REQUEST['loancert']; else $loancert='';
	if (array_key_exists("taxclearance",$_REQUEST))
		$taxclearance=$_REQUEST['taxclearance']; else $taxclearance='';
	if (array_key_exists("affidavittransferor",$_REQUEST))
		$affidavittransferor=$_REQUEST['affidavittransferor']; else $affidavittransferor='';
	if (array_key_exists("affidavitaggregate",$_REQUEST))
		$affidavitaggregate=$_REQUEST['affidavitaggregate']; else $affidavitaggregate='';
	if (array_key_exists("taxreturn",$_REQUEST))
		$taxreturn=$_REQUEST['taxreturn']; else $taxreturn='';
	if (array_key_exists("rescert",$_REQUEST))
		$rescert=$_REQUEST['rescert']; else $rescert='';
	if (array_key_exists("assessor",$_REQUEST))
		$assessor=$_REQUEST['assessor']; else $assessor='';
	$propertylocation = htmlspecialchars(strtoupper($_REQUEST['propertylocation']),ENT_QUOTES,"UTF-8");
	if (empty($propertylocation)) {$propertylocation='';}
	$longtitude=$_REQUEST['longtitude'];
	if (empty($longtitude)) {$longtitude=0;}
	$latitude=$_REQUEST['latitude'];
	if (empty($latitude)) {$latitude=0;}
	$officecode = $_REQUEST['officecode'];
	$preparedby=htmlspecialchars(strtoupper($_REQUEST['preparedby']),ENT_QUOTES,"UTF-8");
	if (!empty($_FILES['municipal'])){
		$municipal = $_REQUEST['municipal'];
	}else {$municipal = 'null';}
	$provincial = $_REQUEST['provincial'];
	$regional = $_REQUEST['regional'];
	$registeredowners = htmlspecialchars(strtoupper($_REQUEST['registeredowners']),ENT_QUOTES,"UTF-8");
	$titleno = htmlspecialchars(strtoupper($_REQUEST['titleno']),ENT_QUOTES,"UTF-8");
	$area = $_REQUEST['area'];
	$tdno = htmlspecialchars(strtoupper($_REQUEST['tdno']),ENT_QUOTES,"UTF-8");
	$instrument = htmlspecialchars(strtoupper($_REQUEST['instrument']),ENT_QUOTES,"UTF-8");
	$transferor = htmlspecialchars(strtoupper($_REQUEST['transferor']),ENT_QUOTES,"UTF-8");
	$transferees = htmlspecialchars(strtoupper($_REQUEST['transferees']),ENT_QUOTES,"UTF-8");
	$dateconveyance = $_REQUEST['dateconveyance'];
	
	if (empty($dateconveyance)){ $dateconveyance = 'null'; } else { $dateconveyance = "'".$dateconveyance."'";}
	$placeconveyance = htmlspecialchars(strtoupper($_REQUEST['placeconveyance']),ENT_QUOTES,"UTF-8");
	$docno = htmlspecialchars(strtoupper($_REQUEST['docno']),ENT_QUOTES,"UTF-8");
	$pageno = htmlspecialchars(strtoupper($_REQUEST['pageno']),ENT_QUOTES,"UTF-8");
	$bookno = htmlspecialchars(strtoupper($_REQUEST['bookno']),ENT_QUOTES,"UTF-8");
	$yrnotarized = $_REQUEST['yrnotarized'];
	$notarypublic = htmlspecialchars(strtoupper($_REQUEST['notarypublic']),ENT_QUOTES,"UTF-8");
	$myobj->iddcrform = $iddcrform;
	$myobj->applicant = $applicant;
	$myobj->trans = 'UPDATE';
	$command ="CALL updatedcrform($iddcrform, '$address', '$applicant', '$referenceno', '$datefiled', '$urgent', '$email', '$cellno', '$telno', '$transaction', '$details', '$preparedby', '$amortization', '$nia', '$loancert', '$taxclearance', '$affidavittransferor', '$affidavitaggregate', '$taxreturn', '$rescert', '$assessor','$propertylocation',$longtitude,$latitude,"; 
	$command .= "'$registeredowners',  '$titleno', $area, '$tdno', '$instrument', '$transferor','$transferees', $dateconveyance,";
	$command .= "'$placeconveyance', '$docno', '$pageno', '$bookno', $yrnotarized, '$notarypublic',$userid)";
	getrow($command,false);
	//$command ="CALL updatemunicipal($iddcrform, $municipal,$userid)";
	//getrow($command,false);
	echo json_encode($myobj);
}

function deletedetail($userid){
	$myobj = new stdClass();
	$iddcrform = $_REQUEST['iddcrform'];
	$command ="CALL deletedcrform($iddcrform, $userid)";
	$myobj->iddcrform=$iddcrform;
	getrow($command,false);
	echo json_encode($myobj);
}
function forDARPO($userid){
	$myobj = new stdClass();
	$idtrackform = $_REQUEST['idtrackform'];
	$iddcrform = $_REQUEST['iddcrform'];
	$provincial = $_REQUEST['provincial'];
	$officename = $_REQUEST['officename'];
	if (!empty($_REQUEST['details'])){ $details = $_REQUEST['details'];} else {$details ='';}
	$command = "select * from office where idoffice=$provincial";
	$noffice = getrow($command);
	$officecode = $noffice['officecode'];
	$command ="CALL tagtrackform($idtrackform,'$details', $userid)";
	getrow($command,false);
	$command ="CALL inserttrackform('$officecode', 'DARPO', 'for DARPO Review', $iddcrform, NULL, $userid)";
	
	getrow($command,false);
	$myobj->iddcrform=$iddcrform;
	$myobj->officecode=$officecode;
	$myobj->role='DARPO';
	$myobj->officename=$officename;
	echo json_encode($myobj);
}
function appeals($userid){
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
	$command ="CALL inserttrackform2('$officecode', 'APPEALS','DARMO', '', $iddcrform, NULL, $userid)";
	getrow($command,false);
	$myobj->iddcrform=$iddcrform;
	$myobj->officecode=$officecode;
	$myobj->role='SECRETARIAT';
	$myobj->officename=$officename;
	echo json_encode($myobj);
}
function loadtemplate(){
	$myobj = new stdClass();
	$myobj->template = file_get_contents('marpo/template.txt');
	echo json_encode($myobj);
	
}
function getlocation(){
	$myobj = new stdClass();
	$officecode = $_REQUEST['officecode'];
	$command = "select * from office where officecode = '$officecode'";
	$row = getrow($command);
	$myobj->location = $row['location'];
	echo json_encode($myobj);
}
function getofficename(){
	$myobj = new stdClass();
	$officecode = $_REQUEST['officecode'];
	$command = "select * from office where officecode = '$officecode'";
	$row = getrow($command);
	$myobj->officename = $row['officename'];
	echo json_encode($myobj);
}
function getcert(){
	$myobj = new stdClass();
	$iddcrform = $_REQUEST['iddcrform'];
	$command = "select * from dcrform where iddcrform = $iddcrform";
	$row=getrow($command);
	if (empty($row['marpocert'])){ $myobj->marpocert ='';} else {$myobj->marpocert=$row['marpocert'];}
	$myobj->signedmarpocert=$row['signedmarpocert'];
	$myobj->iddcrform = $iddcrform;
	echo json_encode($myobj);
}
function showpdf(){
	$iddcrform= $_REQUEST['iddcrform'];
	$command = "select * from dcrform where iddcrform = $iddcrform";
	$row=getrow($command);
	$filename = 'marpo/'.$row['marpocert'];;
    $fileinfo = pathinfo($filename);
    header('Content-Type: application/pdf');
    header('Content-Length: ' . filesize($filename));
    readfile($filename);
}
function deletepdf($userid){
	$myobj = new stdClass();
	$iddcrform = $_REQUEST['iddcrform'];
	$command ="CALL deletemarpocert($iddcrform, $userid)";
	$row=getrow($command);
	$myobj->iddcrform = $iddcrform;
	echo json_encode($myobj);
}
function completed($userid){
	$myobj = new stdClass();
	$idtrackform = $_REQUEST['idtrackform'];
	$iddcrform = $_REQUEST['iddcrform'];
	$officecode = $_REQUEST['officecode'];
	$officename = $_REQUEST['officename'];
	$details = htmlspecialchars($_REQUEST['details'],ENT_QUOTES,"UTF-8");
	
	$command ="CALL tagtrackform($idtrackform,'$details', $userid)";
	getrow($command,false);
	$command ="CALL inserttrackform('$officecode', 'COMPLETED', '', $iddcrform, NULL, $userid)";
	getrow($command,false);
	//$command = "CALL tagapproved($iddcrform, $userid)";
	//getrow($command,false);
	$myobj->iddcrform=$iddcrform;
	$myobj->officecode=$officecode;
	$myobj->officename=$officename;
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
if ($trans=='UPDATE'){
	updatedetails($userid);
}
if ($trans=='getdetails'){
	getdetails();
}
if ($trans=='delete'){
	deletedetail($userid);
}
if ($trans=='offices'){
	getoffices();
}
if ($trans=='forDARPO'){
	forDARPO($userid);
}
if ($trans=='appeals'){
	appeals($userid);
}
if ($trans=='MARO'){
	MARO();
}
if ($trans=='loadtemplate'){
	loadtemplate();
}
if ($trans=='getlocation'){
	getlocation();
}
if ($trans=='getofficename'){
	getofficename();
}
if ($trans=='getcert'){
	getcert();
}
if ($trans=='showpdf'){
	showpdf();
}
if ($trans=='deletepdf'){
	deletepdf($userid);
}
if ($trans=='completed'){
	completed($userid);
}
?>