<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-edit_quiz_questions.php,v 1.12 2004-04-30 13:28:47 ggeller Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once('tiki-setup.php');

include_once('lib/quizzes/quizlib.php');

include_once('lib/homework/homeworklib.php');

if ($feature_quizzes != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_quizzes");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin_quizzes != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["quizId"])) {
	$smarty->assign('msg', tra("No quiz indicated"));

	$smarty->display("error.tpl");
	die;
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
  $area = 'delquizquestion';
  if (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"])) {
    key_check($area);
		$quizlib->remove_quiz_question($_REQUEST["remove"]);
  } else {
    key_get($area);
  }
}

if (isset($_REQUEST["save"])) {
	check_ticket('edit-quiz-question');
	$quizlib->replace_quiz_question($_REQUEST["questionId"], $_REQUEST["question"],
		'o', $_REQUEST["quizId"], $_REQUEST["position"]);

	$smarty->assign('question', '');
	$smarty->assign('questionId', 0);
}

if (isset($_REQUEST["import"])) {
	check_ticket('edit-quiz-question');
	$input = $_REQUEST["input_data"];

	$input_array = preg_split("/\r\n/", $input);

	for ($i = 0; $i < count($input_array); $i++)
		$input_array[$i] = trim($input_array[$i]);

	if ($input_array[count($input_array)] != "")
		array_push($input_array,"");

	$questions = TextToQuestions($input_array);

	foreach ($questions as $question){
		$question_text = $question->getQuestion();
		$id = $quizlib->replace_quiz_question(0, $question_text, 'o', $_REQUEST["quizId"], 0);
		for ($i = 0; $i < $question->getChoiceCount(); $i++){
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
$questions = $quizlib->list_all_questions(0, -1, 'position_desc', '');
$smarty->assign('questions', $questions["data"]);

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
$positions = array();

for ($i = 1; $i < 100; $i++)
	$positions[] = $i;

$smarty->assign('positions', $positions);
ask_ticket('edit-quiz-question');

// Display the template
$smarty->assign('mid', 'tiki-edit_quiz_questions.tpl');
$smarty->display("tiki.tpl");

?>
