<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

class LoginLib
{
	function activateSession($user)
	{
		global $user_cookie_site;
		$_SESSION[$user_cookie_site] = $user;
	}

	function switchUser($name)
	{
		global $user, $user_cookie_site;
		$perms = Perms::get();

		if (! $perms->admin) {
			return;
		}

		$userlib = TikiLib::lib('user');
		$username = $userlib->get_user_real_case($name);
		$this->activateSession($username);
		$_SESSION[$user_cookie_site . '_previous'] = $user;
	}

	function revertSwitch()
	{
		global $user_cookie_site;
		$key = $user_cookie_site . '_previous';
		$username = $_SESSION[$key];
		unset($_SESSION[$key]);
		$this->activateSession($username);
	}

	function isSwitched()
	{
		global $user_cookie_site;
		return isset($_SESSION[$user_cookie_site . '_previous']);
	}
}
