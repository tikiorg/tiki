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
		document.getElementById('checkpass').style.color = '#000000';
		document.getElementById('checkpass').innerHTML = "<img src='pics/icons/accept.png' style='vertical-align:middle' alt='Match' /> Passwords match";
	} else if (document.getElementById('pass1').value != document.getElementById('pass2').value) {
		document.getElementById('checkpass').style.color = '#000000';
		document.getElementById('checkpass').innerHTML = "<img src='pics/icons/exclamation.png' style='vertical-align:middle' alt='Do not match' /> Passwords don\'t match";	
	}
}

function check_mail() {
	xajax_AJAXCheckMail(document.getElementById('email').value);
}
