<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/ban/banlib.php');
$access->check_feature('feature_banning');
$access->check_permission('tiki_p_admin_banning');

$auto_query_args = array( 'banId' );

if (isset($_REQUEST['remove'])) {
	$access->check_authenticity();
	$banlib->remove_rule($_REQUEST['remove']);
	unset($_REQUEST['banId']);
}
if (isset($_REQUEST['del']) && isset($_REQUEST['delsec'])) {
	check_ticket('admin-banning');
	foreach (array_keys($_REQUEST['delsec']) as $sec) {
		$banlib->remove_rule($sec);
	}
	unset($_REQUEST['banId']);
}

if (isset($_REQUEST["import"]) && isset($_FILES["fileCSV"])) {
	check_ticket('admin-banning');

	// import banning rules //
	$number_imported = $banlib->importCSV($_FILES["fileCSV"]["tmp_name"], isset($_REQUEST['import_as_new']));
	if ($number_imported > 0) {
		$smarty->assign('updated', "y");
		$smarty->assign('number_imported', $number_imported);
	}
	unset($_REQUEST['banId']);
}

if (isset($_REQUEST['save'])) {
	check_ticket('admin-banning');
	if ($_REQUEST['mode'] === 'user' && empty($_REQUEST['userreg'])) {
		TikiLib::lib('errorreport')->report(tra("Not saved:") . ' ' . tra("Username pattern empty"));
	} else if ($_REQUEST['mode'] === 'ip' && $_REQUEST['ip1'] == 255 && $_REQUEST['ip2'] == 255 && $_REQUEST['ip3'] == 255 && $_REQUEST['ip4'] == 255) {
		TikiLib::lib('errorreport')->report(tra("Not saved:") . ' ' . tra("Default IP pattern still set"));
	} else {

		$_REQUEST['use_dates'] = isset($_REQUEST['use_dates']) ? 'y' : 'n';
		$_REQUEST['date_from'] = $tikilib->make_time(0, 0, 0, $_REQUEST['date_fromMonth'], $_REQUEST['date_fromDay'], $_REQUEST['date_fromYear']);
		$_REQUEST['date_to'] = $tikilib->make_time(0, 0, 0, $_REQUEST['date_toMonth'], $_REQUEST['date_toDay'], $_REQUEST['date_toYear']);
		$sections = isset($_REQUEST['section']) ? array_keys($_REQUEST['section']) : array();
		// Handle case when many IPs are banned
		if ($_REQUEST['mode'] == 'mass_ban_ip') {
			foreach ($_REQUEST['multi_banned_ip'] as $ip => $value) {
				list($ip1,$ip2,$ip3,$ip4) = explode('.', $ip);
				$banlib->replace_rule($_REQUEST['banId'], 'ip', $_REQUEST['title'], $ip1, $ip2, $ip3, $ip4, $_REQUEST['userreg'], $_REQUEST['date_from'], $_REQUEST['date_to'], $_REQUEST['use_dates'], $_REQUEST['message'], $sections);
			}
		} else {
			$banlib->replace_rule($_REQUEST['banId'], $_REQUEST['mode'], $_REQUEST['title'], $_REQUEST['ip1'], $_REQUEST['ip2'], $_REQUEST['ip3'], $_REQUEST['ip4'], $_REQUEST['userreg'], $_REQUEST['date_from'], $_REQUEST['date_to'], $_REQUEST['use_dates'], $_REQUEST['message'], $sections);
		}
		$info['sections'] = array();
		$info['title'] = '';
		$info['mode'] = 'user';
		$info['ip1'] = 255;
		$info['ip2'] = 255;
		$info['ip3'] = 255;
		$info['ip4'] = 255;
		$info['use_dates'] = 'n';
		$info['date_from'] = $tikilib->now;
		$info['date_to'] = $tikilib->now + 7 * 24 * 3600;
		$info['message'] = '';
		$smarty->assign_by_ref('info', $info);
		unset($_REQUEST['banId']);
	}
}

if ( !empty($_REQUEST['export']) ) {
	$maxRecords = -1;
} elseif (isset($_REQUEST['max'])) {
	$maxRecords = $_REQUEST['max'];
} else {
	$maxRecords = $prefs['maxRecords'];
}

if (!empty($_REQUEST['banId'])) {
	$info = $banlib->get_rule($_REQUEST['banId']);
} else {
	$_REQUEST['banId'] = 0;
	$info['sections'] = array();
	$info['title'] = '';
	$info['mode'] = 'user';
	$info['user'] = '';
	$info['ip1'] = 255;
	$info['ip2'] = 255;
	$info['ip3'] = 255;
	$info['ip4'] = 255;
	$info['use_dates'] = 'n';
	$info['date_from'] = $tikilib->now;
	$info['date_to'] = $tikilib->now + 7 * 24 * 3600 * 100;
	$info['message'] = '';
}

