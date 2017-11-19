<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/*
	 Tikiwiki CSRF protection.
	 also called Sea-Surfing

	 This protection is deprecated. Previous uses in the form...
	 if (isset($_REQUEST['perform_action']) {
	 	check_ticket('foo');
	 	restricted_modification();
	 }
	 ask_ticket('foo');

	 ...can now simply use...
	 $access->check_authenticity();
	 restricted_modification();
 */

/**
 * @param $area
 * @return bool
 * @deprecated. See above comment
 */
function ask_ticket($area)
{
	$_SESSION['antisurf'] = $area;
	return true;
}

/**
 * @param $area
 * @return bool
 * @deprecated. See above comment
 */
function check_ticket($area)
{
	if (! isset($_SESSION['antisurf'])) {
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
