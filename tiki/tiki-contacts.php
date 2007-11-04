<?php
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'mytiki';
require_once ('tiki-setup.php');
if ($prefs['feature_ajax'] == "y") {
require_once ('lib/ajax/ajaxlib.php');
}
include_once ('lib/webmail/contactlib.php');

if ($prefs['feature_contacts'] != 'y') {
  $smarty->assign('msg', tra("This feature is disabled").": feature_contacts");
  $smarty->display("error.tpl");
  die;
}

if (!isset($_REQUEST["contactId"])) {
	$_REQUEST["contactId"] = 0;
}
$smarty->assign('contactId', $_REQUEST["contactId"]);

$exts=$contactlib->get_ext_list($user);
$traducted_exts=array();
foreach($exts as $ext) {
	$traducted_exts[$ext['fieldId']] = array(
    		'tra' => tra($ext['fieldname']),
		'art' => $ext['fieldname'],
		'id' => $ext['fieldId'],
		'show' => $ext['show']
	);
}

if ($_REQUEST["contactId"]) {
	$info = $contactlib->get_contact($_REQUEST["contactId"], $user);
	foreach($info['ext'] as $k => $v) {
	    if (!in_array($k, array_keys($exts))) {
		$exts[$k]=$v;
		$traducted_exts[$k]['tra']=tra($info['fieldname']);
		$traducted_exts[$k]['art']=$info['fieldname'];
		$traducted_exts[$k]['id']=$k;
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
	if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
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
	foreach($exts as $ext)
		$ext_result[$ext['fieldId']] = isset($_REQUEST['ext_'.$ext['fieldId']]) ? $_REQUEST['ext_'.$ext['fieldId']] : '';
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

$contacts = $contactlib->list_contacts($user, $offset, $maxRecords, $sort_mode, $find, true, $_REQUEST["letter"]);

if ( isset($_REQUEST['view']) ) $_SESSION['UserContactsView'] = $_REQUEST['view'];
elseif ( ! isset($_SESSION['UserContactsView']) ) $_SESSION['UserContactsView'] = $userlib->get_user_preference($user, 'user_contacts_default_view');
$smarty->assign('view', $_SESSION['UserContactsView']);

if ( is_array($contacts) ) {

	if ( $_SESSION['UserContactsView'] == 'list' ) {
		$smarty->assign('all', array($contacts));
		$cant = count($contacts);
	} else {
		// ordering contacts by groups
		$all=array();
		$all_personnal=array();
		$cant = 0;

		foreach ( $contacts as $c ) {
			if ( is_array($c['groups']) ) {
				foreach ( $c['groups'] as $g ) {
					$all[$g][] = $c;
					$cant++;
				}
			}

			if ( $c['user'] == $user ) {
				$all_personnal[] = $c;
				$cant++;
			}
		}
	
		// sort contacts by group name
		ksort($all);
	
		// this group needs to be the last one
		$all['user_personal_contacts'] =& $all_personnal;

		$smarty->assign('all', $all);
	}

}

$groups = $userlib->get_user_groups($user);
$smarty->assign('groups', $groups);

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
if ($prefs['feature_ajax'] == "y") {
function user_contacts_ajax() {
    global $ajaxlib, $xajax;
    $ajaxlib->registerTemplate("tiki-contacts.tpl");
    $ajaxlib->registerFunction("loadComponent");
    $ajaxlib->processRequests();
}
user_contacts_ajax();
$smarty->assign("mootab",'y');
}
$smarty->assign('myurl', 'tiki-contacts.php');

$smarty->assign('mid','tiki-contacts.tpl');
$smarty->display('tiki.tpl');
?>
