<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
(The expire date must be rigid.  That is, papers will not be accepted after the expire
date, even if the quiz was started before the expire date.  Otherwise, in cases where
we have "show answers after expire date", a student who has completed the quiz could
give the answers to another student during the grace period.  So when the student
takes the quiz, the system must issue a warning to the student about the expire date
if the date is less the the session timeout time away.)
(Quiz results for students are always stored; quiz results for teachers
(defined here as anyone with tiki_p_admin_quizzes) are never stored.)

The data field is supposed to be xml representing things like the prolog, questions and
epilog for this quiz.  At present if you put
<?questions nQuestions=10 ?>
It is supposed to use 10 questions for this quiz from the tiki_quiz_questions table.
If you leave the data field blank, the default is to use all the questions from the table.
You can also set the same option under the Generl Options section.
*/
require_once ('tiki-setup.php');
include_once ('lib/quizzes/quizlib.php');

$access->check_feature('feature_quizzes');
//Use 12- or 24-hour clock for $publishDate time selector based on admin and user preferences
include_once ('lib/userprefs/userprefslib.php');
$smarty->assign('use_24hr_clock', $userprefslib->get_user_clock_pref($user));

// quizId of 0 is used as a place holder; There should NEVER be a row in the
//   tiki_quizzes table with an id of zero.
if (!isset($_REQUEST["quizId"])) {
	$_REQUEST["quizId"] = 0;
}

$tikilib->get_perm_object($_REQUEST["quizId"], 'quiz');

$access->check_permission('tiki_p_admin_quizzes');

$cat_type = 'quiz';
$cat_objid = $_REQUEST["quizId"];
include_once ("categorize_list.php");
if (isset($_REQUEST["preview"]) || isset($_REQUEST["xmlview"]) || isset($_REQUEST["textview"])) {
	echo "line: " . __LINE__ . "<br>";
	echo "Sorry, preview, xmlview and textview are not supported in this version.<br>";
	// 	foreach ($_REQUEST as $key => $request){
	// 		echo $key." = ".$request."<br>";
	// 	}
	die;
}
$quiz = $quizlib->quiz_fetch($_REQUEST["quizId"]);
function fetchYNOption(&$quiz, $_REQUEST, $option) {
	if (isset($_REQUEST[$option]) && $_REQUEST[$option] == 'on') {
		$quiz[$option] = 'y';
	} else {
		$quiz[$option] = 'n';
	}
}
// Load the data from the
function quiz_data_load() {
	global $_REQUEST;
	$quiz_data = array();
	foreach($_REQUEST as $key => $val) {
		if (preg_match("/^quiz_/", $key)) {
			$k = preg_replace("/^quiz_([.]*)/", "\$1", $key);
			$quiz_data[$k] = $val;
		}
	}
	if ($quiz_data["online"] == "online") {
		$quiz_data["online"] = "y";
	} else if ($quiz_data["online"] == "offline") {
		$quiz_data["online"] = "n";
	}
	//Convert 12-hour clock hours to 24-hour scale to compute time
	if (!empty($_REQUEST['publish_Meridian'])) {
		$_REQUEST['publish_Hour'] = date('H', strtotime($_REQUEST['publish_Hour'] . ':00 ' . $_REQUEST['publish_Meridian']));
	}
	if (!empty($_REQUEST['expire_Meridian'])) {
		$_REQUEST['expire_Hour'] = date('H', strtotime($_REQUEST['expire_Hour'] . ':00 ' . $_REQUEST['expire_Meridian']));
	}
	
	$quiz_data["datePub"] = TikiLib::make_time($quiz_data["publish_Hour"], $quiz_data["publish_Minute"], 0, $quiz_data["publish_Month"], $quiz_data["publish_Day"], $quiz_data["publish_Year"]);
	$quiz_data["dateExp"] = TikiLib::make_time($quiz_data["expire_Hour"], $quiz_data["expire_Minute"], 0, $quiz_data["expire_Month"], $quiz_data["expire_Day"], $quiz_data["expire_Year"]);
	$fields = array('nQuestion', 'shuffleAnswers', 'shuffleQuestions', 'multiSession', 'additionalQuestions', 'limitDisplay', 'timeLimited', 'canRepeat', 'additionalQuestions', 'forum');
	foreach($fields as $field) {
		fetchYNOption($quiz_data, $quiz_data, $field);
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
	// 	die;
	$quiz_data = quiz_data_load();
	// 	foreach($quiz_data as $key => $val){
	// 		echo $key." = ".$val."<br />";
	// 	}
	// 	die;
	$quizNew = new Quiz;
	$quizNew->data_load($quiz_data);
	// echo __FILE__." line: ".__LINE__."<br />";
	// if the id is 0, use just save the new data
	// otherwise we compare the data to what was there before.
	if ($quiz->id == 0 || ($quizNew != $quiz)) {
		$quizlib->quiz_store($quizNew);
		// tell user changes were stored (new quiz stored with id of x or quiz x modified), return to list of admin quizzes
		
	} else {
		// tell user no changes were stored, return to list of admin quizzes
		
	}
	// This way for including questions is was too complicated.  Need to think of a simpler way.
	die;
	echo "line: " . __LINE__ . "<br>";
	echo "Sorry, this is only a prototype at present.<br>";
	// Fixme, this doesn't work for a brand-new quiz because the quizId is zero!
	if ($cat_objid != 0) {
		$cat_href = "tiki-quiz.php?quizId=" . $cat_objid;
		$cat_name = $_REQUEST["name"];
		$cat_desc = substr($_REQUEST["description"], 0, 200);
		include_once ("categorize.php");
	}
	die;
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
function setup_options(&$tpl) {
	//	global $smarty;
	global $tikilib;
	global $user;
	$tpl['online_choices'] = array('online' => 'Online', 'offline' => 'Offline');
	$optionsGrading = array();
	$optionsGrading[] = "machine";
	$optionsGrading[] = "peer review";
	$optionsGrading[] = "teacher";
	$tpl['optionsGrading'] = $optionsGrading;
	$optionsShowScore = array();
	$optionsShowScore[] = "immediately";
	$optionsShowScore[] = "after expire date";
	$optionsShowScore[] = "never";
	$tpl['optionsShowScore'] = $optionsShowScore;
	// FIXME - This needs to be limited to the session timeout in php.ini
	$mins = array();
	for ($i = 1; $i <= 20; $i++) {
		$mins[] = $i;
	}
	$tpl['mins'] = $mins;
	$repetitions = array();
	$qpp = array();
	for ($i = 1; $i <= 10; $i++) {
		$qpp[] = $i;
		$repetitions[] = $i;
	}
	$repetitions[] = "unlimited";
	$qpp[] = "unlimited";
	$tpl['repetitions'] = $repetitions;
	$tpl['qpp'] = $qpp;
	$tpl['siteTimeZone'] = $prefs['display_timezone'];
}
$tpl = array();
setup_options($tpl);
$smarty->assign('tpl', $tpl);
ask_ticket('edit-quiz-question');

$smarty->assign('mid', 'tiki-quiz_edit.tpl');
$smarty->display("tiki.tpl");
