<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'quizzes';
require_once ('tiki-setup.php');
$quizlib = TikiLib::lib('quiz');
$auto_query_args = array('sort_mode', 'offset', 'find');
$access->check_feature('feature_quizzes');
$access->check_permission('tiki_p_take_quiz');
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
$channels = $quizlib->list_quizzes($offset, $maxRecords, $sort_mode, $find);
Perms::bulk(array( 'type' => 'quiz' ), 'object', $channels['data'], 'quizId');
$temp_max = count($channels["data"]);
for ($i = 0; $i < $temp_max; $i++) {
	$quizperms = Perms::get(array( 'type' => 'quiz', 'object' => $channels['data'][$i]['quizId'] ));
	$channels["data"][$i]["individual_tiki_p_take_quiz"] = $quizperms->take_quiz ? 'y' : 'n';
	$channels["data"][$i]["individual_tiki_p_view_quiz_stats"] = $quizperms->view_quiz_stats ? 'y' : 'n';
	$channels["data"][$i]["individual_tiki_p_view_user_stats"] = $quizperms->view_user_stats ? 'y' : 'n';
	$channels["data"][$i]["individual_tiki_p_admin_quizzes"] = $quizperms->admin_quizzes ? 'y' : 'n';
}
$smarty->assign_by_ref('cant_pages', $channels["cant"]);
$smarty->assign_by_ref('channels', $channels["data"]);
include_once ('tiki-section_options.php');
ask_ticket('list-quizzes');
// Display the template
$smarty->assign('mid', 'tiki-list_quizzes.tpl');
$smarty->display("tiki.tpl");
