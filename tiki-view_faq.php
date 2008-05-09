<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-view_faq.php,v 1.24.2.3 2008-03-12 13:04:41 ricks99 Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'faqs';
require_once ('tiki-setup.php');

include_once ('lib/faqs/faqlib.php');

if ($prefs['feature_categories'] == 'y') {
	global $categlib;
	if (!is_object($categlib)) {
		include_once('lib/categories/categlib.php');
	}
}

if ($prefs['feature_faqs'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_faqs");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_view_faqs != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["faqId"])) {
	$smarty->assign('msg', tra("No faq indicated"));

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin != 'y' && $prefs['feature_categories'] == 'y') {
	$perms_array = $categlib->get_object_categories_perms($user, 'faq', $_REQUEST['faqId']);
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
	if (empty($user) && $prefs['feature_antibot'] == 'y' && (!isset($_SESSION['random_number']) || $_SESSION['random_number'] != $_REQUEST['antibotcode'])) {
 $error = tra('You have mistyped the anti-bot verification code; please try again.');
 $smarty->assign('error', $error);
// Save the pending question and answer if antibot code is wrong
 $smarty->assign('pendingquestion', $_REQUEST["suggested_question"]);
 $smarty->assign('pendinganswer', $_REQUEST["suggested_answer"]);
 } else {
  if (!empty($_REQUEST["suggested_question"]))
  	{ 
	   		$faqlib->add_suggested_faq_question($_REQUEST["faqId"], $_REQUEST["suggested_question"], $_REQUEST["suggested_answer"],
			$user);
} else {
 $error = tra('You must suggest a question; please try again.');
 $smarty->assign('error', $error);
// Save the pending answer if question is empty
 $smarty->assign('pendinganswer', $_REQUEST["suggested_answer"]);
 	}}}
}

$suggested = $faqlib->list_suggested_questions(0, -1, 'created_desc', '', $_REQUEST["faqId"]);
$smarty->assign_by_ref('suggested', $suggested["data"]);
$smarty->assign('suggested_cant', count($suggested["data"]));

if ($prefs['feature_faq_comments'] == 'y') {
	$comments_per_page = $prefs['faq_comments_per_page'];

	$thread_sort_mode = $prefs['faq_comments_default_ordering'];
	$comments_vars = array('faqId');
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
$smarty->display("tiki.tpl");

?>
