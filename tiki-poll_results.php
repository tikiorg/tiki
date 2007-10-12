<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-poll_results.php,v 1.21 2007-10-12 07:55:29 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/polls/polllib.php');

if (!isset($polllib)) {
	$polllib = new PollLib($dbTiki);
}

if ($prefs['feature_polls'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_polls");

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["pollId"])) {
	$smarty->assign('msg', tra("No poll indicated"));

	$smarty->display("error.tpl");
	die;
}

$poll_info = $polllib->get_poll($_REQUEST["pollId"]);
$polls = $polllib->list_active_polls(0, -1, 'publishDate_desc', '');
$options = $polllib->list_poll_options($_REQUEST["pollId"]);

$temp_max = count($options);
$total = 0;
$isNum = true; // try to find if it is a numeric poll with a title like +1, -2, 1 point...
for ($i = 0; $i < $temp_max; $i++) {
	if ($poll_info["votes"] == 0) {
		$percent = 0;
	} else {
		$percent = number_format($options[$i]["votes"] * 100 / $poll_info["votes"], 2);

		$options[$i]["percent"] = $percent;
		if ($isNum) {
			if (preg_match('/^([+-]?[0-9]+).*/', $options[$i]['title'], $matches)) {
				$total += $options[$i]['votes'] * $matches[1];
			} else {
				$isNum = false; // it is not a nunmeric poll
			}
		}
	}

	$width = $percent * 200 / 100;
	$options[$i]["width"] = $percent;
}
if ($isNum) {
	$smarty->assign('total', $total);
}
if ($tiki_p_admin_polls == 'y' && !empty($_REQUEST['list'])) {
	if (empty($_REQUEST['sort_mode'])) {
		$_REQUEST['sort_mode'] = 'user_asc';
	}
	$smarty->assign_by_ref('sort_mode', $_REQUEST['sort_mode']);
	if (!isset($_REQUEST['offset'])) {
		$_REQUEST['offset'] = 0;
	}
	$smarty->assign_by_ref('offset', $_REQUEST['offset']);
	if (!isset($_REQUEST['find'])) {
		$_REQUEST['find'] = '';
	}
	$smarty->assign_by_ref('find', $_REQUEST['find']);

	$list_votes = $tikilib->list_votes('poll'.$_REQUEST['pollId'], $_REQUEST['offset'], $maxRecords, $_REQUEST['sort_mode'], $_REQUEST['find'], 'tiki_poll_options', 'title');
	$smarty->assign_by_ref('list_votes', $list_votes['data']);

	$cant_pages = ceil($list_votes['cant'] / $maxRecords);
	$smarty->assign_by_ref('cant_pages', $cant_pages);
	$smarty->assign('actual_page', 1 + ($_REQUEST['offset'] / $maxRecords));
	if ($list_votes['cant'] > ($_REQUEST['offset'] + $maxRecords)) {
		$smarty->assign('next_offset', $_REQUEST['offset'] + $maxRecords);
	} else {
		$smarty->assign('next_offset', -1);
	}
	if ($_REQUEST['offset'] > 0) {
		$smarty->assign('prev_offset', $_REQUEST['offset'] - $maxRecords);
	} else {
		$smarty->assign('prev_offset', -1);
	}
}

// Poll comments
if ($prefs['feature_poll_comments'] == 'y') {
	$comments_per_page = $prefs['poll_comments_per_page'];

	$thread_sort_mode = $prefs['poll_comments_default_ordering'];
	$comments_vars = array('pollId');
	$comments_prefix_var = 'poll:';
	$comments_object_var = 'pollId';
	include_once ("comments.php");
}

$smarty->assign_by_ref('poll_info', $poll_info);
$smarty->assign('title', $poll_info['title']);
$smarty->assign_by_ref('polls', $polls["data"]);
$smarty->assign_by_ref('options', $options);

ask_ticket('poll-results');

// Display the template
$smarty->assign('mid', 'tiki-poll_results.tpl');
$smarty->display("tiki.tpl");

?>
