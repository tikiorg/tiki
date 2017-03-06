<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
	 Tikiwiki CSRF protection.
	 also called Sea-Surfing

	 please report to security@tikiwiki.org
	 if you find a better way to handle sea surfing nastiness
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 * @param $area
 * @return bool
 * @deprecated in favor of check_authenticity in tikiaccesslib.php, which uses the key_get function
 */
function ask_ticket($area)
{
	$_SESSION['antisurf'] = $area;
	return true;
}

/**
 * @param $area
 * @return bool
 * @deprecated in favor of check_authenticity in tikiaccesslib.php, which uses the key_check function
 */
function check_ticket($area)
{
	if (!isset($_SESSION['antisurf'])) {
		$_SESSION['antisurf'] = '';
	}
	if ($_SESSION['antisurf'] != $area) {
		global $prefs;
		$_SESSION['antisurf'] = $area;
		if ($prefs['feature_ticketlib'] == 'y') {
			$smarty = TikiLib::lib('smarty');
			$smarty->assign('post', $_POST);
			$smarty->assign('query', $_SERVER['QUERY_STRING']);
			$smarty->assign('self', $_SERVER['PHP_SELF']);
			$smarty->assign('msg', tra('Possible cross-site request forgery (CSRF, or "sea surfing") detected. Operation blocked.'));
			$smarty->display('error_ticket.tpl');
			die;
		}
	}

	return true;
}

