<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_survey_questions.php,v 1.18 2007-10-12 07:55:24 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/surveys/surveylib.php');

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

$smarty->assign('surveyId', $_REQUEST["surveyId"]);

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
}

if ($tiki_p_admin_surveys != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

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
}

$smarty->assign_by_ref('info', $info);

if (isset($_REQUEST["remove"])) {
  $area = 'delsurveyquestion';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$srvlib->remove_survey_question($_REQUEST["remove"]);
	} else {
		key_get($area);
	}
}

if (isset($_REQUEST["save"])) {
	check_ticket('admin-survey-questions');
	$srvlib->replace_survey_question($_REQUEST["questionId"], $_REQUEST["question"], $_REQUEST["type"], $_REQUEST["surveyId"],
		$_REQUEST["position"], $_REQUEST["options"]);

	$info["question"] = '';
	$info["type"] = '';
	$info["position"] = '';
	$info["options"] = '';
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
//$smarty->assign('questions',$channels["data"]);
$cant_pages = ceil($channels["cant"] / $maxRecords);
if (empty($info["position"])) {
	$info["position"] = $channels["cant"] + 1;
}
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

ask_ticket('admin-survey-questions');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-admin_survey_questions.tpl');
$smarty->display("tiki.tpl");

?>
