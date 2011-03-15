<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'quizzes';
require_once ('tiki-setup.php');
include_once ('lib/quizzes/quizlib.php');

$access->check_feature('feature_quizzes');

$tikilib->get_perm_object($_REQUEST["quizId"], 'quiz');

$access->check_permission('tiki_p_view_quiz_stats');

if (!isset($_REQUEST["quizId"])) {
	$smarty->assign('msg', tra("No quiz indicated"));
	$smarty->display("error.tpl");
	die;
}
$smarty->assign('quizId', $_REQUEST["quizId"]);
$quiz_info = $quizlib->get_quiz($_REQUEST["quizId"]);
$smarty->assign('quiz_info', $quiz_info);
if (isset($_REQUEST["remove"]) && $tiki_p_admin_quizzes == 'y') {
	$access->check_authenticity();
	$quizlib->remove_quiz_stat($_REQUEST["remove"]);
}
if (isset($_REQUEST["clear"]) && $tiki_p_admin_quizzes == 'y') {
	$access->check_authenticity();
	$quizlib->clear_quiz_stats($_REQUEST["clear"]);
}
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'timestamp_desc';
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
$channels = $quizlib->list_quiz_stats($_REQUEST["quizId"], $offset, $maxRecords, $sort_mode, $find);
$smarty->assign_by_ref('cant_pages', $channels["cant"]);
$smarty->assign_by_ref('channels', $channels["data"]);
//Get all the statistics for this quiz
$questions = $quizlib->list_quiz_question_stats($_REQUEST["quizId"], 0, -1, 'position_asc', '');
$smarty->assign_by_ref('questions', $questions);
include_once ('tiki-section_options.php');
ask_ticket('quiz_stats_quiz');
// Display the template
$smarty->assign('mid', 'tiki-quiz_stats_quiz.tpl');
$smarty->display("tiki.tpl");
