<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'faqs';
require_once ('tiki-setup.php');

$faqlib = TikiLib::lib('faq');

$access->check_feature('feature_faqs');
$access->check_permission('tiki_p_admin_faqs');

if (!isset($_REQUEST["faqId"])) {
	$smarty->assign('msg', tra("No questions group indicated"));

	$smarty->display("error.tpl");
	die;
}

$smarty->assign('faqId', $_REQUEST["faqId"]);
$faq_info = $faqlib->get_faq($_REQUEST["faqId"]);
$smarty->assign('faq_info', $faq_info);

if (!isset($_REQUEST["questionId"])) {
	$_REQUEST["questionId"] = 0;
}

$smarty->assign('questionId', $_REQUEST["questionId"]);

if ($_REQUEST["questionId"]) {
	$info = $faqlib->get_faq_question($_REQUEST["questionId"]);
} else {
	$info = array();

	$info["question"] = '';
	$info["answer"] = '';
}
// $smarty->assign('question',$info["question"]);  AWC moved this
// $smarty->assign('answer',$info["answer"]);      AWC moved this
if (isset($_REQUEST["remove"])) {
	$access->check_authenticity();
	$faqlib->remove_faq_question($_REQUEST["remove"]);
}

if (!isset($_REQUEST["filter"])) {
	$_REQUEST["filter"] = '';
}

$smarty->assign('filter', $_REQUEST["filter"]);

if (isset($_REQUEST["useq"])) {
	check_ticket('faq-questions');
	$quse = $faqlib->get_faq_question($_REQUEST["usequestionId"]);

	$faqlib->replace_faq_question($_REQUEST["faqId"], 0, $quse["question"], $quse["answer"]);
	$info = $faqlib->get_faq_question($_REQUEST["questionId"]); // AWC added
}

if (isset($_REQUEST["save"])) {
	check_ticket('faq-questions');
	$faqlib->replace_faq_question($_REQUEST["faqId"], $_REQUEST["questionId"], $_REQUEST["question"], $_REQUEST["answer"]);

	$info["question"] = '';
	$info["answer"] = '';
	//$smarty->assign('question',$info["question"]);  AWC moved this
	//$smarty->assign('answer',$info["answer"]);      AWC moved this
	$smarty->assign('questionId', 0);
}

$smarty->assign('question', $info["question"]); // moved from above
$smarty->assign('answer', $info["answer"]);     // moved from above

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'position_asc,questionId_asc';
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

if (isset($_REQUEST["remove_suggested"])) {
	$faqlib->remove_suggested_question($_REQUEST["remove_suggested"]);
}

if (isset($_REQUEST["approve_suggested"])) {
	$faqlib->approve_suggested_question($_REQUEST["approve_suggested"]);
}

$smarty->assign_by_ref('sort_mode', $sort_mode);
$channels = $faqlib->list_faq_questions($_REQUEST["faqId"], 0, -1, $sort_mode, $find);
$allq = $faqlib->list_all_faq_questions(0, -1, 'position_asc,questionId_asc', $_REQUEST["filter"]);
$smarty->assign_by_ref('allq', $allq["data"]);

$smarty->assign_by_ref('cant_pages', $channels["cant"]);

$smarty->assign_by_ref('channels', $channels["data"]);

$suggested = $faqlib->list_suggested_questions(0, -1, 'created_desc', '', $_REQUEST["faqId"]);
$smarty->assign_by_ref('suggested', $suggested["data"]);

include_once ('tiki-section_options.php');
ask_ticket('faq-questions');


$wikilib = TikiLib::lib('wiki');
$plugins = $wikilib->list_plugins(true, 'faqans');
$smarty->assign_by_ref('plugins', $plugins);

// Display the template
$smarty->assign('mid', 'tiki-faq_questions.tpl');
$smarty->display("tiki.tpl");
