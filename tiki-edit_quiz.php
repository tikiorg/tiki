<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-edit_quiz.php,v 1.25.2.2 2007-11-26 14:44:21 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/quizzes/quizlib.php');

if ($prefs['feature_quizzes'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_quizzes");

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["quizId"])) {
	$_REQUEST["quizId"] = 0;
}


$smarty->assign('quizId', $_REQUEST["quizId"]);

$smarty->assign('individual', 'n');

if ($userlib->object_has_one_permission($_REQUEST["quizId"], 'quiz')) {
	$smarty->assign('individual', 'y');

	if ($tiki_p_admin != 'y') {
		$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'quizzes');

		foreach ($perms["data"] as $perm) {
			$permName = $perm["permName"];

			if ($userlib->object_has_permission($user, $_REQUEST["quizId"], 'quiz', $permName)) {
				$$permName = 'y';

				$smarty->assign("$permName", 'y');
			} else {
				$$permName = 'n';

				$smarty->assign("$permName", 'n');
			}
		}
	}
}

if ($tiki_p_admin_quizzes != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

$_REQUEST["questionsPerPage"] = 999;

$info = array();
$info["name"] = '';
$info["description"] = '';
$info["publishDate"] = $tikilib->now;
$cur_time = explode(',', $tikilib->date_format('%Y,%m,%d,%H,%M,%S', $publishDate));
$info["expireDate"] = $tikilib->make_time($cur_time[3], $cur_time[4], $cur_time[5], $cur_time[1], $cur_time[2], $cur_time[0]+1);
$info["canRepeat"] = 'n';
$info["storeResults"] = 'n';
$info["immediateFeedback"] = 'n';
$info["showAnswers"] = 'n';
$info["shuffleQuestions"] = 'n';
$info["shuffleAnswers"] = 'n';
$info["questionsPerPage"] = 10;
$info["timeLimited"] = 'n';
$info["passingperct"] = '';
$info["timeLimit"] = 60 * 60;

if (isset($_REQUEST["save"])) {
	check_ticket('edit-quiz');

// 	print $_REQUEST["publish_Hour"]."<br>";
// 	print $_REQUEST["publish_Minute"]."<br>";
// 	print $_REQUEST["publish_Month"]."<br>";
// 	print $_REQUEST["publish_Day"]."<br>";
// 	print $_REQUEST["publish_Year"]."<br>";


 	# convert from the displayed 'site' time to 'server' time
 	$publishDate = $tikilib->make_time($_REQUEST["publish_Hour"], $_REQUEST["publish_Minute"], 0, $_REQUEST["publish_Month"], $_REQUEST["publish_Day"], $_REQUEST["publish_Year"]);
 	$expireDate = $tikilib->make_time($_REQUEST["expire_Hour"], $_REQUEST["expire_Minute"], 0, $_REQUEST["expire_Month"], $_REQUEST["expire_Day"], $_REQUEST["expire_Year"]);

//  	print $publishDate."<br>";
//  	print $expireDate."<br>";
//  	die;

	if (isset($_REQUEST["canRepeat"]) && $_REQUEST["canRepeat"] == 'on') {
		$_REQUEST["canRepeat"] = 'y';
	} else {
		$_REQUEST["canRepeat"] = 'n';
	}

	if (isset($_REQUEST["storeResults"]) && $_REQUEST["storeResults"] == 'on') {
		$_REQUEST["storeResults"] = 'y';
	} else {
		$_REQUEST["storeResults"] = 'n';
	}

	if (isset($_REQUEST["immediateFeedback"]) && $_REQUEST["immediateFeedback"] == 'on') {
		$_REQUEST["immediateFeedback"] = 'y';
	} else {
		$_REQUEST["immediateFeedback"] = 'n';
	}

	if (isset($_REQUEST["showAnswers"]) && $_REQUEST["showAnswers"] == 'on') {
		$_REQUEST["showAnswers"] = 'y';
	} else {
		$_REQUEST["showAnswers"] = 'n';
	}

	if (isset($_REQUEST["shuffleQuestions"]) && $_REQUEST["shuffleQuestions"] == 'on') {
		$_REQUEST["shuffleQuestions"] = 'y';
	} else {
		$_REQUEST["shuffleQuestions"] = 'n';
	}

	if (isset($_REQUEST["shuffleAnswers"]) && $_REQUEST["shuffleAnswers"] == 'on') {
		$_REQUEST["shuffleAnswers"] = 'y';
	} else {
		$_REQUEST["shuffleAnswers"] = 'n';
	}

	if (isset($_REQUEST["timeLimited"]) && $_REQUEST["timeLimited"] == 'on') {
		$_REQUEST["timeLimited"] = 'y';
	} else {
		$_REQUEST["timeLimited"] = 'n';
	}

	// GGG Have to change $quizlib->replace_quiz to take publish and expire dates.
	$qid = $quizlib->replace_quiz($_REQUEST["quizId"], $_REQUEST["name"],
																$_REQUEST["description"],	$_REQUEST["canRepeat"],
																$_REQUEST["storeResults"], $_REQUEST["immediateFeedback"],
																$_REQUEST["showAnswers"],	$_REQUEST["shuffleQuestions"],
																$_REQUEST["shuffleAnswers"], $_REQUEST["questionsPerPage"],
																$_REQUEST["timeLimited"], $_REQUEST["timeLimit"],
																$publishDate, $expireDate, $_REQUEST["passingperct"]);
	$cat_type = 'quiz';
	$cat_objid = $qid;
	$cat_desc = substr($_REQUEST["description"], 0, 200);
	$cat_name = $_REQUEST["name"];
	$cat_href = "tiki-take_quiz.php?quizId=" . $cat_objid;
	include_once ("categorize.php");
	$_REQUEST["quizId"] = 0;
	$smarty->assign('quizId', $_REQUEST["quizId"]);
	$quizId = 0;

} elseif ($_REQUEST["quizId"]) {
	// GGG Have to change $quizlib->get_quiz to handle publish and expire dates.  Maybe it does automagically!
	$info = $quizlib->get_quiz($_REQUEST["quizId"]);

	if (!isset($info["publishDate"])){
		$info["publishDate"] = $tikilib->now;
	}
	if (!isset($info["expireDate"])){
		$cur_time = explode(',', $tikilib->date_format('%Y,%m,%d,%H,%M,%S', $tikilib->now));
		$info["expireDate"] = $tikilib->make_time($cur_time[3], $cur_time[4], $cur_time[5], $cur_time[1], $cur_time[2], $cur_time[0]+1);
	}
}

$smarty->assign('name', $info["name"]);
$smarty->assign('description', $info["description"]);
$smarty->assign('canRepeat', $info["canRepeat"]);
$smarty->assign('storeResults', $info["storeResults"]);
$smarty->assign('immediateFeedback',$info["immediateFeedback"]);
$smarty->assign('showAnswers',$info["showAnswers"]);
$smarty->assign('shuffleQuestions',$info["shuffleQuestions"]);
$smarty->assign('shuffleAnswers',$info["shuffleAnswers"]);
$smarty->assign('questionsPerPage', $info["questionsPerPage"]);
$smarty->assign('timeLimited', $info["timeLimited"]);
$smarty->assign('timeLimit', $info["timeLimit"]);
$smarty->assign('passingperct', $info["passingperct"]);

if (isset($_REQUEST["remove"])) {
  $area = 'delquiz';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$quizlib->remove_quiz($_REQUEST["remove"]);
  } else {
    key_get($area);
  }
}

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
$channels = $quizlib->list_quizzes($offset, $maxRecords, $sort_mode, $find);

