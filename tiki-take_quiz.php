<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'quizzes';
require_once ('tiki-setup.php');
$quizlib = TikiLib::lib('quiz');
if ($prefs['feature_categories'] == 'y') {
	$categlib = TikiLib::lib('categ');
}

$access->check_feature('feature_quizzes');

if (!isset($_REQUEST["quizId"])) {
	$smarty->assign('msg', tra("No quiz indicated"));
	$smarty->display("error.tpl");
	die;
}
$tikilib->get_perm_object($_REQUEST['quizId'], 'quiz');

$smarty->assign('quizId', $_REQUEST["quizId"]);
$quiz_info = $quizlib->get_quiz($_REQUEST["quizId"]);

$access->check_permission('tiki_p_take_quiz');
if ($user) {
	// If the quiz cannot be repeated
	if ($quiz_info["canRepeat"] == 'n') {
		// Check if user has taken this quiz
		if ($quizlib->user_has_taken_quiz($user, $_REQUEST["quizId"])) {
			$smarty->assign('msg', tra("You cannot take this quiz twice"));
			$smarty->display("error.tpl");
			die;
		}
	}
}
$smarty->assign('ans', 'n');
if (isset($_REQUEST["timeleft"])) {
	$smarty->assign('ans', 'y');
	$_SESSION["finishQuiz"] = $tikilib->now;
	$elapsed = $_SESSION["finishQuiz"] - $_SESSION["startQuiz"];
	if ($user) {
		// If the quiz cannot be repeated
		if ($quiz_info["canRepeat"] == 'n') {
			// Check if user has taken this quiz
			if ($quizlib->user_has_taken_quiz($user, $_REQUEST["quizId"])) {
				$smarty->assign('msg', tra("You cannot take this quiz twice"));
				$smarty->display("error.tpl");
				die;
			} else {
				$quizlib->user_takes_quiz($user, $_REQUEST["quizId"]);
			}
		}
	}
	// Now get the quiz information
	// Verify time limit if appropiate
	if ($quiz_info["timeLimited"] == 'y') {
		if ($elapsed > $quiz_info["timeLimit"] * 60) {
			$smarty->assign('msg', tra("The quiz time limit was exceeded. The quiz score cannot be computed"));
			$smarty->display("error.tpl");
			die;
		}
	}
	// Now for each quiz question verify the points the user did get
	$questions = $quizlib->list_quiz_questions($_REQUEST["quizId"], 0, -1, 'position_asc', '');
	$points = 0;
	$max = 0;
	$temp_max = count($questions["data"]);
	for ($i = 0; $i < $temp_max; $i++) {
		$options = $quizlib->list_quiz_question_options($questions["data"][$i]["questionId"], 0, -1, 'optionText_asc', '');
		$qid = $questions["data"][$i]["questionId"];
		$max+= $questions["data"][$i]["maxPoints"];
		if (isset($_REQUEST["question_$qid"])) {
			check_ticket('take-quiz');
			$opt = $quizlib->get_quiz_question_option($_REQUEST["question_$qid"]);
			$points+= $opt["points"];
			// Register the answer for quiz stats
			$quizlib->register_quiz_answer($_REQUEST["quizId"], $qid, $_REQUEST["question_$qid"]);
		}
	}
	$result = $quizlib->calculate_quiz_result($_REQUEST["quizId"], $points);
	// register the result for quiz stats
	$userResultId = $quizlib->register_quiz_stats($_REQUEST["quizId"], $user, $elapsed, $points, $max, $result["resultId"]);
	$smarty->assign_by_ref('result', $result);
	if ($quiz_info["storeResults"] == 'y') {
		$temp_max = count($questions["data"]);
		for ($i = 0; $i < $temp_max; $i++) {
			$options = $quizlib->list_quiz_question_options($questions["data"][$i]["questionId"], 0, -1, 'optionText_asc', '');
			$qid = $questions["data"][$i]["questionId"];
			if (isset($_REQUEST["question_$qid"])) {
				check_ticket('take-quiz');
				$quizlib->register_user_quiz_answer($userResultId, $_REQUEST["quizId"], $qid, $_REQUEST["question_$qid"]);
			}
			// TAKE CARE OF FILE UPLOADS FOR QUESTIONS
			if (isset($_FILES["question_upload_$qid"]) && ($tmp_file = $_FILES["question_upload_$qid"]['tmp_name'])) {
				$filename = $_FILES["question_upload_$qid"]['name'];
				$filetype = $_FILES["question_upload_$qid"]['type'];
				$filesize = $_FILES["question_upload_$qid"]['size'];
				$binFile = $_FILES["question_upload_$qid"]['tmp_name'];
				$data = addslashes(fread(fopen($binFile, "r"), filesize($binFile)));
				$quizlib->register_user_quiz_answer_upload($userResultId, $qid, $filename, $filetype, $filesize, $tmp_file);
			}
		}
	}
	//print("points: $points over $max<br />");
	
} else {
	$_SESSION["startQuiz"] = $tikilib->now;
}
$quiz_info["timeLimitsec"] = $quiz_info["timeLimit"] * 60;
$smarty->assign('quiz_info', $quiz_info);
$questions = $quizlib->list_quiz_questions($_REQUEST["quizId"], 0, -1, 'position_asc', '');
$temp_max = count($questions["data"]);
for ($i = 0; $i < $temp_max; $i++) {
	$options = $quizlib->list_quiz_question_options($questions["data"][$i]["questionId"], 0, -1, 'optionText_asc', '');
	$questions["data"][$i]["options"] = $options["data"];
}
$smarty->assign_by_ref('questions', $questions["data"]);
include_once ('tiki-section_options.php');
if ($prefs['feature_theme_control'] == 'y') {
	$cat_type = 'quiz';
	$cat_objid = $_REQUEST["quizId"];
	include ('tiki-tc.php');
}
ask_ticket('take-quiz');
// Display the template
$smarty->assign('mid', 'tiki-take_quiz.tpl');
$smarty->display("tiki.tpl");