// Handle case when coming from tiki-list_comments with a list of IPs to ban
if (!empty($_REQUEST['mass_ban_ip'])) {
	check_ticket('admin-banning');
	$commentslib = TikiLib::lib('comments');
	$smarty->assign('mass_ban_ip', $_REQUEST['mass_ban_ip']);
	$info['mode'] = 'mass_ban_ip';
	$info['title'] = tr('Multiple IP Banning');
	$info['message'] = tr('Access from your localization was forbidden due to excessive spamming.');
	$info['date_to'] = $tikilib->now + 365 * 24 * 3600;
	$banId_list = explode('|', $_REQUEST['mass_ban_ip']);
	// Handle case when coming from tiki-list_comments with a list of IPs to ban and also delete the related comments
	if ( !empty($_REQUEST['mass_remove']) ) {
		$access->check_authenticity(tra('Delete comments then set banning rules'));
	}
	foreach ($banId_list as $id) {
		$ban_comment=$commentslib->get_comment($id);
		$ban_comments_list[$ban_comment['user_ip']][$id]['userName'] = $ban_comment['userName'];
		$ban_comments_list[$ban_comment['user_ip']][$id]['title'] = $ban_comment['title'];
		if ( !empty($_REQUEST['mass_remove']) ) {
			$commentslib->remove_comment($id);
		}
	}
	$smarty->assign_by_ref('ban_comments_list', $ban_comments_list);
}

// Handle case when coming from tiki-admin_actionlog with a list of IPs to ban
if (!empty($_REQUEST['mass_ban_ip_actionlog'])) {
	check_ticket('admin-banning');
	$logslib = TikiLib::lib('logs');
	$smarty->assign('mass_ban_ip', $_REQUEST['mass_ban_ip_actionlog']);
	$info['mode'] = 'mass_ban_ip';
	$info['title'] = tr('Multiple IP Banning');
	$info['message'] = tr('Access from your localization was forbidden due to excessive spamming.');
	$info['date_to'] = $tikilib->now + 365 * 24 * 3600;
	$banId_list = explode('|', $_REQUEST['mass_ban_ip_actionlog']);
	foreach ($banId_list as $id) {
		$ban_actions=$logslib->get_info_action($id);
		$ban_comments_list[$ban_actions['ip']][$id]['userName'] = $ban_actions['user'];
	}
	$smarty->assign_by_ref('ban_comments_list', $ban_comments_list);
}

// Handle case when coming from tiki-adminusers with a list of IPs to ban
if (!empty($_REQUEST['mass_ban_ip_users'])) {
	check_ticket('admin-banning');
	$logslib = TikiLib::lib('logs');
	$smarty->assign('mass_ban_ip', $_REQUEST['mass_ban_ip_users']);
	$info['mode'] = 'mass_ban_ip';
	$info['title'] = tr('Multiple IP Banning');
	$info['message'] = tr('Access from your localization was forbidden due to excessive spamming.');
	$info['date_to'] = $tikilib->now + 365 * 24 * 3600;
	$banUsers_list = explode('|', $_REQUEST['mass_ban_ip_users']);
	foreach ($banUsers_list as $banUser) {
		$ban_actions=$logslib->get_user_registration_action($banUser);
		$ban_comments_list[$ban_actions['ip']][$banUser]['userName'] = $banUser;
	}
	$smarty->assign_by_ref('ban_comments_list', $ban_comments_list);
}

$smarty->assign('banId', $_REQUEST['banId']);
$smarty->assign_by_ref('info', $info);

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'created_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
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
$smarty->assign_by_ref('sort_mode', $sort_mode);
$items = $banlib->list_rules($offset, $maxRecords, $sort_mode, $find);

if (isset($_REQUEST['export']) || isset($_REQUEST['csv'])) {
	// export banning rules //
	$csv = $banlib->export_rules($items['data']);

	header("Content-type: text/comma-separated-values; charset:UTF-8");
	header('Content-Disposition: attachment; filename="tiki-admin_banning.csv"');
	if (function_exists('mb_strlen')) {
		header('Content-Length: ' . mb_strlen($csv, '8bit'));
	} else {
		header('Content-Length: ' . strlen($csv));
	}
	echo $csv;
	die();
}

$smarty->assign('cant', $items['cant']);
$smarty->assign_by_ref('cant_pages', $items["cant"]);
$smarty->assign_by_ref('items', $items["data"]);
$smarty->assign('sections', $sections_enabled);
ask_ticket('admin-banning');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('mid', 'tiki-admin_banning.tpl');
$smarty->display("tiki.tpl");
