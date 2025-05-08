
var menu;
var qs = get_query();
var fullname;
var useraccess;
var soffice;
var role;
var tk;
let userid;
let signimage;

//loadScript();
//function loadScript() {
//  var s2 =document.createElement("script");
//	s2.onload = function() {
//		load2scripts();
		
//	}
//	s2.src ="jquery-1.12.3.min.js";
//	document.head.appendChild(s2);
//}
//function load2scripts(){
	
//	var s0 =document.createElement("script");
//	s0.src ="Chart.bundle.min.js";
//	document.head.appendChild(s0);
//  var s1 =document.createElement("script");
//	s1.src ="bootstrap.min.js";
//	s1.onload = function() {
//		$('[data-toggle="tooltip"]').tooltip();
//	}
//	document.head.appendChild(s1);
//  var s = document.createElement("script");
	
//	s.onload = function (){
//		jQuery.fn.dataTable.Api.register( 'sum()', function ( ) {
//		return this.flatten().reduce( function ( a, b ) {
//		if ( typeof a === 'string' ) {
//			a = a.replace(/[^\d.-]/g, '') * 1;
//		}
//		if ( typeof b === 'string' ) {
//			b = b.replace(/[^\d.-]/g, '') * 1;
//		}

//		return a + b;
//		}, 0 );
//		} );
//		var sa = document.createElement("script");
//		sa.src ="Buttons-1.5.6/js/buttons.print.min.js";
//		document.head.appendChild(sa);
//	}
	
//    s.src ="jquery.dataTables.min.js";
//	document.head.appendChild(s);
//	var s2 = document.createElement("script");
//	s2.src ="jquery.toaster.js";
//	document.head.appendChild(s2);
//	var s3 = document.createElement("script");
//	s3.src ="bootbox.min.js";
//	document.head.appendChild(s3);
//	var s4 = document.createElement("script");
//	s4.src ="select2.min.js";
	
//	s4.onload=function() {
//		ready();
//	}
//	document.head.appendChild(s4);
	
//}

//<script src="jquery-1.12.3.min.js"></script>
//<script src="bootstrap.min.js"></script>
//<script src="jquery.dataTables.min.js"></script>
//<script src="Buttons-1.5.6/js/buttons.print.min.js"></script>
//<script src="jquery.toaster.js"></script>
//<script src="bootbox.min.js"></script>
//<script src="select2.min.js"></script>
//<script src="Chart.bundle.min.js"></script>
//<script src="main1.js"></script>
$(function() {
	$('[data-toggle="tooltip"]').tooltip();
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
    ready();
});
function ready(){

	$("#header").load("header.html");
	$("#header1").load("header1.html");
	 
	//var nbackground = Math.floor((Math.random() * 3) + 1);
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
function home(){
	let tk = qs['tk'];
	if (tk){
		location.href = "index.html?tk="+tk;
	} else {
		location.href = "index.html";
	}
}
function signbutton(data){
   	
   if (data['userid']==-1){
		if (document.getElementById('login')){document.getElementById('login').style.display = "block";}
		if (document.getElementById('logout')){document.getElementById('logout').style.display = "none";}
		if (document.getElementById('menuicon')){document.getElementById('menuicon').style.display = "none";}
		//if (document.getElementById('content')){document.getElementById('content').style.display = "none";}
		
   } else {
		document.getElementById('login').style.display = "none";
		document.getElementById('logout').innerHTML =  '<span><img src="userimages/'+data['image']+'"style="max-height:1.5em"><span id="fullname">'+data['fullname']+'</span></span>';
		document.getElementById('logout').style.display = "block";
		menu = data['menu'];
		
		
		fullname = data['fullname'];
		soffice = data['office'];
		userid = data['userid'];
		signimage = data['signature'];
		showmenu(data['userid'], data['token']);
		
		
		
		
   }  
   startload();
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
		$.toaster({ priority : 'danger', title : 'DAR', message : 'Invalid Account'});
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

var date = new Date();
var day = date.getDate();
var month = date.getMonth() + 1;
var year = date.getFullYear();

if (month < 10) month = "0" + month;
if (day < 10) day = "0" + day;
	//dstring = d.getFullYear() + "-" + cmonth + "-" + cday;
	var today = year + "-" + month + "-" + day;       
	return date.toLocaleDateString('en-CA');
	
}
function datetimelocal(){
	var now = new Date();
	now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    return now.toISOString().slice(0,16);
}
function initdatetimelocal(xdate){
	var now = new Date(xdate);
	now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    return now.toISOString().slice(0,16);
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
for(var x=0; x < otk.length; x++)   // comparison should be "<" not "<="
	{
		otk[x].value = qs['tk'];
	}
}
function datedifference(date1, date2, period, unit){
var talert = false;
var d = Math.abs(date2 - date1) / 1000;                           // delta
var r = {};                                                                // result
var s = {                                                                  // structure
    year: 31536000,
    month: 2592000,
    week: 604800, // uncomment row to ignore
    day: 86400,   // feel free to add your own row
    hour: 3600,
    minute: 60,
    second: 1
};
if (unit){
	if (unit=='DAYS'){
		var nperiod = period * 86400;
		if (d >= nperiod){
			talert = true;
			
		}			
	}
}
Object.keys(s).forEach(function(key){
    r[key] = Math.floor(d / s[key]);
    d -= r[key] * s[key];
});

var sday =''
if (r['year']>0) { if (r['year'] == 1) {sday += r['year'] +' year '; } else {sday += r['year'] +' years ';}}
if (r['month']>0) { if (r['month'] == 1) {sday += r['month'] + ' month ';} else { sday += r['month'] + ' months ';}}
if (r['week']>0) { if (r['week'] == 1) {sday += r['week'] + ' week ';} else { sday += r['week'] + ' weeks ';}}
if (r['day']>0) { if (r['day']== 1) { sday += r['day'] + ' day ';} else { sday += r['day'] + ' days ';}}
if (r['hour']> 0) { if (r['hour'] == 1) {sday += r['hour'] + ' hour ';} else { sday += r['hour'] + ' hours ';}}
if (r['minute']>0) { if (r['minute'] ==1) {sday += r['minute'] + ' minute';} else { sday += r['minute'] + ' minutes ';}}
if (talert){
	sday = '<span class="label label-danger">'+sday+'</span>';
} else {
	sday = '<span class="label label-default">'+sday+'</span>';	
}

 return sday;
}
function initstatus(d){
var status = d['status'];
sstatus = '<span class="label label-default">'+d['status']+'</span>';
if (status =='RECEIVED'){
	sstatus = '<span class="label label-default">'+d['status']+'</span>';
}
if (status =='ONGOING'){
	sstatus = '<span class="label label-success">'+d['status']+'</span>';
}
if (status =='PENDING'){
	sstatus = '<span class="label label-danger">'+d['status']+'</span>';
}
return sstatus;
}
function gentkform(tk){
	$.get("webauthnController.php",{"trans":"gentk","tk":tk},function(data){$('input[name="tkform"]').val(data);},'json');
	return true;
}
function arrayBufferToBase64(buffer) {
            let binary = '';
            let bytes = new Uint8Array(buffer);
            let len = bytes.byteLength;
            for (let i = 0; i < len; i++) {
                binary += String.fromCharCode( bytes[ i ] );
            }
            return btoa(binary);
}
function arrayBufferToStr(buf) {
    return String.fromCharCode.apply(null, new Uint8Array(buf));
}