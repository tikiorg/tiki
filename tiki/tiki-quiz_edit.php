<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-quiz_edit.php,v 1.4 2004-05-13 19:01:10 ggeller Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, 
//                          George G. Geller et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

error_reporting(E_ALL);

// Initialization
require_once('tiki-setup.php');

include_once('lib/quizzes/quizlib.php');

if ($feature_quizzes != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_quizzes");

	$smarty->display("error.tpl");
	die;
}

// When the quiz id is not indicated, we redirect to the list of quizzes.
if (!isset($_REQUEST["quizId"])) {
	header ("location: tiki-list_quizzes.php");
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

if ($tiki_p_admin_quizzes != 'y') {
	$smarty->assign('msg', tra("You don't have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

$cat_type = 'quiz';
$cat_objid = $_REQUEST["quizId"];
include_once ("categorize_list.php");

if (isset($_REQUEST["save"])) {
	check_ticket('edit-quiz-question');
	$cat_href = "tiki-quiz.php?quizId=" . $cat_objid;
	$cat_name = $_REQUEST["name"];
	$cat_desc = substr($_REQUEST["description"], 0, 200);
	include_once ("categorize.php");

	echo "line: ".__LINE__."<br>";
	echo "Sorry, this is only a prototype at present.<br>";

	foreach ($_REQUEST as $key => $request){
		echo $key." = ".$request."<br>";
	}

	// Have to parse the data and bail out if there is an error.
	//
	// Store the new or revised information
	// If everything works, preview the quiz.
	// 
	die;
}


$quiz = $quizlib->get_quiz($_REQUEST["quizId"]);
// echo "line ".__LINE__."<br>";
// foreach ($quiz as $key => $val){
// 	echo $key." = ".$val."<br>";
// }
// die;

$smarty->assign('quiz', $quiz);

// echo __LINE__."<br>";
// die;

// Fill array with possible number of questions per page

$positions = array();

for ($i = 1; $i < 100; $i++)
	$positions[] = $i;

$smarty->assign('positions', $positions);
ask_ticket('edit-quiz-question');

// GGG scaffolding -start

$quiz['name'] = "Test Chapter 01";
$quiz['description'] = "Quiz on Chapter 01 of Tom Sawyer";

// The default publish date to be Jan 1, of this year at midnight.
$quiz['publishDate'] = mktime(0, 0, 0, 1, 1,  date("Y"));

// The default expire date to be 10 years after the default publish date
$quiz['expireDate'] = mktime(0, 0, 0, 1, 1,  date("Y")+10);

// GGG scaffolding -end

$smarty->assign_by_ref('quiz', $quiz);

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

// Additional data for smarty
$tzName = $tikilib->get_display_timezone($user);
if ($tzName == "Local"){
	$tzName = "";
}
$smarty->assign('siteTimeZone', $tzName);

// Display the template
$smarty->assign('mid', 'tiki-quiz_edit.tpl');
$smarty->display("tiki.tpl");

// if (isset($_REQUEST["import"])) {
// 	check_ticket('edit-quiz-question');

// 	$questions = TextToQuestions($_REQUEST["input_data"]);

// 	foreach ($questions as $question){
// 		$question_text = $question->getQuestion();
// 		$id = $quizlib->replace_quiz_question(0, $question_text, 'o', $_REQUEST["quizId"], 0);
// 		for ($i = 0; $i < $question->getChoiceCount(); $i++){
// 			$a = $question->GetChoice($i);
// 			$b = $question->GetCorrect($i);
// 			$quizlib->replace_question_option(0, $a, $b, $id);
// 		}
// 	}

// 	$smarty->assign('question', '');
// 	$smarty->assign('questionId', 0);
// }

?>
