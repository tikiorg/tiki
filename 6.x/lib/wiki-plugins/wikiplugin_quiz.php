<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id:  $

function wikiplugin_quiz_info() {
	return array(
		'name' => tra('Quiz'),
		'documentation' => 'PluginQuiz',
		'description' => tra('Display a quiz'),
		'prefs' => array( 'feature_quizzes', 'wikiplugin_quiz' ),
		'body' => tra('Title'),
		'icon' => 'pics/icons/thumb_up.png',
		'params' => array(
			'quizId' => array(
				'required' => true,
				'name' => tra('Quiz'),
				'description' => tra('Numeric value representing the quiz ID'),
				'default' => ''
			),
			'showtitle' => array(
				'required' => false,
				'name' => tra('Show Title'),
				'description' => tra('Show quiz title (shown by default).'),
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
			),
			'showdescription' => array(
				'required' => false,
				'name' => tra('Show description'),
				'description' => tra('Show quiz description (shown by default).'),
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)

			),
			'resultlink' => array(
				'required' => false,
				'name' => tra('Link to results'),
				'description' => tra('Include a link to detailed results (shown by default).'),
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)

			)

		),
	);
}

function wikiplugin_quiz($data, $params) {
	global $smarty, $quizlib, $trklib, $tikilib, $dbTiki, $userlib, $tiki_p_admin, $prefs, $_REQUEST, $user;
	$default = array('showdescription' => 'y', 'showtitle' => 'y', 'resultlink' => 'y');
	$params = array_merge($default, $params);

	extract ($params,EXTR_SKIP);

	if (!isset($quizId)) {
	    $smarty->assign('msg', tra("missing quiz ID for plugin QUIZ"));
	    return $smarty->fetch("error.tpl");
		die;
	}

$section = 'quizzes';
require_once ('tiki-setup.php');
include_once ('lib/quizzes/quizlib.php');
if ($prefs['feature_categories'] == 'y') {
	global $categlib;
	if (!is_object($categlib)) {
		include_once ('lib/categories/categlib.php');
	}
}

//$access->check_feature('feature_quizzes');


$tikilib->get_perm_object( $quizId, 'quiz' );

$smarty->assign('quizId', $quizId);
$smarty->assign('showtitle', $showtitle);
$smarty->assign('showdescription', $showdescription);
$smarty->assign('resultlink', $resultlink);

$quiz_info = $quizlib->get_quiz($quizId);

//$access->check_permission('tiki_p_take_quiz');
if ($user) {
	// If the quiz cannot be repeated
	if ($quiz_info["canRepeat"] == 'n') {
		// Check if user has taken this quiz
		if ($quizlib->user_has_taken_quiz($user, $quizId)) {
			$smarty->assign('msg', tra("You cannot take this quiz twice"));
			$smarty->display("error.tpl");
			die;
		}
	}
}

		
			if ($prefs['ajax_xajax'] == 'y') {
				if (!isset($_REQUEST['xajax'])) {	// xajaxRequestUri needs to be set to tiki-take_quiz.php in JS before calling the func
					$ajaxlib->registerFunction('processQuiz');
				}
			} else {
				if (isset($_REQUEST['quiz_send'])) {
					doProcessQuiz($_REQUEST);
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
			$smarty->assign('msg', tra("Quiz time limit exceeded quiz cannot be computed"));
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
	$smarty->assign_by_ref('userResultId', $userResultId);
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
$ur_info = $quizlib->get_user_quiz_result($userResultId);
$smarty->assign('ur_info', $ur_info);

	//print("points: $points over $max<br />");
	
} else {
	$_SESSION["startQuiz"] = $tikilib->now;
}
$quiz_info["timeLimitsec"] = $quiz_info["timeLimit"] * 60;
$smarty->assign('quiz_info', $quiz_info);
$questions = $quizlib->list_quiz_questions($quizId, 0, -1, 'position_asc', '');
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

 return $smarty->fetch("wiki-plugins/wikiplugin_quiz.tpl");


}
