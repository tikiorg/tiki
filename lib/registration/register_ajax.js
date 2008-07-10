function check_name() {
	xajax_AJAXCheckUserName(document.getElementById('name').value);
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
	xajax_AJAXCheckMail(document.getElementById('email').value);
}
