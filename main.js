
var menu;
var qs = get_query();
var fullname;
var useraccess;
var soffice;
var role;
var tk;

loadScript();
function loadScript() {
  var s2 =document.createElement("script");
	s2.onload = function() {
		load2scripts();
		
	}
	s2.src ="jquery-1.12.3.min.js";
	document.head.appendChild(s2);
}
function load2scripts(){
	
  var s1 =document.createElement("script");
	s1.src ="bootstrap.min.js";
	s1.onload = function() {
		$('[data-toggle="tooltip"]').tooltip();
	}
	document.head.appendChild(s1);
  var s = document.createElement("script");
	
	s.onload = function (){
		jQuery.fn.dataTable.Api.register( 'sum()', function ( ) {
		return this.flatten().reduce( function ( a, b ) {
		if ( typeof a === 'string' ) {
			a = a.replace(/[^\d.-]/g, '') * 1;
		}
		if ( typeof b === 'string' ) {
			b = b.replace(/[^\d.-]/g, '') * 1;
		}

		return a + b;
		}, 0 );
		} );
		var sa = document.createElement("script");
		sa.src ="Buttons-1.5.6/js/buttons.print.min.js";
		document.head.appendChild(sa);
	}
	
    s.src ="jquery.dataTables.min.js";
	document.head.appendChild(s);
	var s2 = document.createElement("script");
	s2.src ="jquery.toaster.js";
	document.head.appendChild(s2);
	var s3 = document.createElement("script");
	s3.src ="bootbox.min.js";
	document.head.appendChild(s3);
	var s4 = document.createElement("script");
	s4.src ="select2.min.js";
	
	
	s4.onload=function() {
		ready();
	}
	document.head.appendChild(s4);
}
function ready(){

	$("#header").load("header.html");
	$("#header1").load("header1.html");
	 
	var nbackground = Math.floor((Math.random() * 3) + 1);
	//if (nbackground == 1 ){document.body.style.backgroundImage = "url('board.jpg')";}
	//if (nbackground == 2 ){document.body.style.backgroundImage = "url('kids.png')";}
	//if (nbackground == 3 ){document.body.style.backgroundImage = "url('kids2.png')";}
	//document.body.style.backgroundImage = "url('')";
	//document.body.style.backgroundRepeat = "no-repeat";
	//document.body.style.backgroundPosition = "center center";
	//document.body.style.backgroundSize = "cover";
	//document.body.style.backgroundAttachment ="fixed";
	startTime();
	$.get("checkstatus.php",{"token":qs['tk']}, function(data){ signbutton(data);},"json");	
    
}

function signbutton(data){
   	
   if (data['userid']==-1){
		if (document.getElementById('login')){document.getElementById('login').style.display = "block";}
		if (document.getElementById('logout')){document.getElementById('logout').style.display = "none";}
		if (document.getElementById('menuicon')){document.getElementById('menuicon').style.display = "none";}
		if (document.getElementById('content')){document.getElementById('content').style.display = "none";}
		
   } else {
		document.getElementById('login').style.display = "none";
		document.getElementById('logout').innerHTML =  '<span><img src="userimages/'+data['image']+'"style="max-height:1.5em"><span id="fullname">'+data['fullname']+'</span></span>';
		document.getElementById('logout').style.display = "block";
		menu = data['menu'];
		
		
		fullname = data['fullname'];
		soffice = data['office'];
		
		showmenu(data['userid'], data['token']);
		
		
		startload();
		
   }  
}
function showmenu(userid, token){
	$.get("usersController.php", {trans:"priviledges",userid: userid,"tk":token}, function(data) {showmenu2(data);},'json');
}
function showmenu2(data){
	var userid = data['userid'];
	var token = data['token'];
	menu = data['menu'];
	useraccess = data['access'];
	var content = '<a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>';
		for(var key in menu) {
			var data = menu[key];
			
			if (data['url']){
				if (checkaccess(key, useraccess)=='checked'){
					var url1 = data['url'];
					var n = url1.indexOf("?");
					var xurl ='';
					if (n== -1) {
						xurl = url1+'?tk='+token;
					} else {xurl = url1+'&tk='+token;}
				
					content +='<a href="'+xurl+'">'+key+'</a>';
				}
			}
			else {
				var ckey ="'"+key+"','"+token+"'";
				content += '<a href="#" onclick="menudetails('+ckey+')">'+key+'</a>';
			}
		}
	document.getElementById('mySidenav').innerHTML = content;
	document.getElementById('menuicon').style.display = "block";
				
}
function SignIn(){
	$('#loginModal').modal('show');
}
function verify(){
	var email = document.getElementById('emaillog').value;
	var password = document.getElementById('passwordlog').value;
	$.get("loginController.php",{email:email,password:password}, function(data){ processverification(data);},"json");
	
}
function processverification(data){
	if (data['userid']==-1){
		
	} else {
		
		$.get("checkstatus.php",{"token":data['token']}, function(data){ signbutton(data);},"json");
		$('#loginModal').modal('hide');
	}
}
function SignOut(){
	$("#lModal").modal("show");
}	
function logout(){
	$("#lModal").modal("hide");
	$.get("loginController.php",{email:''}, function(data) {window.open("index.html",'_self');},"json");
}
function lnot(){
	$("#lModal").modal("hide");
}
function openNav() {
    document.getElementById("mySidenav").style.width = "250px";
}

