<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'quizzes';
require_once ('tiki-setup.php');
$quizlib = TikiLib::lib('quiz');
$access->check_feature('feature_quizzes');
if (!isset($_REQUEST["quizId"])) {
	$smarty->assign('msg', tra("No quiz indicated"));
	$smarty->display("error.tpl");
	die;
}
$smarty->assign('individual', 'n');

$tikilib->get_perm_object($_REQUEST["quizId"], 'quiz');
$access->check_permission('tiki_p_view_user_results');
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
include_once ('tiki-section_options.php');
ask_ticket('quiz-res-stats');
$smarty->assign('mid', 'tiki-quiz_result_stats.tpl');
$smarty->display("tiki.tpl");
