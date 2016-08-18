<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'surveys';
require_once ('tiki-setup.php');
include_once ('lib/surveys/surveylib.php');
$access->check_feature('feature_surveys');
$access->check_permission('tiki_p_take_survey');

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
Perms::bulk(array( 'type' => 'survey' ), 'object', $channels['data'], 'surveyId');
$temp_max = count($channels["data"]);
for ($i = 0; $i < $temp_max; $i++) {
	$survperms = Perms::get(array( 'type' => 'survey', 'object' => $channels['data'][$i]['surveyId'] ));
	$channels["data"][$i]["individual_tiki_p_take_survey"] = $survperms->take_survey ? 'y' : 'n';
	$channels["data"][$i]["individual_tiki_p_view_survey_stats"] = $survperms->view_survey_stats ? 'y' : 'n';
	$channels["data"][$i]["individual_tiki_p_admin_surveys"] = $survperms->admin_surveys ? 'y' : 'n';

	if ($tikilib->user_has_voted($user, 'survey' . $channels["data"][$i]["surveyId"])) {
		$channels["data"][$i]["taken_survey"] = 'y';
	} else {
		$channels["data"][$i]["taken_survey"] = 'n';
	}
}
$smarty->assign_by_ref('cant_pages', $channels["cant"]);
$smarty->assign_by_ref('channels', $channels["data"]);
include_once ('tiki-section_options.php');
ask_ticket('list-surveys');
// Display the template
$smarty->assign('mid', 'tiki-list_surveys.tpl');
$smarty->display("tiki.tpl");
