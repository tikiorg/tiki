<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/surveys/surveylib.php');
$auto_query_args = array(
	'surveyId',
	'questionId',
	'offset',
	'find',
	'sort_mode',
	'maxRecords'
);
$access->check_feature('feature_surveys');

if (!isset($_REQUEST["surveyId"])) {
	$smarty->assign('msg', tra("No survey indicated"));
	$smarty->display("error.tpl");
	die;
}
$smarty->assign('surveyId', $_REQUEST["surveyId"]);
$smarty->assign('individual', 'n');
if ($userlib->object_has_one_permission($_REQUEST["surveyId"], 'survey')) {
	$smarty->assign('individual', 'y');
	if ($tiki_p_admin != 'y') {
		$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'surveys');
		foreach($perms["data"] as $perm) {
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
}
$access->check_permission('tiki_p_admin_surveys');

$survey_info = $srvlib->get_survey($_REQUEST["surveyId"]);
$smarty->assign('survey_info', $survey_info);
if (!isset($_REQUEST["questionId"])) {
	$_REQUEST["questionId"] = 0;
}
$smarty->assign('questionId', $_REQUEST["questionId"]);
if ($_REQUEST["questionId"]) {
	$info = $srvlib->get_survey_question($_REQUEST["questionId"]);
} else {
	$info = array();
	$info["question"] = '';
	$info["type"] = '';
	$info["position"] = '';
	$info["options"] = '';
	$info["mandatory"] = '';
	$info["min_answers"] = '';
	$info["max_answers"] = '';
}
$smarty->assign_by_ref('info', $info);
if (isset($_REQUEST["remove"])) {
	$access->check_authenticity();
	$srvlib->remove_survey_question($_REQUEST["remove"]);
}
if (isset($_REQUEST["save"])) {
	check_ticket('admin-survey-questions');
	$srvlib->replace_survey_question($_REQUEST["questionId"], $_REQUEST["question"], $_REQUEST["type"], $_REQUEST["surveyId"], $_REQUEST["position"], $_REQUEST["options"], isset($_REQUEST["mandatory"]) ? 'y' : 'n', $_REQUEST["min_answers"], $_REQUEST["max_answers"]);
	$info["question"] = '';
	$info["type"] = '';
	$info["position"] = '';
	$info["options"] = '';
	$info["mandatory"] = '';
	$info["min_answers"] = '';
	$info["max_answers"] = '';
	$smarty->assign('questionId', 0);
	$smarty->assign('info', $info);
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
$channels = $srvlib->list_survey_questions($_REQUEST["surveyId"], $offset, $maxRecords, $sort_mode, $find);
if (empty($info["position"])) {
	$info["position"] = $channels["cant"] + 1;
}
$smarty->assign_by_ref('cant_pages', $channels["cant"]);
$smarty->assign_by_ref('channels', $channels["data"]);
// Fill array with possible number of questions per page
$positions = array();
for ($i = 1; $i < 100; $i++) $positions[] = $i;
$smarty->assign('positions', $positions);
ask_ticket('admin-survey-questions');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template
$smarty->assign('mid', 'tiki-admin_survey_questions.tpl');
$smarty->display("tiki.tpl");
