<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

if (!isset($_REQUEST['partnerId'])) $_REQUEST['partnerId'] = $prefs['partnerId'];
if (!isset($_REQUEST['secret'])) $_REQUEST['secret'] = $prefs['secret'];
if (!isset($_REQUEST['adminSecret'])) $_REQUEST['adminSecret'] = $prefs['adminSecret'];
if (!isset($_REQUEST['kdpUIConf'])) $_REQUEST['kdpUIConf'] = $prefs['kdpUIConf'];
if (!isset($_REQUEST['kdpWidget'])) $_REQUEST['kdpWidget'] = $prefs['kdpWidget'];
if (!isset($_REQUEST['kcwUIConf'])) $_REQUEST['kcwUIConf'] = $prefs['kcwUIConf'];
if (!isset($_REQUEST['kseUIConf'])) $_REQUEST['kseUIConf'] = $prefs['kseUIConf'];
if (!isset($_REQUEST['kaeUIConf'])) $_REQUEST['kaeUIConf'] = $prefs['kaeUIConf'];

$tikilib->set_preference("partnerId", $_REQUEST['partnerId']);
$tikilib->set_preference("secret", $_REQUEST['secret']);
$tikilib->set_preference("adminSecret", $_REQUEST['adminSecret']);
$tikilib->set_preference("kdpUIConf", $_REQUEST['kdpUIConf']);
$tikilib->set_preference("kdpWidget", $_REQUEST['kdpWidget']);
$tikilib->set_preference("kcwUIConf", $_REQUEST['kcwUIConf']);
$tikilib->set_preference("kseUIConf", $_REQUEST['kseUIConf']);
$tikilib->set_preference("kaeUIConf", $_REQUEST['kaeUIConf']);

$smarty->assign('partnerId', $_REQUEST['partnerId']);
$smarty->assign('secret', $_REQUEST['secret']);
$smarty->assign('adminSecret', $_REQUEST['adminSecret']);
$smarty->assign('kdpUIConf', $_REQUEST['kdpUIConf']);
$smarty->assign('kdpWidget', $_REQUEST['kdpWidget']);
$smarty->assign('kcwUIConf', $_REQUEST['kcwUIConf']);
$smarty->assign('kseUIConf', $_REQUEST['kseUIConf']);
$smarty->assign('kaeUIConf', $_REQUEST['kaeUIConf']);
