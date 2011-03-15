<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'faqs';
require_once ('tiki-setup.php');
include_once ('lib/faqs/faqlib.php');
if ($prefs['feature_categories'] == 'y') {
	global $categlib;
	if (!is_object($categlib)) {
		include_once ('lib/categories/categlib.php');
	}
}

$access->check_feature('feature_faqs');

if (!isset($_REQUEST["faqId"])) {
	$smarty->assign('msg', tra("No faq indicated"));
	$smarty->display("error.tpl");
	die;
}

$smarty->assign('headtitle', tra('FAQs'));
$tikilib->get_perm_object( $_REQUEST['faqId'], 'faq' );

$access->check_permission('tiki_p_view_faqs');

$faqlib->add_faq_hit($_REQUEST["faqId"]);
$smarty->assign('faqId', $_REQUEST["faqId"]);
$faq_info = $tikilib->get_faq($_REQUEST["faqId"]);
$smarty->assign('faq_info', $faq_info);
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'position_asc,questionId_asc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign('find', $find);
$channels = $faqlib->list_faq_questions($_REQUEST["faqId"], 0, -1, 'position_asc,questionId_asc', $find);
$smarty->assign_by_ref('channels', $channels["data"]);
if (isset($_REQUEST["sugg"])) {
	check_ticket('view-faq');
	if ($tiki_p_suggest_faq == 'y') {
		if (empty($user) && $prefs['feature_antibot'] == 'y' && !$captchalib->validate()) {
			$error = $captchalib->getErrors();
			$smarty->assign('error', $error);
			// Save the pending question and answer if antibot code is wrong
			$smarty->assign('pendingquestion', $_REQUEST["suggested_question"]);
			$smarty->assign('pendinganswer', $_REQUEST["suggested_answer"]);
		} else {
			if (!empty($_REQUEST["suggested_question"])) {
				$faqlib->add_suggested_faq_question($_REQUEST["faqId"], $_REQUEST["suggested_question"], $_REQUEST["suggested_answer"], $user);
			} else {
				$error = tra('You must suggest a question; please try again.');
				$smarty->assign('error', $error);
				// Save the pending answer if question is empty
				$smarty->assign('pendinganswer', $_REQUEST["suggested_answer"]);
			}
		}
	}
}
$suggested = $faqlib->list_suggested_questions(0, -1, 'created_desc', '', $_REQUEST["faqId"]);
$smarty->assign_by_ref('suggested', $suggested["data"]);
$smarty->assign('suggested_cant', count($suggested["data"]));
if ($prefs['feature_faq_comments'] == 'y') {
	$comments_per_page = $prefs['faq_comments_per_page'];
	$thread_sort_mode = $prefs['faq_comments_default_ordering'];
	$comments_vars = array(
		'faqId'
	);
	$comments_prefix_var = 'faq:';
	$comments_object_var = 'faqId';
	include_once ("comments.php");
}
include_once ('tiki-section_options.php');
if ($prefs['feature_theme_control'] == 'y') {
	$cat_type = 'faq';
	$cat_objid = $_REQUEST["faqId"];
	include ('tiki-tc.php');
}
ask_ticket('view-faq');
// Display the template
$smarty->assign('mid', 'tiki-view_faq.tpl');
if (isset($_REQUEST['print'])) {
	$smarty->display('tiki-print.tpl');
	$smarty->assign('print', 'y');
} else {
	$smarty->display("tiki.tpl");
}
