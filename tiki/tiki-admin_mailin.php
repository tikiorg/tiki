<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_mailin.php,v 1.12 2004-06-17 20:00:23 teedog Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/mailin/mailinlib.php');

//check if feature is on
if($feature_mailin != 'y') {
  $smarty->assign('msg', tra("This feature is disabled").": feature_mailin");
  $smarty->display("error.tpl");
  die;  
}
//check permissions
if ($tiki_p_admin_mailin != 'y' and $tiki_p_admin != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}	

// Add a new mail account for the user here  
if (!isset($_REQUEST["accountId"]))
	$_REQUEST["accountId"] = 0;

$smarty->assign('accountId', $_REQUEST["accountId"]);

if (isset($_REQUEST["new_acc"])) {
	check_ticket('admin-mailin');
	$mailinlib->replace_mailin_account($_REQUEST["accountId"], $_REQUEST["account"], $_REQUEST["pop"], $_REQUEST["port"],
		$_REQUEST["username"], $_REQUEST["pass"], $_REQUEST["smtp"], $_REQUEST["useAuth"], $_REQUEST["smtpPort"], $_REQUEST["type"],
		$_REQUEST["active"], $_REQUEST["anonymous"], $_REQUEST["attachments"], $_REQUEST["article_topicId"], $_REQUEST["article_type"]);
	$_REQUEST["accountId"] = 0;
}

if (isset($_REQUEST["remove"])) {
  $area = 'delmailin';
  if (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"])) {
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
    $mailin_autocheck = $_REQUEST["mailin_autocheck"];
    $mailin_autocheckFreq = $_REQUEST["mailin_autocheckFreq"];
  }
}
$smarty->assign('mailin_autocheck',$mailin_autocheck);
$smarty->assign('mailin_autocheckFreq',$mailin_autocheckFreq);

global $artlib;
if (!is_object($artlib)) {
	include_once('lib/articles/artlib.php');
}
$topics = $artlib->list_topics();
$smarty->assign_by_ref('topics', $topics);
$types = $artlib->list_types();
$smarty->assign_by_ref('types', $types);

ask_ticket('admin-mailin');

$smarty->assign('mid', 'tiki-admin_mailin.tpl');
$smarty->display("tiki.tpl");

?>
