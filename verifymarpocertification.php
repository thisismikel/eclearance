<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="referrer" content="origin">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="mobile-web-app-capable" content="yes">
<link rel="stylesheet" href="main.css">

</head>

<body>
	<h1><img src="DARLogo.jpg"></h1>
	<div id="pdfshow"></div>
</body>
<script src="jquery.min.js"></script>

<script src="pdfobject.min.js"></script>
<script>
$(function() {
	let qs = get_query();
	let iddcrform = qs['iddcrform'];
	//PDFObject.embed("verifydarclearance2.php?iddcrform="+iddcrform, "#pdfshow");
	let params = 'menubar=no,width=0, height=0`,location=no, status=no, toolbar=no';
	window.open('verifymarpocertification2.php?iddcrform='+iddcrform,'pdfwindow',params);

});
function get_query(){
	
    var url = location.href;
    var qs = url.substring(url.indexOf('?') + 1).split('&');
    for(var i = 0, result = {}; i < qs.length; i++){
        qs[i] = qs[i].split('=');
        result[qs[i][0]] = decodeURI(qs[i][1]);
    }
	 return result;
}
</script>
<?php
?>