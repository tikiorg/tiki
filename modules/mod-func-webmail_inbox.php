<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 * @return array
 */
function module_webmail_inbox_info()
{
	return array(
		'name' => tra('Webmail Inbox'),
		'description' => tra('Displays Webmail Inbox.'),
		'prefs' => array('feature_webmail', 'feature_ajax'),
		'params' => array(
			'accountid' => array(
				'name' => tra('Account Id'),
				'description' => tra('Webmail account identifier (if not set uses user\'s current account)'),
				'filter' => 'int'
			),
			'mode' => array(
				'name' => tra('Mode'),
				'description' => tra('Mode.') . ' ' . tra('Possible values:') . ' "webmail", "groupmail".',
				'filter' => 'word'
			),
			'group' => array(
				'name' => tra('Group'),
				'description' => tra('GroupMail: Group (e.g. "Help Team")'),
				'filter' => 'striptags'
			),
			'trackerId' => array(
				'name' => tra('Tracker ID'),
				'description' => tra('GroupMail: Tracker ID (to store GroupMail activity)'),
				'filter' => 'int',
				'profile_reference' => 'tracker',
			),
			'fromFId' => array(
				'name' => tra('From Field ID'),
				'description' => tra('GroupMail: From Field (Id of field in tracker to store email From header)'),
				'filter' => 'int',
				'profile_reference' => 'tracker_field',
			),
			'subjectFId' => array(
				'name' => tra('Subject Field ID'),
				'description' => tra('GroupMail: Subject Field (Id of field in tracker to store email Subject header)'),
				'filter' => 'int',
				'profile_reference' => 'tracker_field',
			),
			'messageFId' => array(
				'name' => tra('Message Field ID'),
				'description' => tra('GroupMail: Message Field (Id of field in tracker to store email message identifier)'),
				'filter' => 'int',
				'profile_reference' => 'tracker_field',
			),
			'contentFId' => array(
				'name' => tra('Content Field ID'),
				'description' => tra('GroupMail: Content Field (Id of field in tracker to store email message body content)'),
				'filter' => 'int',
				'profile_reference' => 'tracker_field',
			),
			'accountFId' => array(
				'name' => tra('Account Field ID'),
				'description' => tra('GroupMail: Account Field (Id of field in tracker to store Webmail account name)'),
				'filter' => 'int',
				'profile_reference' => 'tracker_field',
			),
			'datetimeFId' => array(
				'name' => tra('DateTime Field Id'),
				'description' => tra('GroupMail: Date Time Field (Id of field in tracker to store email sent timestamp)'),
				'filter' => 'int',
				'profile_reference' => 'tracker_field',
			),
			'operatorFId' => array(
				'name' => tra('Operator Field ID'),
				'description' => tra('GroupMail: Operator Field (Id of field in tracker to store operator name (username))'),
				'filter' => 'int',
				'profile_reference' => 'tracker_field',
			),
			'maxlen' => array(
				'name' => tra('Maximum length'),
				'description' => tra('Maximum number of characters in subjects allowed before truncating.'),
				'filter' => 'int'
			),
		),
		'common_params' => array(
			'rows',
			'nonums',
		),
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_webmail_inbox($mod_reference, $module_params)
{
	global $prefs, $webmaillib, $user, $webmail_reload, $webmail_start, $webmail_list_page;
	$headerlib = TikiLib::lib('header');
	$smarty = TikiLib::lib('smarty');
	if (!$user) {
		$smarty->assign('tpl_module_title', tra('Webmail error'));
		$smarty->assign('error', 'You are not logged in');
		return;	// modules cannot "exit", they must "return" to keep tiki alive
	}

	global $tiki_p_use_webmail, $tiki_p_use_group_webmail;

	if ($tiki_p_use_webmail != 'y' && $tiki_p_use_group_webmail != 'y') {
		$smarty->assign('tpl_module_title', tra('Webmail error'));
		$smarty->assign('error', 'You do not have permission to use this feature');
		return;
	}

	require_once ('tiki-webmail_ajax.php');
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
	$webmail_start = isset($_SESSION['webmailinbox'][$mod_reference['moduleId']]['start']) ? $_SESSION['webmailinbox'][$mod_reference['moduleId']]['start'] : 0;

	if (isset($_REQUEST['refresh_mail'])) {
		webmail_refresh();
	}

	$module_params['autoloaddelay'] = isset($module_params['autoloaddelay']) ? isset($module_params['autoloaddelay']) : 1;
	if ($module_params['autoloaddelay'] > -1) {
		$headerlib->add_js('setTimeout("doRefreshWebmail()", '.($module_params["autoloaddelay"] * 1000).');');
	}

	$smarty->assign('webmail_list', $webmail_list_page);

	$smarty->assign_by_ref('module_params', $module_params); // re-assigning this to cater for AJAX reloads
	$smarty->assign('maxlen', isset($module_params['maxlen']) ? $module_params['maxlen'] : 30);
	$smarty->assign('request_uri', strpos($_SERVER['REQUEST_URI'], '?') === false ? $_SERVER['REQUEST_URI'].'?' : $_SERVER['REQUEST_URI'].'&');
	$module_rows = count($webmail_list_page);
	$smarty->assign('module_type', 'module');
	$smarty->assign('module_rows', $module_rows);
}

function webmail_refresh() 	// called in ajax mode
{
	global $webmaillib, $user, $webmail_list_page, $webmail_account, $webmail_reload, $webmail_start, $module_params;
	$trklib = TikiLib::lib('trk');
	$contactlib = TikiLib::lib('contact');
	$smarty = TikiLib::lib('smarty');

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
		$smarty->loadPlugin('smarty_function_icon');
		$smarty->assign('tpl_module_title', tra('Webmail error'));
		$smarty->assign(
			'error',
			tra('No accounts set up (or no current account set)') . '&nbsp;' .
			'<a href="tiki-webmail.php?locSection=settings">' .
			smarty_function_icon(array('_id'=>'arrow_right'), $smarty)
		) . '</a>';
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
		$itemid = $trklib->get_item_id($module_params['trackerId'], $module_params['messageFId'], $a_mail['realmsgid']);
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

}
