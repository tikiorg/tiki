<?php 
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function validator_username($input, $parameter = '', $message = '')
{
	global $userlib, $prefs;
	if ($userlib->user_exists($input)) {
		return tra("User already exists");
	}
	if (!empty($prefs['username_pattern']) && !preg_match($prefs['username_pattern'], $input)) {
		return tra("Invalid character combination for username");
	}
	if (strtolower($input) == 'anonymous' || strtolower($input) == 'registered') {
		return tra("Invalid username");
	}
	if (strlen($input) > $prefs['max_username_length']) {
		$error = tra("Username cannot contain more than") . ' ' . $prefs['max_username_length'] . ' ' . tra("characters");
		return $error;
	}
	if (strlen($input) < $prefs['min_username_length']) {
		$error = tra("Username must be at least") . ' ' . $prefs['min_username_length'] . ' ' . tra("characters long");
		return $error;
	}
	return true;
}
