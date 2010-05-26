
function check_name() {
	xajax.config.requestURI = "tiki-register.php";
	xajax_chkRegName(xajax.$('name').value);
}

// This is not AJAX, but fits here - needs cleanup though (hardcoded colors, etc.)
function check_pass() {
	xajax.config.requestURI = "tiki-register.php";
	pass1 = document.getElementById('pass1').value;
	pass2 = document.getElementById('pass2').value;
	if ((pass1 === '') || (pass2 === '')) {
		xajax.$('checkpass').innerHTML = '';
	} else if (pass1 !== pass2) {
		xajax.$('checkpass').innerHTML = "<img src='pics/icons/exclamation.png' style='vertical-align:middle' alt='Do not match' /> Passwords don\'t match";	
	} else if (pass1 === pass2) {
		xajax.$('checkpass').innerHTML = "<img src='pics/icons/accept.png' style='vertical-align:middle' alt='Match' /> Passwords match";
	}
}

function check_mail() {
	xajax.config.requestURI = "tiki-register.php";
	xajax_chkRegEmail(xajax.$('email').value);
}
