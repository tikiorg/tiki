<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-survey_stats.php,v 1.16 2007-10-12 07:55:32 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'surveys';
require_once ('tiki-setup.php');
include_once ('lib/surveys/surveylib.php');

$auto_query_args = array('sort_mode','offset','find');

if ($prefs['feature_surveys'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_surveys");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_view_survey_stats != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'created_desc';
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

$channels = $srvlib->list_surveys($offset, $maxRecords, $sort_mode, $find);

$temp_max = count($channels["data"]);
for ($i = 0; $i < $temp_max; $i++) {
	if ($userlib->object_has_one_permission($channels["data"][$i]["surveyId"], 'survey')) {
		$channels["data"][$i]["individual"] = 'y';

		if ($userlib->object_has_permission($user, $channels["data"][$i]["surveyId"], 'survey', 'tiki_p_take_survey')) {
			$channels["data"][$i]["individual_tiki_p_take_survey"] = 'y';
		} else {
			$channels["data"][$i]["individual_tiki_p_take_survey"] = 'n';
		}

		if ($userlib->object_has_permission($user, $channels["data"][$i]["surveyId"], 'survey', 'tiki_p_view_survey_stats')) {
			$channels["data"][$i]["individual_tiki_p_view_survey_stats"] = 'y';
		} else {
			$channels["data"][$i]["individual_tiki_p_view_survey_stats"] = 'n';
		}

		if ($tiki_p_admin
			== 'y' || $userlib->object_has_permission($user, $channels["data"][$i]["surveyId"], 'survey', 'tiki_p_admin_surveys')) {
			$channels["data"][$i]["individual_tiki_p_take_survey"] = 'y';

			$channels["data"][$i]["individual_tiki_p_view_survey_stats"] = 'y';
			$channels["data"][$i]["individual_tiki_p_admin_surveys"] = 'y';
		}
	} else {
		$channels["data"][$i]["individual"] = 'n';
	}
}

$smarty->assign_by_ref('cant_pages', $channels["cant"]);

$smarty->assign_by_ref('channels', $channels["data"]);

$smarty->assign('section',$section);
include_once ('tiki-section_options.php');

ask_ticket('survey-stats');

// Display the template
$smarty->assign('mid', 'tiki-survey_stats.tpl');
$smarty->display("tiki.tpl");

?>
