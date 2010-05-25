<?php 

function validator_regex($input, $parameter = '') {
	$times = preg_match("/$parameter/", $input, $matches);
	if (!$times || $matches[0] != $input) {
		return false;
	} else {	
		return true;
	}
}



