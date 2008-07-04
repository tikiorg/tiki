<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-faq_questions.php,v 1.28 2007-10-12 07:55:27 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'faqs';
require_once ('tiki-setup.php');

include_once ('lib/faqs/faqlib.php');

if ($prefs['feature_faqs'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_faqs");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin_faqs != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["faqId"])) {
	$smarty->assign('msg', tra("No questions group indicated"));

	$smarty->display("error.tpl");
	die;
}

$smarty->assign('faqId', $_REQUEST["faqId"]);
$faq_info = $tikilib->get_faq($_REQUEST["faqId"]);
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
  $area = 'delfaqquestion';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$faqlib->remove_faq_question($_REQUEST["remove"]);
  } else {
    key_get($area);
  }
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

$cant_pages = ceil($channels["cant"] / $maxRecords);
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

$suggested = $faqlib->list_suggested_questions(0, -1, 'created_desc', '', $_REQUEST["faqId"]);
$smarty->assign_by_ref('suggested', $suggested["data"]);

include_once("textareasize.php");
include_once ('lib/quicktags/quicktagslib.php');
$quicktags = $quicktagslib->list_quicktags(0,20,'taglabel_desc','','faqs');
$smarty->assign_by_ref('quicktags', $quicktags["data"]);
include_once ('tiki-section_options.php');
ask_ticket('faq-questions');

// Display the template
$smarty->assign('mid', 'tiki-faq_questions.tpl');
$smarty->display("tiki.tpl");

?>