$temp_max = count($channels["data"]);
for ($i = 0; $i < $temp_max; $i++) {
	if ($userlib->object_has_one_permission($channels["data"][$i]["quizId"], 'quiz')) {
		$channels["data"][$i]["individual"] = 'y';

		if ($userlib->object_has_permission($user, $channels["data"][$i]["quizId"], 'quiz', 'tiki_p_take_quiz')) {
			$channels["data"][$i]["individual_tiki_p_take_quiz"] = 'y';
		} else {
			$channels["data"][$i]["individual_tiki_p_take_quiz"] = 'n';
		}

		if ($userlib->object_has_permission($user, $channels["data"][$i]["quizId"], 'quiz', 'tiki_p_view_quiz_stats')) {
			$channels["data"][$i]["individual_tiki_p_view_quiz_stats"] = 'y';
		} else {
			$channels["data"][$i]["individual_tiki_p_view_quiz_stats"] = 'n';
		}

		if ($userlib->object_has_permission($user, $channels["data"][$i]["quizId"], 'quiz', 'tiki_p_view_user_stats')) {
			$channels["data"][$i]["individual_tiki_p_view_user_stats"] = 'y';
		} else {
			$channels["data"][$i]["individual_tiki_p_view_user_stats"] = 'n';
		}

		if ($tiki_p_admin
			== 'y' || $userlib->object_has_permission($user, $channels["data"][$i]["quizId"], 'quiz', 'tiki_p_admin_quizzes')) {
			$channels["data"][$i]["individual_tiki_p_take_quiz"] = 'y';

			$channels["data"][$i]["individual_tiki_p_view_quiz_stats"] = 'y';
			$channels["data"][$i]["individual_tiki_p_admin_quizzes"] = 'y';
			$channels["data"][$i]["individual_tiki_p_view_user_stats"] = 'y';
		}
	} else {
		$channels["data"][$i]["individual"] = 'n';
	}
}

$cant_pages = ceil($channels["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($channels["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('channels', $channels["data"]);

// Fill array with possible number of questions per page
$qpp = array(
	1,
	2,
	3,
	4
);

for ($i = 5; $i < 50; $i += 5)
	$qpp[] = $i;

$hrs = array();

for ($i = 0; $i < 10; $i++)
	$hrs[] = $i;

$mins = array();

for ($i = 1; $i < 120; $i++)
	$mins[] = $i;

$smarty->assign('qpp', $qpp);
$smarty->assign('hrs', $hrs);
$smarty->assign('mins', $mins);

$cat_type = 'quiz';
$cat_objid = $_REQUEST["quizId"];
include_once ("categorize_list.php");
ask_ticket('edit-quiz');

$smarty->assign('publishDate', $info['publishDate']);
$smarty->assign('publishDateSite', $info['publishDate']);
$smarty->assign('expireDate', $info['expireDate']);
$smarty->assign('expireDateSite', $info['expireDate']);

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-edit_quiz.tpl');
$smarty->display("tiki.tpl");

?>
