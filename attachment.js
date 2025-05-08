function getattachments(keyvalue,readonly){
var skeyname=document.getElementById('keyname').value; 
var tk = qs['tk'];
	txt="trans2=getattachments&keyname="+skeyname+"&keyvalue="+keyvalue+"&tk="+tk+"&end=end";
	$.ajax({
            type: "GET",
            url: "attachmentController.php",
            data: txt,
			dataType: 'json',
            cache: false,
            success: function(data) {
					var cbody="";
					cidpayables ="";
					var count = 0;
					for (var i = 0; i < data.length; i++){
						count += 1;
						var imgdet= data[i];
						cbody += formatattachments(imgdet,readonly);	  	
						if (count == 4) { 
							cbody += "<div class='row'></div>";
							count = 0;}
					}	
					document.getElementById('divimages').innerHTML = cbody;
            }
    });
	if (readonly){
		document.getElementById('attachmentbutton').style.display = "none";
	}
}
function getattachments1(keyvalue,readonly){
var skeyname=document.getElementById('keyname1').value; 
var tk = qs['tk'];
	txt="trans2=getattachments&keyname="+skeyname+"&keyvalue="+keyvalue+"&tk="+tk+"&end=end";
	$.ajax({
            type: "GET",
            url: "attachmentController.php",
            data: txt,
			dataType: 'json',
            cache: false,
            success: function(data) {
					var cbody="";
					cidpayables ="";
					var count = 0;
					for (var i = 0; i < data.length; i++){
						count += 1;
						var imgdet= data[i];
						cbody += formatattachments(imgdet,readonly);	  	
						if (count == 4) { 
							cbody += "<div class='row'></div>";
							count = 0;}
					}	
					document.getElementById('divimages1').innerHTML = cbody;
            }
    });
	if (readonly){
		document.getElementById('attachmentbutton1').style.display = "none";
	}
}

