<?php 

function validator_password($input, $parameter = '', $message = '') {
	global $userlib;
	$errors = $userlib->check_password_policy($input);
	if (!$errors) {
		return true;
	} else {
		return $errors;
	}
}



