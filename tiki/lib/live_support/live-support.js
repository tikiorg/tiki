/*** Shared functions ***/

function foo() {
	var ret = msg('http://localhost/tcvs/tiki/tiki-live_support_server.php?op_online=pepe');
	alert(ret);
}

function msg(msg) {
try {
  // for Mozilla
  req = new XMLHttpRequest();
  req.overrideMimeType("text/xml");
} catch (e) {
  // for IE5+
  req = new ActiveXObject("Msxml2.XMLHTTP");
}
req.open("GET",msg,false);
req.send(null);
return req.responseText;
}

function msgxml(msg) {
try {
  // for Mozilla
  req = new XMLHttpRequest();
  req.overrideMimeType("text/xml");
} catch (e) {
  // for IE5+
  req = new ActiveXObject("Msxml2.XMLHTTP");
}
req.open("GET",msg,false);
req.send(null);
return req.responseXML;
}

function write_msg(txt,role,name) {
	window.chat_data.document.write(txt);
	window.chat_data.document.write('<br />');
	document.getElementById('data').value='';
	/* And now send the message to the server */
	var ret = msg('http://localhost/tcvs/tiki/tiki-live_support_server.php?write=' + document.getElementById('reqId').value + '&msg=' + txt + '&senderId=' + document.getElementById('senderId').value + '&role=' + role + '&name=' + name);		
}

function event_poll() {
	evpollInterval = setInterval("pollForEvents()", 5000);
}

function pollForEvents() {
	var ret = msg('http://localhost/tcvs/tiki/tiki-live_support_server.php?get_last_event=' + document.getElementById('reqId').value + '&senderId=' + document.getElementById('senderId').value);	
	/* alert(ret);
	alert(last_event); */
	if(ret > last_event) {
		while(last_event < ret) {
			last_event = last_event + 1;
			var txt = msg('http://localhost/tcvs/tiki/tiki-live_support_server.php?get_event=' + document.getElementById('reqId').value + '&last=' + last_event + '&senderId=' + document.getElementById('senderId').value);			
			if(txt) {
				window.chat_data.document.write(txt);
				window.chat_data.document.write('<br />');			
			}
		}
	}
}

/*** Client window functions ***/
function request_chat(user,tiki_user,email,reason) {
    document.getElementById('username').value=document.getElementById('user').value;
	document.getElementById('request_chat').style.display='none';
	document.getElementById('requesting_chat').style.display='block';
	var ret = msg('http://localhost/tcvs/tiki/tiki-live_support_server.php?request_chat=1&reason=' + reason + '&user=' + user + '&tiki_user=' + tiki_user + '&email=' + email + '&user_id=' + document.getElementById('senderId').value);
	document.getElementById('reqId').value = ret;
	client_poll();
}

function client_poll() {
  clourInterval = setInterval("pollForAccept()", 5000);
}

function pollForAccept() {
	var ret = msg('http://localhost/tcvs/tiki/tiki-live_support_server.php?get_status=' + document.getElementById('reqId').value);	
	if(ret == 'op_accepted') {
		clearInterval(clourInterval);
		document.getElementById('requesting_chat').style.display='none';
		document.getElementById('chat').style.display='block';
		event_poll();
	}
}

function client_close() {
  msg('http://localhost/tcvs/tiki/tiki-live_support_server.php?client_close=' + document.getElementById('reqId').value);
}

/*** Operator window function ***/
function operator_close() {
  msg('http://localhost/tcvs/tiki/tiki-live_support_server.php?operator_close=' + document.getElementById('reqId').value);
}

/*** Operator console functions ***/

function pollForRequests() {
	var last = msg('http://localhost/tcvs/tiki/tiki-live_support_server.php?poll_requests=1');
	if (last > last_req) {
	  window.location.reload();
	  last_req = last;
	}
}

function console_poll() {
  var ourInterval = setInterval("pollForRequests()", 10000);
}

var clourInterval = null;