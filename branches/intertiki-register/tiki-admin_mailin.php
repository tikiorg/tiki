<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_mailin.php,v 1.23 2007-10-12 07:55:24 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/mailin/mailinlib.php');

//check if feature is on
if($prefs['feature_mailin'] != 'y') {
  $smarty->assign('msg', tra("This feature is disabled").": feature_mailin");
  $smarty->display("error.tpl");
  die;  
}
//check permissions
if ($tiki_p_admin_mailin != 'y' and $tiki_p_admin != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}	

function account_ok($pop, $user, $pass) {
	//include_once ("lib/webmail/pop3.php");
	include_once ("lib/webmail/net_pop3.php");
	//$pop3 = new POP3($pop, $user, $pass);
  //$pop3->Open();
	//$err = $pop3->has_error;
	//$pop3->close();
	$pop3 = new Net_POP3();
	$pop3->connect($pop);
	$pop3->login($user, $pass);
	if (!$pop3) {
		$pop3->disconnect();
		return false;
	} else {
		$pop3->disconnect();
		return true;
	}
}

// Add a new mail account for the user here  
if (!isset($_REQUEST["accountId"]))
	$_REQUEST["accountId"] = 0;

$smarty->assign('accountId', $_REQUEST["accountId"]);

if (isset($_REQUEST["new_acc"])) {
	check_ticket('admin-mailin');
	if (!account_ok($_REQUEST["pop"], $_REQUEST["username"], $_REQUEST["pass"]))
		$tikifeedback[] = array('num'=>1,'mes'=>sprintf(tra("Mail-in account %s incorrect"),$_REQUEST["account"]));
	else {
		$mailinlib->replace_mailin_account($_REQUEST["accountId"], $_REQUEST["account"], $_REQUEST["pop"], $_REQUEST["port"],
			$_REQUEST["username"], $_REQUEST["pass"], $_REQUEST["smtp"], $_REQUEST["useAuth"], $_REQUEST["smtpPort"], $_REQUEST["type"],
			$_REQUEST["active"], $_REQUEST["anonymous"], $_REQUEST["attachments"], $_REQUEST["article_topicId"], $_REQUEST["article_type"], $_REQUEST["discard_after"]);
//	$_REQUEST["accountId"] = 0;
		$tikifeedback[] = array('num'=>1,'mes'=>sprintf(tra("Mail-in account %s saved"),$_REQUEST["account"]));
	}
} else {
	$smarty->assign('confirmation', 0);
}

if (isset($_REQUEST["remove"])) {
  $area = 'delmailin';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$mailinlib->remove_mailin_account($_REQUEST["remove"]);
  } else {
    key_get($area);
  }

}

if ($_REQUEST["accountId"]) {
	$info = $mailinlib->get_mailin_account($_REQUEST["accountId"]);
} else {
	$info["account"] = '';
	$info["username"] = '';
	$info["pass"] = '';
	$info["pop"] = '';
	$info["smtp"] = '';
	$info["useAuth"] = 'n';
	$info["port"] = 110;
	$info["smtpPort"] = 25;
	$info["type"] = 'article-put';
	$info["active"] = 'y';
	$info["anonymous"] = 'y';
	$info["anonymous"] = 'y';
	$info["attachments"] = 'n';
	$info["article_topicId"] = '';
	$info["article_type"] = '';
}

$smarty->assign('info', $info);
// List  
$accounts = $mailinlib->list_mailin_accounts(0, -1, 'account_asc', '');
$smarty->assign('accounts', $accounts["data"]);

if (isset($_REQUEST["mailin_autocheck"]) ) {
  if($_REQUEST["mailin_autocheck"]=='y' && !(ereg("[0-9]+",$_REQUEST["mailin_autocheckFreq"]) && $_REQUEST["mailin_autocheckFreq"]>0)) {
    $smarty->assign('msg', tra("Frequency should be a positive integer!"));
    $smarty->display("error.tpl");
    die;  
  } else {
    $tikilib->set_preference("mailin_autocheck", $_REQUEST["mailin_autocheck"]);
    $tikilib->set_preference("mailin_autocheckFreq", $_REQUEST["mailin_autocheckFreq"]);
    if ($prefs['mailin_autocheck'] == 'y') {
	    $tikifeedback[] = array('num'=>1,'mes'=>sprintf(tra("Mail-in accounts set to be checked every %s minutes"),$prefs['mailin_autocheckFreq']));
    } else {
    	$tikifeedback[] = array('num'=>1,'mes'=>sprintf(tra("Automatic Mail-in accounts checking disabled")));
    }
  }
}

global $artlib;
if (!is_object($artlib)) {
	include_once('lib/articles/artlib.php');
}
$topics = $artlib->list_topics();
$smarty->assign_by_ref('topics', $topics);
$types = $artlib->list_types();
$smarty->assign_by_ref('types', $types);

$smarty->assign_by_ref('tikifeedback', $tikifeedback);

ask_ticket('admin-mailin');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-admin_mailin.tpl');
$smarty->display("tiki.tpl");

?>
