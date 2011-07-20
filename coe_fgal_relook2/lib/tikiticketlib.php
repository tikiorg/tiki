<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// Deprecated in favor of key_get($area)
function ask_ticket($area) {
	$_SESSION['antisurf'] = $area; 
	return true;
}

// Deprecated in favor of key_check($area)
function check_ticket($area) { 
	if (!isset($_SESSION['antisurf'])) $_SESSION['antisurf'] = '';
	if ($_SESSION['antisurf'] != $area) { 
		global $smarty, $prefs;
		$_SESSION['antisurf'] =  $area; 
		if ($prefs['feature_ticketlib'] == 'y') {
			$smarty->assign('post',$_POST);
			$smarty->assign('query',$_SERVER["QUERY_STRING"]);
			$smarty->assign('self',$_SERVER["PHP_SELF"]);
			$smarty->assign('msg',tra("Sea Surfing (CSRF) detected. Operation blocked."));
			$smarty->display('error_ticket.tpl');
			die;
		}
	}
	return true;
}

// new valid functions for ticketing:
	 // * @param string $area No more used
function key_get($area = null, $confirmation_text = '', $confirmaction='') {
	global $tikilib,$smarty,$prefs;
	if ($prefs['feature_ticketlib2'] == 'y') {
		$ticket = md5(uniqid(rand()));
		$_SESSION['tickets'][$ticket] = time();
		$smarty->assign('ticket',$ticket);
		if (empty($confirmation_text)) {
			$confirmation_text = tra('Click here to confirm your action');
		}

		if (empty($confirmaction)) {
			$confirmaction = $_SERVER['PHP_SELF'];
		}

		// Display the confirmation in the main tiki.tpl template
		$smarty->assign('post',$_POST);
		$smarty->assign('dblclickedit','n');
		$smarty->assign('print_page','n');
		$smarty->assign('confirmation_text', $confirmation_text);
		$smarty->assign('confirmaction', $confirmaction);
		$smarty->assign('mid','confirm.tpl');
		$smarty->display("tiki.tpl");
		die();
	}
}
	 // * @param string $area No more used
function key_check($area = null) {
	global $smarty, $prefs;
	if ($prefs['feature_ticketlib2'] == 'y') {
		if (isset($_REQUEST['ticket']) && isset($_SESSION["tickets"][$_REQUEST['ticket']])) {
			$time = $_SESSION["tickets"][$_REQUEST['ticket']];
			if ($time < time() && $time > (time()-(60*15))) {
				return true;
			}
		}
		$smarty->assign('msg',tra('Sea Surfing (CSRF) detected. Operation blocked.'));
		$smarty->display("error.tpl");
		exit();
	}
}
