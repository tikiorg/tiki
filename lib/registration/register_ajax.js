var cp = new cpaint();
cp.set_response_type('XML');
cp.set_debug(0);
var nameSequence = "0";
var mailSequence = "0";

function check_name() {
	nameSequence++; 
	cp.call('tiki-register_ajax.php', 'AJAXCheckUserName', name_result, document.getElementById('name').value, nameSequence);
}

function name_result(result) {
	if (result.getElementsByTagName('nameSequence').item(0).firstChild.data == nameSequence) {
		document.getElementById('checkfield').innerHTML = result.getElementsByTagName('nameMessage').item(0).firstChild.data;
	}
}

// This is not AJAX, but fits here - needs cleanup though (hardcoded colors, etc.)
function check_pass() {
	pass1 = document.getElementById('pass1').value;
	pass2 = document.getElementById('pass2').value;
	if ((pass1 == '') || (pass2 == '')) {
		document.getElementById('checkpass').innerHTML = '';
	} else if ( pass1 == pass2 ) {
		document.getElementById('checkpass').style.color = '#339933';
		document.getElementById('checkpass').innerHTML = 'Passwords match';
	} else if (document.getElementById('pass1').value != document.getElementById('pass2').value) {
		document.getElementById('checkpass').style.color = '#FF0000';
		document.getElementById('checkpass').innerHTML = 'Passwords don\'t match';	
	}
}

function check_mail() {
	mailSequence++;
	cp.call('tiki-register_ajax.php', 'AJAXCheckMail', mail_result, document.getElementById('email').value, mailSequence);
}

function mail_result(result) {
	if (result.getElementsByTagName('mailSequence').item(0).firstChild.data == mailSequence) {
		document.getElementById('checkmail').innerHTML = result.getElementsByTagName('mailMessage').item(0).firstChild.data;
	}
}