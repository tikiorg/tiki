<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-poll_results.php,v 1.21.2.4 2007-12-06 14:14:11 nkoth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'poll';
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

// Now check permissions to access this page
if ($tiki_p_view_poll_results != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg',tra("Permission denied you cannot view this page"));
	$smarty->display("error.tpl");
	die;  
}

if (!isset($_REQUEST["maxRecords"])) {
	$_REQUEST["maxRecords"] = 30;
	$smarty->assign('maxRecords', $_REQUEST['maxRecords']);	
} elseif ($_REQUEST["maxRecords"] == '') {
	$_REQUEST["maxRecords"] = -1;
	$smarty->assign('maxRecords', '');
}
if (!isset($_REQUEST['find'])) {
	$_REQUEST['find'] = '';
}
$smarty->assign_by_ref('find', $_REQUEST['find']);
	
$polls = $polllib->list_active_polls(0, $_REQUEST["maxRecords"], "votes_desc", $_REQUEST['find']);
$pollIds = array();
if (isset($_REQUEST["pollId"])) {
	$pollIds[] = $_REQUEST["pollId"];	
} else {
	foreach ($polls["data"] as $pId) {
		$pollIds[] = $pId["pollId"];	
	}
}

$poll_info_arr = array();
foreach ($pollIds as $pK => $pId) {
// iterate each poll
$poll_info = $polllib->get_poll($pId);
$poll_info_arr[$pK] = $poll_info;
$options = $polllib->list_poll_options($pId);
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
$poll_info_arr[$pK]["options"] = $options;
$poll_info_arr[$pK]["total"] = $total;
} // end iterate each poll

function scoresort($a, $b) {
	if (isset($_REQUEST["scoresort_asc"])) {
		$i = $_REQUEST["scoresort_asc"];
	} else {
		$i = $_REQUEST["scoresort_desc"]; 
	}
	// must first sort based on missing, otherwise missing index will occur when trying to read more info. 
	if (count($a["options"]) <= $i && count($b["options"]) <= $i ) {
		return 0;
	} elseif (count($a["options"]) <= $i ) {
		return -1;
	} elseif (count($b["options"]) <= $i ) {
		return 1;
	}
	if ($a["options"][$i]["title"] == $poll_info_arr["options"][$i]["title"] && $b["options"][$i]["title"] != $poll_info_arr["options"][$i]["title"] ) {
    	return 1;  
    }
	if ($a["options"][$i]["title"] != $poll_info_arr["options"][$i]["title"] && $b["options"][$i]["title"] == $poll_info_arr["options"][$i]["title"] ) {
    	return -1;  
    }
    if ($a["options"][$i]["width"] == $b["options"][$i]["width"]) {
    	return 0;  
    }
	if (isset($_REQUEST["scoresort_asc"])) {
		return ($a["options"][$i]["width"] < $b["options"][$i]["width"]) ? -1 : 1;
	} else {
		return ($a["options"][$i]["width"] > $b["options"][$i]["width"]) ? -1 : 1; 
	}    
}
if (isset($_REQUEST["scoresort_desc"])) {
	$smarty->assign('scoresort_desc', $_REQUEST["scoresort_desc"]);
} elseif (isset($_REQUEST["scoresort_asc"])) {
	$smarty->assign('scoresort_asc', $_REQUEST["scoresort_asc"]);
}
if (isset($_REQUEST["scoresort_asc"]) || isset($_REQUEST["scoresort_desc"])) {	
	$t_arr = $poll_info_arr;
	$sort_ok = usort($t_arr, "scoresort");
	if ($sort_ok)  $poll_info_arr = $t_arr;
}

if ($tiki_p_admin_polls == 'y' && !empty($_REQUEST['list']) && isset($_REQUEST['pollId'])) {	
	if (empty($_REQUEST['sort_mode'])) {
		$_REQUEST['sort_mode'] = 'user_asc';
	}
	$smarty->assign_by_ref('sort_mode', $_REQUEST['sort_mode']);
	if (!isset($_REQUEST['offset'])) {
		$_REQUEST['offset'] = 0;
	}
	$smarty->assign_by_ref('offset', $_REQUEST['offset']);
	
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
if ($prefs['feature_poll_comments'] == 'y' && isset($_REQUEST['pollId'])) {
	$comments_per_page = $prefs['poll_comments_per_page'];

	$thread_sort_mode = $prefs['poll_comments_default_ordering'];
	$comments_vars = array('pollId');
	$comments_prefix_var = 'poll:';
	$comments_object_var = 'pollId';
	include_once ("comments.php");
}

$smarty->assign_by_ref('poll_info_arr', $poll_info_arr);

// the following 4 lines preserved to preserve environment for old templates
$smarty->assign_by_ref('poll_info', $poll_info);
$smarty->assign('title', $poll_info['title']);
$smarty->assign_by_ref('polls', $polls["data"]);
$smarty->assign_by_ref('options', $options);

ask_ticket('poll-results');

// Display the template
$smarty->assign('mid', 'tiki-poll_results.tpl');
$smarty->display("tiki.tpl");

?>
