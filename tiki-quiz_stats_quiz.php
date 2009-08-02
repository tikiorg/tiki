<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: /cvsroot/tikiwiki/tiki/tiki-quiz_stats_quiz.php,v 1.17 2007-10-12 07:55:31 nyloth Exp $
$section = 'quizzes';
require_once ('tiki-setup.php');
include_once ('lib/quizzes/quizlib.php');
if ($prefs['feature_quizzes'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled") . ": feature_quizzes");
	$smarty->display("error.tpl");
	die;
}

$tikilib->get_perm_object($_REQUEST["quizId"], 'quiz');

if ($tiki_p_view_quiz_stats != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}
if (!isset($_REQUEST["quizId"])) {
	$smarty->assign('msg', tra("No quiz indicated"));
	$smarty->display("error.tpl");
	die;
}
$smarty->assign('quizId', $_REQUEST["quizId"]);
$quiz_info = $quizlib->get_quiz($_REQUEST["quizId"]);
$smarty->assign('quiz_info', $quiz_info);
if (isset($_REQUEST["remove"]) && $tiki_p_admin_quizzes == 'y') {
	$area = 'delquizstatsquiz';
	if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		key_check($area);
		$quizlib->remove_quiz_stat($_REQUEST["remove"]);
	} else {
		key_get($area);
	}
}
if (isset($_REQUEST["clear"]) && $tiki_p_admin_quizzes == 'y') {
	$area = 'delquizstatsclear';
	if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		key_check($area);
		$quizlib->clear_quiz_stats($_REQUEST["clear"]);
	} else {
		key_get($area);
	}
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
