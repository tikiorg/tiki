<?php
// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'mytiki';
require_once ('tiki-setup.php');
include_once ('lib/webmail/contactlib.php');

if ($feature_contacts != 'y') {
  $smarty->assign('msg', tra("This feature is disabled").": feature_contacts");
  $smarty->display("error.tpl");
  die;
}

if (!isset($_REQUEST["contactId"])) {
	$_REQUEST["contactId"] = 0;
}

$smarty->assign('contactId', $_REQUEST["contactId"]);

$tmpexts=$contactlib->get_ext_list($user);
$exts=array();
$traducted_exts=array();
foreach($tmpexts as $ext) $exts[bin2hex($ext)]=$ext;
foreach($exts as $k => $v) {
    $traducted_exts[$k]['tra']=tra($v);
    $traducted_exts[$k]['art']=$v;
}

if ($_REQUEST["contactId"]) {
	$info = $contactlib->get_contact($_REQUEST["contactId"], $user);
	foreach($info['ext'] as $ext => $value) {
	    if (!in_array($ext, $exts)) {
		$k=bin2hex($ext);
		$exts[$k]=$ext;
		$traducted_exts[$k]['tra']=tra($ext);
		$traducted_exts[$k]['art']=$ext;
	    }
	}
} else {
	$info = array();
	$info["firstName"] = '';
	$info["lastName"] = '';
	$info["email"] = '';
	$info["nickname"] = '';
	$info["groups"] = array();
}
$smarty->assign('info', $info);
$smarty->assign('exts', $traducted_exts);

if (isset($_REQUEST["remove"])) {
	if (!$user) {
		$smarty->assign('msg', tra("You are not logged in"));
		$smarty->display("error.tpl");
		die;
	}
	$area = "delwebmailcontact";
	if ($feature_ticketlib2 != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		key_check($area);
		$contactlib->remove_contact($_REQUEST["remove"], $user);
	} else {
		key_get($area);
	}
}

if (isset($_REQUEST["save"])) {
	if (!$user) {
		$smarty->assign('msg', tra("You are not logged in"));
		$smarty->display("error.tpl");
		die;
	}
	check_ticket('webmail-contact');
	$ext_result=array();
	foreach($exts as $k=>$ext)
	    $ext_result[$ext]=isset($_REQUEST['ext_'.$k]) ? $_REQUEST['ext_'.$k] : '';
	$contactlib->replace_contact($_REQUEST["contactId"], $_REQUEST["firstName"], $_REQUEST["lastName"], $_REQUEST["email"], $_REQUEST["nickname"], $user, $_REQUEST['groups'], $ext_result);
	$info["firstName"] = '';
	$info["lastName"] = '';
	$info["email"] = '';
	$info["nickname"] = '';
	$info["groups"] = array();
	$smarty->assign('info', $info);
	$smarty->assign('contactId', 0);
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'email_asc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
$smarty->assign_by_ref('sort_mode', $sort_mode);

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);
$maxRecords = 20;

$channels = $contactlib->list_contacts($user, $offset, $maxRecords, $sort_mode, $find, true, $_REQUEST["letter"]);
if ( is_array($channels['data']) ) foreach ( $channels['data'] as $c ) {
	if ( is_array($c['groups']) ) foreach ( $c['groups'] as $g ) $all['data'][$g][] = $c;
	if ( $c['user'] == $user ) $all_personnal[] = $c;
}

ksort($all['data']); // sort contacts by group name
$all['data']['user_personal_contacts'] =& $all_personnal; // this group needs to be the last one
$smarty->assign('all', $all['data']);

$groups = $userlib->get_user_groups($user);
$smarty->assign('groups', $groups);

$cant = $channels['cant'] + $all['cant'];
$cant_pages = ceil($cant / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));
if ($cant > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

$smarty->assign('letters', range('a','z'));
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

include_once ('tiki-section_options.php');

ask_ticket('contacts');

$smarty->assign('mid','tiki-contacts.tpl');
$smarty->display('tiki.tpl');
?>
