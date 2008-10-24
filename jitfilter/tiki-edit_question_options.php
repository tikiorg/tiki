<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-edit_question_options.php,v 1.17 2007-10-12 07:55:26 nyloth Exp $

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

if (!isset($_REQUEST["questionId"])) {
	$smarty->assign('msg', tra("No question indicated"));

	$smarty->display("error.tpl");
	die;
}

$smarty->assign('questionId', $_REQUEST["questionId"]);
$quiz_info = $quizlib->get_quiz_question($_REQUEST["questionId"]);
$smarty->assign('question_info', $quiz_info);
$_REQUEST["quizId"] = $quiz_info["quizId"];
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
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["optionId"])) {
	$_REQUEST["optionId"] = 0;
}

$smarty->assign('optionId', $_REQUEST["optionId"]);

if ($_REQUEST["optionId"]) {
	$info = $quizlib->get_quiz_question_option($_REQUEST["optionId"]);
} else {
	$info = array();

	$info["optionText"] = '';
	$info["points"] = '';
}

$smarty->assign('optionText', $info["optionText"]);
$smarty->assign('points', $info["points"]);

if (isset($_REQUEST["remove"])) {
  $area = 'delquizquestionoptions';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$quizlib->remove_quiz_question_option($_REQUEST["remove"]);
  } else {
    key_get($area);
  }
}

if (isset($_REQUEST["save"])) {
	check_ticket('edit-question-options');
	$quizlib->replace_question_option($_REQUEST["optionId"], $_REQUEST["optionText"], $_REQUEST["points"], $_REQUEST["questionId"]);

	$smarty->assign('optionText', '');
	$smarty->assign('optionId', 0);
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'optionText_asc';
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
$channels = $quizlib->list_quiz_question_options($_REQUEST["questionId"], $offset, $maxRecords, $sort_mode, $find);

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

ask_ticket('edit-question-options');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-edit_question_options.tpl');
$smarty->display("tiki.tpl");

?>
