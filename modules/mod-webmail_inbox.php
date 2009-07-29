<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],basename(__FILE__)) !== false) {
  header('location: index.php');
  exit;
}

if (!$user) {
	$smarty->assign('tpl_module_title', tra('Webmail error'));
	$smarty->assign('error', 'You are not logged in');
	return;	// modules cannot "exit", they must "return" to keep tiki alive
}

global $prefs;
if ($prefs['feature_webmail'] != 'y') {
	$smarty->assign('tpl_module_title', tra('Webmail error'));
	$smarty->assign('error', 'This feature is disabled');
	return;
}
global $tiki_p_use_webmail, $tiki_p_use_group_webmail;
if ($tiki_p_use_webmail != 'y' && $tiki_p_use_group_webmail != 'y') {
	$smarty->assign('tpl_module_title', tra('Webmail error'));
	$smarty->assign('error', 'Permission denied to use this feature');
	return;
}

if ($prefs['feature_ajax'] == 'y') {
	// this includes what we need for ajax
	require_once ('tiki-webmail_ajax.php');
} else  {
	require_once $smarty->_get_plugin_filepath('function', 'icon');
	$smarty->assign('tpl_module_title', tra('Webmail error'));
	$smarty->assign('error', tra('AJAX feature required').'&nbsp;'.
		'<a href="tiki-admin.php?page=features">'.smarty_function_icon(array('_id'=>'arrow_right'), $smarty)).'</a>';
	return;
}

global $webmaillib, $headerlib, $user, $webmail_reload, $webmail_start;
include_once ('lib/webmail/webmaillib.php');


// get autoRefresh val from account so it can go into the page JS
if (isset($module_params['accountid'])) {
	$webmail_account = $webmaillib->get_webmail_account($user, $module_params['accountid']);
} else {
	$webmail_account = $webmaillib->get_current_webmail_account($user);
}

if ($webmail_account && $webmail_account['autoRefresh'] > 0) {
	$headerlib->add_js('var autoRefresh = '.($webmail_account['autoRefresh'] * 1000).';');
}
$webmail_reload = (isset($module_params['reload']) && $module_params['reload'] == 'y');
$webmail_start = isset($_SESSION['webmailinbox'][$module_params['module_id']]['start']) ? $_SESSION['webmailinbox'][$module_params['module_id']]['start'] : 0;


global $webmail_list_page;

