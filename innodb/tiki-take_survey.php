<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'surveys';
require_once ('tiki-setup.php');
include_once ('lib/surveys/surveylib.php');
if ($prefs['feature_categories'] == 'y') {
	global $categlib;
	if (!is_object($categlib)) {
		include_once ('lib/categories/categlib.php');
	}
}

$access->check_feature('feature_surveys');

if (!isset($_REQUEST["surveyId"])) {
	$smarty->assign('msg', tra("No survey indicated"));
	$smarty->display("error.tpl");
	die;
}
$tikilib->get_perm_object( $_REQUEST["surveyId"], 'survey' );

$smarty->assign('surveyId', $_REQUEST["surveyId"]);
$survey_info = $srvlib->get_survey($_REQUEST["surveyId"]);
$smarty->assign('survey_info', $survey_info);
$access->check_permission('tiki_p_take_survey');

// Check if user has taken this survey
if ($tiki_p_admin != 'y') {
	if ($tikilib->user_has_voted($user, 'survey' . $_REQUEST["surveyId"])) {
		$smarty->assign('msg', tra("You cannot take this survey twice"));
		$smarty->display("error.tpl");
		die;
	}
}
if ($_REQUEST["vote"]) $srvlib->add_survey_hit($_REQUEST["surveyId"]);
$questions = $srvlib->list_survey_questions($_REQUEST["surveyId"], 0, -1, 'position_asc', '');
$smarty->assign_by_ref('questions', $questions["data"]);
if (isset($_REQUEST["ans"])) {
	check_ticket('take-survey');
	$error_msg = '';
	$srvlib->register_answers($_REQUEST['surveyId'], $questions['data'], $_REQUEST, $error_msg);
	if ($error_msg == '') header('Location: tiki-list_surveys.php');
}
include_once ('tiki-section_options.php');
ask_ticket('take-survey');
// Display the template
$smarty->assign('error_msg', $error_msg);
$smarty->assign('mid', 'tiki-take_survey.tpl');
$smarty->display("tiki.tpl");
