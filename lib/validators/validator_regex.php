<?php 

function validator_regex($input, $parameter = '', $message = '') {
	$times = preg_match("/$parameter/", $input, $matches);
	if (!$times || $matches[0] != $input) {
		if ($message) {
			return tra($message);
		} else {
			return false;	
		}
	} else {	
		return true;
	}
}