function formatattachments(imgdet,readonly){
	var cbody ='';
	var a1 = imgdet['imagename'];
	var id1="'"+imgdet['idattachments']+"'";
	var a = a1.split(".");
	var ext = a.pop();
	cbody ='<div id="attachment'+imgdet['idattachments']+'" class="col-xs-12" >';
	cbody +='<div id="path'+imgdet['idattachments']+'" style="display:none">'+imgdet['imagename']+'</div>';
	cbody += '<a href="#"  onclick="showimg('+id1+');return false;">';
	cbody += '<img src="images/paperclip.jpg" id="img'+imgdet['idattachments']+'">'+imgdet['label']+'</a>';
	if (!readonly){
		cbody += ' <a href="#" name="attachdelitem" type="button" onclick="deletethis('+id1+');return false;" class="btn btn-danger btn-xs">Remove</a>';
	}
	cbody += '</div>';  
	return cbody;
}
function addimages(){
  $("#myImage").modal("show");
}
function addimages1(){
  $("#myImage1").modal("show");
}
function checkfilesize()
{
    var x = document.getElementById("inputForm");
    var i;
	n=1;
    for (i = 0; i < x.length; i++) {
        var ctype1 = x.elements[i].type;
        if (ctype1 == "file") { 
            var input = x.elements[i];
            if (!input) { alert("Um, couldn't find the fileinput element.");
            }
            else if (!input.files) {
                alert("This browser doesn't seem to support the `files` property of file inputs.");
				n=0;
            }
            else if (!input.files[0]) {
                n=0;
            }
            else {
                file = input.files[0];
                if (file.size >20000000) {
                    alert("File " + file.name + " must not be more than 20MB in size");
					n=0;
                    return false;
                }
            }
            
        }
    }

	if (n==1) {
		var someform = document.getElementById('inputForm');
		var data = new FormData(someform);
		$.ajax({
            url: 'attachmentController.php',
			data: data,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
			type: 'POST',
            success: function(data) {
				    if (data["idattachments"] > -1){
						var gg = document.getElementById('divimages').innerHTML;
						var cbody = formatattachments(data);
						document.getElementById('divimages').innerHTML = gg + cbody;
					}
					
					$("#myImage").modal("hide");
				
			}
		});
	}
    return true;
        
}
function checkfilesize1()
{
	
    var x = document.getElementById("inputForm1");
    var i;
	n=1;
    for (i = 0; i < x.length; i++) {
        var ctype1 = x.elements[i].type;
        if (ctype1 == "file") { 
            var input = x.elements[i];
            if (!input) { alert("Um, couldn't find the fileinput element.");
            }
            else if (!input.files) {
                alert("This browser doesn't seem to support the `files` property of file inputs.");
				n=0;
            }
            else if (!input.files[0]) {
                n=0;
            }
            else {
                file = input.files[0];
                if (file.size >20000000) {
                    alert("File " + file.name + " must not be more than 20MB in size");
					n=0;
                    return false;
                }
            }
            
        }
    }

	if (n==1) {
		var someform = document.getElementById('inputForm1');
		var data = new FormData(someform);
		$.ajax({
            url: 'attachmentController.php',
			data: data,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
			type: 'POST',
            success: function(data) {
				    if (data["idattachments"] > -1){
						var gg = document.getElementById('divimages1').innerHTML;
						var cbody = formatattachments(data);
						document.getElementById('divimages1').innerHTML = gg + cbody;
					}
					
					$("#myImage1").modal("hide");
				
			}
		});
	}
    return true;
        
}
function checkordertransfer()
{
    var x = document.getElementById("transferform");
    var i;
	n=1;
    for (i = 0; i < x.length; i++) {
        var ctype1 = x.elements[i].type;
        if (ctype1 == "file") { 
            var input = x.elements[i];
            if (!input) { alert("Um, couldn't find the fileinput element.");
            }
            else if (!input.files) {
                alert("This browser doesn't seem to support the `files` property of file inputs.");
				n=0;
            }
            else if (!input.files[0]) {
                n=0;
            }
            else {
                file = input.files[0];
                if (file.size >20000000) {
                    alert("File " + file.name + " must not be more than 20MB in size");
					n=0;
                    return false;
                }
            }
            
        }
    }

	if (n==1) {
		var someform = document.getElementById('inputForm');
		var data = new FormData(someform);
		$.ajax({
            url: 'attachmentController.php',
			data: data,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
			type: 'POST',
            success: function(data) {
				    if (data["idattachments"] > -1){
						var gg = document.getElementById('divimages').innerHTML;
						var cbody = formatattachments(data);
						document.getElementById('divimages').innerHTML = gg + cbody;
					}
					
					$("#myImage").modal("hide");
				
			}
		});
	}
    return true;
        
}
function deletethis(id){
  var path1 = 'path'+id;	
  var source = "attachments/"+document.getElementById(path1).innerHTML;
  var tk = qs['tk'];
  var txt='trans2=delimage&id='+id+'&tk='+tk+'&src='+encodeURI(source);
  if(!confirm("Remove this image?")){return false;}
  $.ajax({
            type: "GET",
            url: "attachmentController.php",
            data: txt,
			dataType: 'json',
            cache: false,
            success: function(data) {
                if (data['id']=="-1") {
                  alert("Failed to delete this Image...")  ;
                } else {
                  var divid='attachment'+id;
                  document.getElementById(divid).style.display = "none";
                  
                }
            
            }
              
          });
}
function showimg(id){
	    var path1 = 'path'+id;
		var imgid = 'img'+id;
		var img = document.getElementById(imgid);
		var path = document.getElementById(path1).innerHTML;
			
		var a = path.split(".");
		var astr = a.pop();
		if (astr.toUpperCase() == 'PDF') {
			window.open('attachments/'+escapeHtml(path),"_blank");
		} else {
		   	document.getElementById('imgmodal').src = 'attachments/'+path;
			$('#myShowImage').modal('toggle');
		}
		
}
function readURL(input, id) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
	  var sfilename = input.value;
	  var ext = sfilename.substr(sfilename.lastIndexOf('.') + 1);
	  if (ext.toUpperCase() == 'PDF'){$(id).attr('src', 'attachments/pdficon.jpg');} else {
      $(id)
        .attr('src', e.target.result);}
        
    };
	
    reader.readAsDataURL(input.files[0]);
  }
}
function hidemyImage(){
	$('#myImage').modal('toggle');
}
function hidemyImage1(){
	$('#myImage1').modal('toggle');
}
function hidemyShowImage(){
	$('#myShowImage').modal('toggle');
}
function startattachment() {
	$("#attachmentdiv").load("attachment.html");
}
function attachmentreadonly(){
		document.getElementById('attachmentbutton').style.display = "none";
		var x = document.getElementsByName('attachdelitem');
		for (i = 0; i < x.length; i++){
			
			x[i].style.display = "none";
		}
		
}
function attachmentreadonly1(){
		document.getElementById('attachmentbutton1').style.display = "none";
		var x = document.getElementsByName('attachdelitem1');
		for (i = 0; i < x.length; i++){
			
			x[i].style.display = "none";
		}
		
}
function escapeHtml(text) {
  var map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };
  
  return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}