if (!function_exists('webmail_refresh')) {
function webmail_refresh() {	// called in ajax mode
	global $webmaillib, $user, $smarty, $webmail_list_page, $webmail_account, $webmail_reload, $webmail_start, $module_params, $trklib, $contactlib;
	include_once('lib/trackers/trackerlib.php');
	include_once ('lib/webmail/contactlib.php');
	
	$accountid = isset($module_params['accountid']) ? $module_params['accountid'] : 0;
	$webmail_account = $webmaillib->get_webmail_account($user, $accountid);
	
	try {
		$webmail_list = $webmaillib->refresh_mailbox($user, $accountid, $webmail_reload);
	} catch (Exception $e) {
		$err = $e->getMessage();
		$smarty->assign('tpl_module_title', tra('Webmail error'));
		$smarty->assign('error', $err);
		return;
	}
	
	if (!$webmail_account) {
		require_once $smarty->_get_plugin_filepath('function', 'icon');
		$smarty->assign('tpl_module_title', tra('Webmail error'));
		$smarty->assign('error', tra('No accounts set up (or no current account set)').'&nbsp;'.
			'<a href="tiki-webmail.php?locSection=settings">'.smarty_function_icon(array('_id'=>'arrow_right'), $smarty)).'</a>';
		return;
	}
	
	$mailsum = count($webmail_list);
	
	if ($webmail_start < 1 || $webmail_start > $mailsum)
		$webmail_start = $mailsum;

	$upperlimit = $webmail_start;
	$smarty->assign('start', $webmail_start);
	$numshow = isset($module_params['rows']) ? $module_params['rows'] : $webmail_account['msgs'];
	
	$webmail_list_page = Array();
	
	for ($i = $webmail_start - 1; $i > -1 && $i > $upperlimit - $numshow - 1; $i--) {
		$a_mail = $webmail_list[$i];
		$webmaillib->replace_webmail_message($webmail_account['accountId'], $user, $a_mail['realmsgid']);
		list($a_mail['isRead'], $a_mail['isFlagged'], $a_mail['isReplied']) = $webmaillib->get_mail_flags($webmail_account['accountId'], $user, $a_mail['realmsgid']);
		
		// handle take/taken operator here
		$itemid = $trklib->get_item_id( $module_params['trackerId'], $module_params['messageFId'], $a_mail['realmsgid']);
		if ($itemid > 0) {
			$a_mail['operator'] = $trklib->get_item_value($module_params['trackerId'], $itemid, $module_params['operatorFId']);
		} else {
			$a_mail['operator'] = '';
		}
		
		// check if sender is in contacts
		$a_mail['sender']['contactId'] = $contactlib->get_contactId_email($a_mail['sender']['email'], $user);
		// check if there's a wiki page
		$ext = $contactlib->get_ext_by_name($user, tra('Wiki Page'), $a_mail['sender']['contactId']);
		if ($ext) {
			$a_mail['sender']['wikiPage'] = $contactlib->get_contact_ext_val($user, $a_mail['sender']['contactId'], $ext['fieldId']);
		}
				
		$webmail_list_page[] = $a_mail;
	}
	
	$lowerlimit = $i;

	if ($lowerlimit < 0) {
		$lowerlimit = 0;
	}
	$showstart = $mailsum - $upperlimit + 1;
	$showend = $mailsum - $lowerlimit;
//	$smarty->assign('showstart', $showstart);
//	$smarty->assign('showend', $showend);
//	$smarty->assign('total', $mailsum);
//	$smarty->assign('current', $webmail_account);
//	$smarty->assign('flagsPublic',$webmail_account['flagsPublic']);
	
	
	if ($lowerlimit > 0) {
		$smarty->assign('nextstart', $lowerlimit);
	} else {
		$smarty->assign('nextstart', '');
	}

	if ($upperlimit <> $mailsum) {
		$prevstart = $upperlimit + $numshow;

		if ($prevstart > $mailsum)
			$prevstart = $mailsum;

		$smarty->assign('prevstart', $prevstart);
	} else {
		$smarty->assign('prevstart', '');
	}

//	if ($_REQUEST['start'] <> $mailsum) {
//		$smarty->assign('first', $mailsum);
//	} else {
//		$smarty->assign('first', '');
//	}
//
//	// Now calculate the last message block
//	$last = $mailsum % $numshow;
//
//	if ($_REQUEST['start'] <> $last) {
//		$smarty->assign('last', $last);
//	} else {
//		$smarty->assign('last', '');
//	}
	
}
}	// endif function_exists 'webmail_refresh'

if (isset($_REQUEST['refresh_mail']) || (isset($_REQUEST['xjxfun']) && $_REQUEST['xjxfun'] == 'refreshWebmail')) {	// YUK!
	webmail_refresh();
}

$module_params['autoloaddelay'] = isset($module_params['autoloaddelay']) ? isset($module_params['autoloaddelay']) : 1;
if ($module_params['autoloaddelay'] > -1) {
	$headerlib->add_js('setTimeout("doRefreshWebmail()", '.($module_params["autoloaddelay"] * 1000).');');
}

$smarty->assign('webmail_list', $webmail_list_page);

$smarty->assign_by_ref('module_params', $module_params); // re-assigning this to cater for AJAX reloads
$smarty->assign('maxlen', isset($module_params['maxlen']) ? $module_params['maxlen'] : 30);
$smarty->assign('nonums', isset($module_params['nonums']) ? $module_params['nonums'] : 'n');
$smarty->assign('request_uri', strpos($_SERVER['REQUEST_URI'], '?') === false ? $_SERVER['REQUEST_URI'].'?' : $_SERVER['REQUEST_URI'].'&');
$module_rows = count($webmail_list_page);
$smarty->assign('module_type', 'module');
$smarty->assign('module_rows', $module_rows);
if (isset($module_params['title'])) {
	$smarty->assign('tpl_module_title', $module_params['title']);
}