/* Set the width of the side navigation to 0 */
function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
}
function menudetails(key,token){
var content = menu[key];
var detail = "";

	for(var key in content){
		var dd = content[key];
		
		if (checkaccess(key, useraccess)=='checked'){
			var details = content[key];
			if (details['display']){
				var image;
				if (details['image']){
					image = details['image'];
				} else {
					image = 'buttons.jpg';
				}
				
				var url1 = details['url'];
				var n = url1.indexOf("?");
				var xurl ='';
				if (n== -1) {
					xurl = url1+'?tk='+token;
				} else {xurl = url1+'&tk='+token;}
				detail += '<a href="'+xurl+'"><div class="col-sm-3"><div class="well center-block text-center"><img src="'+image+'" class="img-responsive center-block"><span><strong>'+key+'</strong></span></div></div></a>';
			}	
		}
	}
	document.getElementById('content').innerHTML = detail;
	document.getElementById('content').style.display = 'block';
	closeNav();
}
function configuredate(){
var d = new Date(); 
	cmonth = d.getMonth()+1;
	cmonth = '' + cmonth;
	if (cmonth.trim().length==1){
		cmonth = '0'+cmonth;
	}
	cday = ''+d.getDate();
	if (cday.trim().length==1){
		cday = '0'+cday;
	}
	dstring = d.getFullYear() + "-" + cmonth + "-" + cday;
	return dstring;
}
function get_query(){
	
    var url = location.href;
    var qs = url.substring(url.indexOf('?') + 1).split('&');
    for(var i = 0, result = {}; i < qs.length; i++){
        qs[i] = qs[i].split('=');
        result[qs[i][0]] = decodeURI(qs[i][1]);
    }
	 return result;
}
function startTime() {
    var today = new Date();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();
	var mid = "AM";
    m = checkTime(m);
    s = checkTime(s);
	
	if (h>12) {
		mid ="PM";
		h = h - 12;
	}
	if (h<1){
		h="12";
	}
	if (h<10){
			h = "0" + h;
	}
	if (document.getElementById('time')){
		document.getElementById('time').innerHTML = h + ":" + m + ":" + s + " " + mid;
	}
    var t = setTimeout(startTime, 500);
}
function checkTime(i) {
    if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
    return i;
}
function checkaccess(details, access){
	
	for (x = 0; x < access.length; x++){ 
		var module = access[x];
		if (details==module['name']) {
			return 'checked';
		}
	}
	return '';
}

function confirmModal(smessage){
	document.getElementById('cMessage').innerHTML=smessage;
	$("#cModal").modal("show");
}
function initload(){
var otk = document.getElementsByName('tk');
tk=qs['tk'];
for(var x=0; x < otk.length; x++)   // comparison should be "<" not "<="
	{
		otk[x].value = qs['tk'];
	}
}
