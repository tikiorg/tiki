<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-quiz_edit.php,v 1.7 2004-05-18 21:01:31 ggeller Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, 
//                          George G. Geller et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

/*

  Must check for any students having accessed the quiz before
	allowing changes.
  
  Status
	N students have taken this quiz.
	M quizzes have been machine graded
	J quizzes have been graded by peer review
	K quizzes have been graded by graders and teachers

	(The expire date must be rigid.  That is, papers will not be accepted after the expire
		date, even if the quiz was started before the expire date.  Otherwise, in cases where
		we have "show answers after expire date", a student who has completed the quiz could
		give the answers to another student during the grace period.  So when the student
		takes the quiz, the system must issue a warning to the student about the expire date
		if the date is less the the session timeout time away.)
	(Quiz results are always stored.)

*/

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

$dc = &$tikilib->get_date_converter($user);

if (isset($_REQUEST["preview"]) || isset($_REQUEST["xmlview"])|| isset($_REQUEST["textview"])) {
	echo "line: ".__LINE__."<br>";
	echo "Sorry, this is only a prototype at present.<br>";

	foreach ($_REQUEST as $key => $request){
		echo $key." = ".$request."<br>";
	}
	die;
}

function	fetchYNOption(&$quiz, $_REQUEST, $option){
	if (isset($_REQUEST[$option]) && $_REQUEST[$option] == 'on'){
		$quiz[$option] = 'y';
	} else {
		$quiz[$option] = 'n';
	}
}

if (isset($_REQUEST["save"])) {
// 	foreach ($_REQUEST as $key => $val){
// 		echo $key." = ".$val."<br>";
// 	}
// 	die;

	check_ticket('edit-quiz-question');
	$cat_href = "tiki-quiz.php?quizId=" . $cat_objid;
	$cat_name = $_REQUEST["name"];
	$cat_desc = substr($_REQUEST["description"], 0, 200);
	//	$cat_objid = $_REQUEST["quizId"];
	include_once ("categorize.php");

	echo "line: ".__LINE__."<br>";
	echo "Sorry, this is only a prototype at present.<br>";

	$quiz = array();

	if (!isset($_REQUEST['online']) && !($_REQUEST["online"] =! "choice_online" || $_REQUEST["online"] =! "choice_offline")){
		echo "line: ".__LINE__."<br>";
		echo 'Invalid value for $_REQUEST["online"].  Is your tpl file correct?<br>';
		die;
	}
	if ($_REQUEST["online"] == "choice_online"){
		$quiz["online"] = "y";
	}
	else if ($_REQUEST["online"] == "choice_offline"){
		$quiz["online"] = "n";
	}

	$quiz["name"] = $_REQUEST["name"];
	$quiz["description"] = $_REQUEST["description"];

  $quiz["datePub"] = $dc->getServerDateFromDisplayDate(mktime($_REQUEST["publish_Hour"],
    $_REQUEST["publish_Minute"], 0, $_REQUEST["publish_Month"], $_REQUEST["publish_Day"], 
    $_REQUEST["publish_Year"]));

  $quiz["dateExp"] = $dc->getServerDateFromDisplayDate(mktime($_REQUEST["expire_Hour"],
    $_REQUEST["expire_Minute"], 0, $_REQUEST["expire_Month"], $_REQUEST["expire_Day"], 
    $_REQUEST["expire_Year"]));
 
	$fields = array('shuffleAnswers',
									'shuffleQuestions',
									'multiSession',
									'additionalQuestions',
									'limitDisplay',
									'timeLimited',
									'canRepeat',
									'additionalQuestions',
									'forum');
	foreach ($fields as $field){
		fetchYNOption(&$quiz, $_REQUEST, $field);
		echo '$quiz["'.$field.'"] = '.$quiz[$field]."<br>";
 	}

	$quiz['questionsPerPage'] = $_REQUEST['questionsPerPage'];
	echo '$quiz["questionsPerPage"] = '.$quiz["questionsPerPage"]."<br>";

	$quiz['timeLimit'] = $_REQUEST['timeLimit'];
	echo '$quiz["timeLimit"] = '.$quiz["timeLimit"]."<br>";

	$quiz['repetitions'] = $_REQUEST['repetitions'];
	echo '$quiz["repetitions"] = '.$quiz["repetitions"]."<br>";

	$quiz['grading-method'] = $_REQUEST['grading-method'];
	echo '$quiz["grading-method"] = '.$quiz["grading-method"]."<br>";

	$quiz['showScore'] = $_REQUEST['showScore'];
	echo '$quiz["showScore"] = '.$quiz["showScore"]."<br>";

	$quiz['showCorrectAnswers'] = $_REQUEST['showCorrectAnswers'];
	echo '$quiz["showCorrectAnswers"] = '.$quiz["showCorrectAnswers"]."<br>";

	$quiz['publishStats'] = $_REQUEST['publishStats'];
	echo '$quiz["publishStats"] = '.$quiz["publishStats"]."<br>";

	$quiz['forumName'] = $_REQUEST['forumName'];
	echo '$quiz["forumName"] = '.$quiz["forumName"]."<br>";

	die;

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

$smarty->assign('online_choices', array(
			'choice_offline' => 'Offline',
			'choice_online'  => 'Online'));
$smarty->assign('online', 'choice_offline');

$quiz['online'] = "n";

$quiz['name'] = "Chapter 01";
$quiz['description'] = "Quiz on Chapter 01 of Tom Sawyer";
$quiz['version'] = 'x';

// The taken and history stuff has to come from the database.
$quiz['taken'] = 'y';
$quiz['history'] = array();
$quiz['history'][] = "and so on...";
$quiz['history'][] = "Version 3 was attempted by student(s) 3 time(s).";
$quiz['history'][] = "Version 2 was attempted by student(s) 2 time(s).";
$quiz['history'][] = "Version 1 was attempted by student(s) 1 time(s).";

// The default publish date to be Jan 1, of this year at midnight.
$quiz['datePub'] = mktime(0, 0, 0, 1, 1,  date("Y"));

// The default expire date to be 10 years after the default publish date
$quiz['dateExp'] = mktime(0, 0, 0, 1, 1,  date("Y")+10);

$quiz['grading'] = "machine";

$quiz['showScore'] = "immediately";

$quiz['showCorrectAnswers'] = "immediately";

// GGG scaffolding -end

$optionsGrading = array();
$optionsGrading[] = "machine";
$optionsGrading[] = "peer review";
$optionsGrading[] = "teacher";
$smarty->assign('optionsGrading', $optionsGrading);

$optionsShowScore = array();
$optionsShowScore[] = "immediately";
$optionsShowScore[] = "after expire date";
$optionsShowScore[] = "never";
$smarty->assign('optionsShowScore', $optionsShowScore);

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
