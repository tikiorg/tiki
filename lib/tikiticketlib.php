<?php
/* $Id: /cvsroot/tikiwiki/tiki/lib/tikiticketlib.php,v 1.24 2007-10-12 07:55:38 nyloth Exp $

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

function ask_ticket($area) {
	$_SESSION['antisurf'] = $area; 
	return true;
}

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

// new valid function for ticketing :

function key_get($area, $confirmation_text = '', $confirmaction='') {
//confirmaction actin must be set if the param are not transfer via the URI
	global $tikilib,$smarty,$prefs,$user;
	if ($prefs['feature_ticketlib2'] == 'y') {
		if ($user) {
			$whose = $user;
		} else { 
  		$whose = ' '. md5($_SERVER['REMOTE_ADDR'].$_SERVER['USER_AGENT']);
		}
		$ticket = md5(uniqid(rand()));
		$tikilib->set_user_preference($whose,'ticket',$ticket);
		$smarty->assign('ticket',$ticket);
		$_SESSION["ticket_$area"] = time();
		if (empty($confirmation_text)) {
			$confirmation_text = tra('Click here to confirm your action');
		}
		if (empty($confirmaction)) {
			$confirmaction = $_SERVER['REQUEST_URI'];
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

function key_check($area) {
	global $tikilib,$smarty,$prefs,$user;
	if ($prefs['feature_ticketlib2'] != 'y') {
		return true;
	} else {
		if (isset($_SESSION["ticket_$area"])
			and $_SESSION["ticket_$area"] < date('U')
			and $_SESSION["ticket_$area"] > (date('U')-(60*15))) {
			$smarty->load_filter('pre', 'ticket');
			if ($user) {
				$whose = $user;
			} else { 
				$whose = ' '. md5($_SERVER['REMOTE_ADDR'].$_SERVER['USER_AGENT']);
			}
			if (isset($_REQUEST) and is_array($_REQUEST)
				and (!isset($_REQUEST['ticket']) 
				or $_REQUEST['ticket'] != $tikilib->get_user_preference($whose,'ticket'))) {
				unset($_REQUEST);
			} else {
				return true;
			}
		}
		unset($_SESSION["ticket_$area"]);
		$smarty->assign('msg',tra('Sea Surfing (CSRF) detected. Operation blocked.'));
		$smarty->display("error.tpl");
		die();
	}
}

?>
