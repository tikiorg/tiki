<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-quiz_edit.php,v 1.3 2004-05-11 20:13:35 ggeller Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, 
//                          George G. Geller et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// 	include_once ("categorize.php"); put this somewhere for the save.


// Initialization
require_once('tiki-setup.php');

include_once('lib/quizzes/quizlib.php');

if ($feature_quizzes != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_quizzes");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin_quizzes != 'y') {
	$smarty->assign('msg', tra("You don't have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["quizId"])) {
	$smarty->assign('msg', tra("No quiz indicated"));

	$smarty->display("error.tpl");
	die;
}

$smarty->assign('quizId', $_REQUEST["quizId"]);

$cat_type = 'quiz';
$cat_objid = $_REQUEST["quizId"];


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
$quiz_info["name"] = "Chapter 01";
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
	echo "Sorry, this is only a prototype at present.<br>";
	die;
	echo "line: ".__LINE__."<br>";
	echo '$_REQUEST["quizId"] = '.$_REQUEST["quizId"]."<br>";
	foreach ($_REQUEST as $request){
		echo '$request = '.$request."<br>";
	}
	foreach ($_REQUEST as $key => $request){
		echo $key." = ".$request."<br>";
	}

	check_ticket('edit-quiz-question');
	$quizlib->replace_quiz_question($_REQUEST["questionId"], $_REQUEST["question"],
		'o', $_REQUEST["quizId"], $_REQUEST["position"]);

	$smarty->assign('question', '');
	$smarty->assign('questionId', 0);
}

if (isset($_REQUEST["import"])) {
	check_ticket('edit-quiz-question');

	$questions = TextToQuestions($_REQUEST["input_data"]);

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

include_once ("categorize_list.php");

$smarty->assign('positions', $positions);
ask_ticket('edit-quiz-question');

// GGG scaffolding
// $smarty->assign('repetitionLimit', "10");
$mins = array();

for ($i = 1; $i <= 20; $i++)
	$mins[] = $i;
$smarty->assign('mins', $mins);

$mins = array();
$repetitions = array();
$qpp = array();

for ($i = 1; $i <= 20; $i++){
	$mins[] = $i;
}
$smarty->assign('mins', $mins);

for ($i = 1; $i <= 10; $i++){
	$qpp[] = $i;
	$repetitions[] = $i;
}
$repetitions[] = "Unlimited";
$qpp[] = "Unlimited";
$smarty->assign('repetitions', $repetitions);
$smarty->assign('qpp', $qpp);

$smarty->assign('questionsPerPage', "Unlimited");

$quiz_info["name"] = "Chapter 01";

// Display the template
$smarty->assign('mid', 'tiki-quiz_edit.tpl');
$smarty->display("tiki.tpl");

?>
