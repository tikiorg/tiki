<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_mailin.php,v 1.5 2003-12-15 18:19:23 bburgaud Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
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
if ($tiki_p_admin_mailin != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

// Add a new mail account for the user here  
if (!isset($_REQUEST["accountId"]))
	$_REQUEST["accountId"] = 0;

$smarty->assign('accountId', $_REQUEST["accountId"]);

if (isset($_REQUEST["new_acc"])) {
	$mailinlib->replace_mailin_account($_REQUEST["accountId"], $_REQUEST["account"], $_REQUEST["pop"], $_REQUEST["port"],
		$_REQUEST["username"], $_REQUEST["pass"], $_REQUEST["smtp"], $_REQUEST["useAuth"], $_REQUEST["smtpPort"], $_REQUEST["type"],
		$_REQUEST["active"], $_REQUEST["anonymous"]);

	$_REQUEST["accountId"] = 0;
}

if (isset($_REQUEST["remove"])) {
	$mailinlib->remove_mailin_account($_REQUEST["remove"]);
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
	$info["type"] = 'wiki-get';
	$info["active"] = 'y';
	$info["anonymous"] = 'y';
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

$smarty->assign('mid', 'tiki-admin_mailin.tpl');
$smarty->display("tiki.tpl");

?>