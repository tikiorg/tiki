<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-quiz_result_stats.php,v 1.15 2007-10-12 07:55:31 nyloth Exp $

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
	$smarty->assign('msg', tra("No quiz indicated"));

	$smarty->display("error.tpl");
	die;
}

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

if ($tiki_p_view_user_results != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

$smarty->assign('quizId', $_REQUEST["quizId"]);
$quiz_info = $quizlib->get_quiz($_REQUEST["quizId"]);
$smarty->assign('quiz_info', $quiz_info);

if (!isset($_REQUEST["resultId"])) {
	$smarty->assign('msg', tra("No result indicated"));

	$smarty->display("error.tpl");
	die;
}

$smarty->assign('resultId', $_REQUEST["resultId"]);

if (!isset($_REQUEST["userResultId"])) {
	$smarty->assign('msg', tra("No result indicated"));

	$smarty->display("error.tpl");
	die;
}

$smarty->assign('userResultId', $_REQUEST["userResultId"]);
$ur_info = $quizlib->get_user_quiz_result($_REQUEST["userResultId"]);
$smarty->assign('ur_info', $ur_info);

$result = $quizlib->get_quiz_result($_REQUEST["resultId"]);
$smarty->assign_by_ref('result', $result);

$questions = $quizlib->get_user_quiz_questions($_REQUEST["userResultId"]);
$smarty->assign('questions', $questions);

$section = 'quizzes';
include_once ('tiki-section_options.php');

ask_ticket('quiz-res-stats');

$smarty->assign('mid', 'tiki-quiz_result_stats.tpl');
$smarty->display("tiki.tpl");

?>
