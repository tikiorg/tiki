<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-quiz_edit.php,v 1.12 2004-05-28 14:03:14 ggeller Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, 
//                          George G. Geller et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

/*

	(The expire date must be rigid.  That is, papers will not be accepted after the expire
		date, even if the quiz was started before the expire date.  Otherwise, in cases where
		we have "show answers after expire date", a student who has completed the quiz could
		give the answers to another student during the grace period.  So when the student
		takes the quiz, the system must issue a warning to the student about the expire date
		if the date is less the the session timeout time away.)
	(Quiz results for students are always stored; quiz results for teachers 
   (defined here as anyone with tiki_p_admin_quizzes) are never stored.)

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

// quizId of 0 is used as a place holder; There should NEVER be a row in the 
//   tiki_quizzes table with an id of zero.
if(!isset($_REQUEST["quizId"])){
	$_REQUEST["quizId"] = 0;
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
	$smarty->assign('msg', tra("You don't have permission to edit quizzes."));

	$smarty->display("error.tpl");
	die;
}

$cat_type = 'quiz';
$cat_objid = $_REQUEST["quizId"];
include_once ("categorize_list.php");

$dc = &$tikilib->get_date_converter($user);

if (isset($_REQUEST["preview"]) || isset($_REQUEST["xmlview"])|| isset($_REQUEST["textview"])) {
	echo "line: ".__LINE__."<br>";
	echo "Sorry, preview, xmlview and textview are not supported in this version.<br>";

// 	foreach ($_REQUEST as $key => $request){
// 		echo $key." = ".$request."<br>";
// 	}
	die;
}

$quiz = $quizlib->quiz_fetch($_REQUEST["quizId"]);

function	fetchYNOption(&$quiz, $_REQUEST, $option){
	if (isset($_REQUEST[$option]) && $_REQUEST[$option] == 'on'){
		$quiz[$option] = 'y';
	} else {
		$quiz[$option] = 'n';
	}
}

// Load the data from the 
function quiz_data_load(){
	global $_REQUEST;
	global $dc;
	$quiz_data = array();
	foreach($_REQUEST as $key => $val){
		if (preg_match("/^quiz_/",$key)){
			$k = preg_replace("/^quiz_([.]*)/","\$1",$key);
			$quiz_data[$k] = $val;
		}
	}
	if ($quiz_data["online"] == "online"){
		$quiz_data["online"] = "y";
	}
	else if ($quiz_data["online"] == "offline"){
		$quiz_data["online"] = "n";
	}

  $quiz_data["datePub"] = $dc->getServerDateFromDisplayDate(mktime($quiz_data["publish_Hour"],
    $quiz_data["publish_Minute"], 0, $quiz_data["publish_Month"], $quiz_data["publish_Day"], 
    $quiz_data["publish_Year"]));

  $quiz_data["dateExp"] = $dc->getServerDateFromDisplayDate(mktime($quiz_data["expire_Hour"],
    $quiz_data["expire_Minute"], 0, $quiz_data["expire_Month"], $quiz_data["expire_Day"], 
    $quiz_data["expire_Year"]));
 
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
		fetchYNOption(&$quiz_data, $quiz_data, $field);
		// echo '$quiz_data["'.$field.'"] = '.$quiz_data[$field]."<br>";
 	}

 	return $quiz_data;
}

if (isset($_REQUEST["save"])) {
	check_ticket('edit-quiz-question');

// 	echo __FILE__." line ".__LINE__."<br />";
// 	foreach($_REQUEST as $key => $val){
// 		if (preg_match("/^quiz_/",$key)){
// 			echo $key." = ".$val."<br />";
// 		}
// 	}

	$quiz_data = quiz_data_load();

// 	foreach($quiz_data as $key => $val){
// 		echo $key." = ".$val."<br />";
// 	}

	$quizNew = new Quiz;
	$quizNew->data_load($quiz_data);

	// echo __FILE__." line: ".__LINE__."<br />";

	// if the id is 0, use just save the new data
	// otherwise we compare the data to what was there before.
	if ($quiz->id == 0 || ($quizNew != $quiz)){
		$quizlib->quiz_store($quizNew);
		// tell user changes were stored (new quiz stored with id of x or quiz x modified), return to list of admin quizzes
	}
	else {
		// tell user no changes were stored, return to list of admin quizzes
	}

	// This way for including questions is was too complicated.  Need to think of a simpler way.
 	die;

	echo "line: ".__LINE__."<br>";
	echo "Sorry, this is only a prototype at present.<br>";


	// Fixme, this doesn't work for a brand-new quiz because the quizId is zero!
	if ($cat_objid != 0){
		$cat_href = "tiki-quiz.php?quizId=" . $cat_objid;
		$cat_name = $_REQUEST["name"];
		$cat_desc = substr($_REQUEST["description"], 0, 200);
		include_once ("categorize.php");
	}


	die;


	// See tiki-edit_quiz_questions.php for how to get import the quiz questions.
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

	// Have to parse the data and bail out if there is an error.
	//
	// Store the new or revised information
	// If everything works, preview the quiz.
	// 
}

// Scaffolding
// The taken and history stuff has to come from version and studentAttempts fields in
//  the tiki_quiz table in the database.
// $quiz->taken = 'y';
// $quiz->history = array();
// $quiz->history[] = "and so on...";
// $quiz->history[] = "Version 3 (date stamp) was attempted by students 3 time(s).";
// $quiz->history[] = "Version 2 (date stamp) was attempted by students 2 time(s).";
// $quiz->history[] = "Version 1 (date stamp) was attempted by students 1 time(s).";

$smarty->assign('quiz', $quiz);

// echo __LINE__."<br>";
// echo '$quiz->id = '.$quiz->id."<br>";
// die;

function setup_options(&$tpl){
	global $smarty;
	global $tikilib;
	global $user;

	$tpl['online_choices'] = array('online'  => 'Online',	'offline' => 'Offline');
	
	$optionsGrading = array();
	$optionsGrading[] = "machine";
	$optionsGrading[] = "peer review";
	$optionsGrading[] = "teacher";
	$tpl['optionsGrading'] = $optionsGrading;
// 	$smarty->assign('optionsGrading', $optionsGrading);

	$optionsShowScore = array();
	$optionsShowScore[] = "immediately";
	$optionsShowScore[] = "after expire date";
	$optionsShowScore[] = "never";
	$tpl['optionsShowScore'] = $optionsShowScore;
// 	$smarty->assign('optionsShowScore', $optionsShowScore);
	
	// FIXME - This needs to be limited to the session timeout in php.ini
	$mins = array();
	for ($i = 1; $i <= 20; $i++){
		$mins[] = $i;
	}
	$tpl['mins'] = $mins;
	//	$smarty->assign('mins', $mins);
	
	$repetitions = array();
	$qpp = array();
	
	for ($i = 1; $i <= 10; $i++){
		$qpp[] = $i;
		$repetitions[] = $i;
	}
	$repetitions[] = "unlimited";
	$qpp[] = "unlimited";
	$tpl['repetitions'] = $repetitions;
	//	$smarty->assign('repetitions', $repetitions);
	$tpl['qpp'] = $qpp;
	//	$smarty->assign('qpp', $qpp);
	
	// $tpl['questionsPerPage'] = "Unlimited";
	//	$smarty->assign('questionsPerPage', "Unlimited");
	
	// Additional data for smarty
	$tzName = $tikilib->get_display_timezone($user);
	if ($tzName == "Local"){
		$tzName = "";
	}
	// $smarty->assign('siteTimeZone', $tzName);
	$tpl['siteTimeZone'] = $tzName;
}

$tpl = array();
setup_options(&$tpl);
$smarty->assign('tpl', $tpl);

ask_ticket('edit-quiz-question');

// Display the template
$smarty->assign('mid', 'tiki-quiz_edit.tpl');

// $smarty->display("tiki.tpl");
$smarty->display("ggg-tiki.tpl");

// Scraps
// if ($_REQUEST["quizId"] == 0) { // When the quiz id is not indicated, create a new quiz
// 	$quiz = new Quiz;

// 	// scaffolding
// // 	echo "line ".__LINE__."<br>";
// // 	$lines = $quiz->show_html();
// // 	foreach ($lines as $line){
// // 		echo $line;
// // 	}
// // 	die;
// } else {
// 	$quiz = $quizlib->get_quiz($_REQUEST["quizId"]);
// 	echo "line ".__LINE__."<br>";
// 	foreach ($quiz as $key => $val){
// 		echo $key." = ".$val."<br>";
// 	}
// 	echo "publishDate = ".date("r",$quiz['publishDate'])."<br>";
// 	echo "expireDate = ".date("r",$quiz['expireDate'])."<br>";
// 	die;
// 	$quiz = $quizlib->quiz_fetch($_REQUEST["quizId"]);
// }

?>
