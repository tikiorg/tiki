<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$access->check_feature('layout_section');
$access->check_permission('tiki_p_admin');

foreach($sections_enabled as $section => $data) {
	if (isset($_REQUEST["${section}_layout"])) {
		check_ticket('admin-layout');
		if (isset($_REQUEST["${section}_left_column"]) && $_REQUEST["${section}_left_column"] == "on") {
			$tikilib->set_preference("${section}_left_column", 'y');
		} else {
			$tikilib->set_preference("${section}_left_column", 'n');
		}
		if (isset($_REQUEST["${section}_right_column"]) && $_REQUEST["${section}_right_column"] == "on") {
			$tikilib->set_preference("${section}_right_column", 'y');
		} else {
			$tikilib->set_preference("${section}_right_column", 'n');
		}
		if (isset($_REQUEST["${section}_top_bar"]) && $_REQUEST["${section}_top_bar"] == "on") {
			$tikilib->set_preference("${section}_top_bar", 'y');
		} else {
			$tikilib->set_preference("${section}_top_bar", 'n');
		}
		if (isset($_REQUEST["${section}_bot_bar"]) && $_REQUEST["${section}_bot_bar"] == "on") {
			$tikilib->set_preference("${section}_bot_bar", 'y');
		} else {
			$tikilib->set_preference("${section}_bot_bar", 'n');
		}
	}
}
$sections_smt = array();
$temp_max = count($sections_enabled);
$needed_prefs = array();
$needed_elements = array(
	'left_column',
	'right_column',
	'top_bar',
	'bot_bar'
);
foreach($sections_enabled as $sec => $dat) foreach($needed_elements as $elmt) $needed_prefs[$sec . '_' . $elmt] = 'y';
$tikilib->get_preferences($needed_elements, true, true);
foreach($sections_enabled as $sec => $dat) {
	$aux["name"] = $sec;
	foreach($needed_elements as $elmt) $aux[$elmt] = $ {
		$sec . '_' . $elmt
	};
	$sections_smt[] = $aux;
}
$smarty->assign('sections', $sections_smt);
ask_ticket('admin-layout');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template
$smarty->assign('mid', 'tiki-admin_layout.tpl');
$smarty->display("tiki.tpl");
