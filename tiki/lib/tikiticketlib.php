<?php
/* $Header: /cvsroot/tikiwiki/tiki/lib/tikiticketlib.php,v 1.8 2004-03-31 07:38:43 mose Exp $

Tikiwiki CSRF protection.
also called Sea-Surfing

please report to security@tikiwiki.org 
if you find a better way to handle sea surfing nastiness
*/

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}

// obsolete: will be removed soon
function ask_ticket($area) {
	//$_SESSION['antisurf'] =  $area; 
	return true;
}

// obsolete: will be removed soon
function check_ticket($area) { 
	/*
	if (!isset($_SESSION['antisurf'])) $_SESSION['antisurf'] = '';
	if ($_SESSION['antisurf'] != $area) { 
		global $smarty, $feature_ticketlib;
		$_SESSION['antisurf'] =  $area; 
		if ($feature_ticketlib == 'y') {
		$smarty->assign('post',$_POST);
		$smarty->assign('query',$_SERVER["QUERY_STRING"]);
		$smarty->assign('self',$_SERVER["PHP_SELF"]);
		$smarty->assign('msg',tra("Sea Surfing (CSRF) detected. Operation blocked."));
		$smarty->display('error_ticket.tpl');
		die;
		}
	}
	*/
	return true;
}

// new valid function for ticketing :

function key_get($area) {
	global $smarty;
	$_SESSION["ticket_$area"] = time();
	$smarty->assign('confirmaction', $_SERVER['REQUEST_URI']);
	$smarty->display("confirm.tpl");
	die();
}

function key_check($area) {
	global $_SESSION;
	if (isset($_SESSION["ticket_$area"])
		and $_SESSION["ticket_$area"] < date('U')
		and $_SESSION["ticket_$area"] > (date('U')-(60*15))) {
		return true;
	}
	global $smarty;
	unset($_SESSION["ticket_$area"]);
	$smarty->assign('msg',tra('Sea Surfing (CSRF) detected. Operation blocked.'));
	$smarty->assign('nocreate',1);
	$smarty->display("error.tpl");
	die();
}

?>
