<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_layout.php,v 1.17 2006-11-23 18:33:25 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

if ($layout_section != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": layout_section");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}


foreach ($sections_enabled as $section=>$data) {
	if (isset($_REQUEST["${section}_layout"])) {
		check_ticket('admin-layout');
		if (isset($_REQUEST["${section}_left_column"]) && $_REQUEST["${section}_left_column"] == "on") {
			$tikilib->set_preference("${section}_left_column", 'y');
			$smarty->assign("${section}_left_column", 'y');
		} else {
			$tikilib->set_preference("${section}_left_column", 'n');
			$smarty->assign("${section}_left_column", 'n');
		}

		if (isset($_REQUEST["${section}_right_column"]) && $_REQUEST["${section}_right_column"] == "on") {
			$tikilib->set_preference("${section}_right_column", 'y');
			$smarty->assign("${section}_right_column", 'y');
		} else {
			$tikilib->set_preference("${section}_right_column", 'n');
			$smarty->assign("${section}_right_column", 'n');
		}

		if (isset($_REQUEST["${section}_top_bar"]) && $_REQUEST["${section}_top_bar"] == "on") {
			$tikilib->set_preference("${section}_top_bar", 'y');
			$smarty->assign("${section}_top_bar", 'y');
		} else {
			$tikilib->set_preference("${section}_top_bar", 'n');
			$smarty->assign("${section}_top_bar", 'n');
		}

		if (isset($_REQUEST["${section}_bot_bar"]) && $_REQUEST["${section}_bot_bar"] == "on") {
			$tikilib->set_preference("${section}_bot_bar", 'y');
			$smarty->assign("${section}_bot_bar", 'y');
		} else {
			$tikilib->set_preference("${section}_bot_bar", 'n');
			$smarty->assign("${section}_bot_bar", 'n');
		}
	}
}

$sections_smt = array();
$temp_max = count($sections_enabled);
foreach ($sections_enabled as $sec=>$dat) {
	$aux["name"] = $sec;
	$aux["left_column"] = $tikilib->get_preference("${sec}_left_column", 'y');
	$aux["right_column"] = $tikilib->get_preference("${sec}_right_column", 'y');
	$aux["top_bar"] = $tikilib->get_preference("${sec}_top_bar", 'y');
	$aux["bot_bar"] = $tikilib->get_preference("${sec}_bot_bar", 'y');
	$sections_smt[] = $aux;
}
$smarty->assign('sections', $sections_smt);

ask_ticket('admin-layout');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-admin_layout.tpl');
$smarty->display("tiki.tpl");

?>
