<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-take_survey.php,v 1.18.2.1 2007-12-07 05:56:38 mose Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/surveys/surveylib.php');

if ($prefs['feature_categories'] == 'y') {
	global $categlib;
	if (!is_object($categlib)) {
		include_once('lib/categories/categlib.php');
	}
}

if ($prefs['feature_surveys'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_surveys");

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["surveyId"])) {
	$smarty->assign('msg', tra("No survey indicated"));

	$smarty->display("error.tpl");
	die;
}

$smarty->assign('individual', 'n');

if ($userlib->object_has_one_permission($_REQUEST["surveyId"], 'survey')) {
	$smarty->assign('individual', 'y');

	if ($tiki_p_admin != 'y') {
		$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'surveys');

		foreach ($perms["data"] as $perm) {
			$permName = $perm["permName"];

			if ($userlib->object_has_permission($user, $_REQUEST["surveyId"], 'survey', $permName)) {
				$$permName = 'y';

				$smarty->assign("$permName", 'y');
			} else {
				$$permName = 'n';

				$smarty->assign("$permName", 'n');
			}
		}
	}
} elseif ($tiki_p_admin != 'y' && $prefs['feature_categories'] == 'y') {
	$perms_array = $categlib->get_object_categories_perms($user, 'survey', $_REQUEST['surveyId']);
   	if ($perms_array) {
   		$is_categorized = TRUE;
    	foreach ($perms_array as $perm => $value) {
    		$$perm = $value;
    	}
   	} else {
   		$is_categorized = FALSE;
   	}
	if ($is_categorized && isset($tiki_p_view_categorized) && $tiki_p_view_categorized != 'y') {
		if (!isset($user)){
			$smarty->assign('msg',$smarty->fetch('modules/mod-login_box.tpl'));
			$smarty->assign('errortitle',tra("Please login"));
		} else {
			$smarty->assign('msg',tra("Permission denied you cannot view this page"));
    	}
	    $smarty->display("error.tpl");
		die;
	}
}

$smarty->assign('surveyId', $_REQUEST["surveyId"]);
$survey_info = $srvlib->get_survey($_REQUEST["surveyId"]);

if ($tiki_p_take_survey != 'y') {
	$smarty->assign('msg', tra("You don't have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

// Check if user has taken this survey
if ($tiki_p_admin != 'y') {
	if ($tikilib->user_has_voted($user, 'survey' . $_REQUEST["surveyId"])) {
		$smarty->assign('msg', tra("You cannot take this survey twice"));

		$smarty->display("error.tpl");
		die;
	}
}

if ($_REQUEST["vote"])
  $srvlib->add_survey_hit($_REQUEST["surveyId"]);

$smarty->assign('survey_info', $survey_info);

$questions = $srvlib->list_survey_questions($_REQUEST["surveyId"], 0, -1, 'position_asc', '');
$smarty->assign_by_ref('questions', $questions["data"]);

if (isset($_REQUEST["ans"])) {
	check_ticket('take-survey');
	foreach ($questions["data"] as $question) {
		$questionId = $question["questionId"];

		//print("question: $questionId<br />");
		if (isset($_REQUEST["question_" . $questionId])) {
			if ($question["type"] == 'm') {
				// If we have a multiple question
				$ids = array_keys($_REQUEST["question_" . $questionId]);

				//print_r($ids);
				// Now for each of the options we increase the number of votes
				foreach ($ids as $optionId) {
					$srvlib->register_survey_option_vote($questionId, $optionId);
				}
			} else {
				$value = $_REQUEST["question_" . $questionId];

				//print("value: $value<br />");
				if ($question["type"] == 'r' || $question["type"] == 's') {
					$srvlib->register_survey_rate_vote($questionId, $value);
				} elseif ($question["type"] == 't' || $question["type"] == 'x') {
					$srvlib->register_survey_text_option_vote($questionId, $value);
				} else {
					$srvlib->register_survey_option_vote($questionId, $value);
				}
			}
		}
	}

	$tikilib->register_user_vote($user, 'survey' . $_REQUEST["surveyId"]);
	header ("location: tiki-list_surveys.php");
}

//print_r($questions);
$section = 'surveys';
include_once ('tiki-section_options.php');

include_once("textareasize.php");

include_once ('lib/quicktags/quicktagslib.php');
$quicktags = $quicktagslib->list_quicktags(0,-1,'taglabel_desc','','wiki');
$smarty->assign_by_ref('quicktags', $quicktags["data"]);
$smarty->assign('quicktagscant', $quicktags["cant"]);

ask_ticket('take-survey');

// Display the template
$smarty->assign('mid', 'tiki-take_survey.tpl');
$smarty->display("tiki.tpl");

?>
