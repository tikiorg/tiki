<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');
include_once('lib/quizzes/quizlib.php');

$auto_query_args = array('quizId', 'questionId', 'sort_mode', 'offset', 'find');
$access->check_feature('feature_quizzes');

if (!isset($_REQUEST["quizId"])) {
	$smarty->assign('msg', tra("No quiz indicated"));

	$smarty->display("error.tpl");
	die;
}

$tikilib->get_perm_object($_REQUEST["quizId"], 'quiz');

$access->check_permission('tiki_p_admin_quizzes');

$smarty->assign('quizId', $_REQUEST["quizId"]);

$smarty->assign('individual', 'n');

$quiz_info = $quizlib->get_quiz($_REQUEST["quizId"]);
$smarty->assign('quiz_info', $quiz_info);

if (!isset($_REQUEST["questionId"])) {
	$_REQUEST["questionId"] = 0;
}

$smarty->assign('questionId', $_REQUEST["questionId"]);

if ($_REQUEST["questionId"]) {
	$info = $quizlib->get_quiz_question($_REQUEST["questionId"]);
} else {
	$info = array();

	$info["question"] = '';
	$info["type"] = '';
	$info["position"] = '';
}

$smarty->assign('question', $info["question"]);
$smarty->assign('type', $info["type"]);
$smarty->assign('position', $info["position"]);

if (isset($_REQUEST["remove"])) {
	$access->check_authenticity();
	$quizlib->remove_quiz_question($_REQUEST["remove"]);
}

if (isset($_REQUEST["save"])) {
	check_ticket('edit-quiz-question');
	
	$quizlib->replace_quiz_question($_REQUEST["questionId"], $_REQUEST["question"],
		$_REQUEST["questionType"], $_REQUEST["quizId"], $_REQUEST["position"]);

	$smarty->assign('question', '');
	$smarty->assign('questionId', 0);
}

if (isset($_REQUEST["import"])) {
	check_ticket('edit-quiz-question');

	$questions = TextToQuestions($_REQUEST["input_data"]);

	foreach ($questions as $question){
		$question_text = $question->getQuestion();
		$id = $quizlib->replace_quiz_question(0, $question_text, 'o', $_REQUEST["quizId"], 0);
		$temp_max = $question->getChoiceCount();
		for ($i = 0; $i < $temp_max; $i++){
			$a = $question->GetChoice($i);
			$b = $question->GetCorrect($i);
			$quizlib->replace_question_option(0, $a, $b, $id);
		}
	}

	$smarty->assign('question', '');
	$smarty->assign('questionId', 0);
}

if (isset($_REQUEST["useQuestion"])) {
	check_ticket('edit-quiz-question');
	$info = $quizlib->get_quiz_question($_REQUEST["usequestionid"]);

	$qid = $quizlib->replace_quiz_question(0, $info["question"], $info["type"], $_REQUEST["quizId"], $_REQUEST["position"]);
	$options = $quizlib->list_quiz_question_options($info["questionId"], 0, -1, 'points_desc', '');

	foreach ($options["data"] as $opt) {
		$quizlib->replace_question_option(0, $opt["optionText"], $opt["points"], $qid);
	}
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'position_asc';
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
$channels = $quizlib->list_quiz_questions($_REQUEST["quizId"], $offset, $maxRecords, $sort_mode, $find);
// GGG turned this off as we now have too many questions in the db for this to work.
// $questions = $quizlib->list_all_questions(0, -1, 'position_desc', '');
// $smarty->assign('questions', $questions["data"]);

$smarty->assign_by_ref('cant_pages', $channels["cant"]);

$smarty->assign_by_ref('channels', $channels["data"]);

// Fill array with possible number of questions per page
$positions = array();

for ($i = 1; $i < 100; $i++)
	$positions[] = $i;

$smarty->assign('positions', $positions);

$questionTypes = array();
$questionTypes["o"] = "Optional";
$questionTypes["f"] = "Optional + File";

$smarty->assign('questionTypes', $questionTypes);

ask_ticket('edit-quiz-question');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-edit_quiz_questions.tpl');
$smarty->display("tiki.tpl");
