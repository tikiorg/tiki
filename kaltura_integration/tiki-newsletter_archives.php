<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-newsletter_archives.php,v 1.8 2007-10-12 07:55:29 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/newsletters/nllib.php');

if ($prefs['feature_newsletters'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_newsletters");

	$smarty->display("error.tpl");
	die;
}

if (!empty($_REQUEST['nlId'])) {
	$smarty->assign('nlId', $_REQUEST["nlId"]);
	$nl_info = $nllib->get_newsletter($_REQUEST["nlId"]);
	$smarty->assign_by_ref('nl_info', $nl_info);
}

if (isset($_REQUEST['remove']) && !empty($_REQUEST['nlId'])) {
	if (!$tikilib->user_has_perm_on_object($user, $_REQUEST['nlId'], 'newsletter', 'tiki_p_admin_newsletters')) {
		$smarty->assign('msg', tra("You do not have permission to use this feature"));
		$smarty->display("error.tpl");
		die;
	}
	$area = 'delnewsletter';
	if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		key_check($area);
		$nllib->remove_edition($_REQUEST["nlId"], $_REQUEST["remove"]);
	} else {
		key_get($area);
	}
}
if (!empty($_REQUEST['error'])) {
	$edition_errors = $nllib->get_edition_errors($_REQUEST['error']);
	$edition_info = $nllib->get_edition($_REQUEST['error']);
	$smarty->assign_by_ref('edition_errors', $edition_errors);
	$smarty->assign_by_ref('edition_info', $edition_info);
}
if (!empty($_REQUEST['deleteError'])) {
	$edition_errors = $nllib->remove_edition_errors($_REQUEST['deleteError']);
}

if (!isset($_REQUEST["ed_sort_mode"])) {
	$ed_sort_mode = 'sent_desc';
} else {
	$ed_sort_mode = $_REQUEST["ed_sort_mode"];
}

if (!isset($_REQUEST["ed_offset"])) {
	$ed_offset = 0;
} else {
	$ed_offset = $_REQUEST["ed_offset"];
}

$smarty->assign_by_ref('ed_offset', $ed_offset);

if (isset($_REQUEST["ed_find"])) {
	$ed_find = $_REQUEST["ed_find"];
} else {
	$ed_find = '';
}

$smarty->assign('ed_find', $ed_find);

$smarty->assign_by_ref('ed_sort_mode', $ed_sort_mode);
if (isset($_REQUEST["nlId"])) {
	$channels = $nllib->list_editions($_REQUEST["nlId"], $ed_offset, $maxRecords, $ed_sort_mode, $ed_find, false, 'tiki_p_subscribe_newsletters');
} else {
	$channels = $nllib->list_editions(0, $ed_offset, $maxRecords, $ed_sort_mode, $ed_find, false, 'tiki_p_subscribe_newsletters');
}
$cant_pages = ceil($channels["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($ed_offset / $maxRecords));

if ($channels["cant"] > ($ed_offset + $maxRecords)) {
	$smarty->assign('next_offset', $ed_offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($ed_offset > 0) {
	$smarty->assign('prev_offset', $ed_offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}
$smarty->assign_by_ref('channels', $channels["data"]);
$smarty->assign('url', "tiki-newsletter_archives.php");

if (isset($_REQUEST['editionId'])) {
	foreach ($channels['data'] as $edition) {
		if ($edition['editionId'] == $_REQUEST['editionId']) {
			$edition["dataparsed"] = $tikilib->parse_data($edition["data"]);
			$smarty->assign_by_ref('edition', $edition);
			break;
		}
	}	
}

ask_ticket('newsletters');

$section='newsletters';
include_once('tiki-section_options.php');

// Display the template
$smarty->assign('mid', 'tiki-newsletter_archives.tpl');
$smarty->display("tiki.tpl");

?>
