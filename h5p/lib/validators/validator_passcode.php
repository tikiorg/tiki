<?php 
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function validator_passcode($input, $parameter = '', $message = '')
{
	global $prefs;
	if ($input == $prefs['registerPasscode']) {
		return true;
	} else {
		return tra('Wrong passcode. You need to know the passcode to register at this site');
	}
}



