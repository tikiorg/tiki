<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-theme_control_sections.php,v 1.6 2004-03-28 07:32:23 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/themecontrol/tcontrol.php');
include_once ('lib/categories/categlib.php');

if ($feature_theme_control != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_theme_control");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

$styles = array();
$h = opendir("styles/");

while ($file = readdir($h)) {
	if (strstr($file, "css")) {
		$styles[] = $file;
	}
}

closedir ($h);
$smarty->assign_by_ref('styles', $styles);

if (isset($_REQUEST['assign'])) {
	check_ticket('tc-sections');
	$tcontrollib->tc_assign_section($_REQUEST['section'], $_REQUEST['theme']);
}

if (isset($_REQUEST["delete"])) {
	check_ticket('tc-sections');
	foreach (array_keys($_REQUEST["sec"])as $sec) {
		$tcontrollib->tc_remove_section($sec);
	}
}

$channels = $tcontrollib->tc_list_sections(0, -1, 'section_asc', '');
$smarty->assign_by_ref('channels', $channels["data"]);

$sections = array(
	'wiki',
	'galleries',
	'file_galleries',
	'cms',
	'blogs',
	'forums',
	'chat',
	'categories',
	'games',
	'faqs',
	'html_pages',
	'quizzes',
	'surveys',
	'webmail',
	'trackers',
	'featured_links',
	'directory',
	'user_messages',
	'newsreader',
	'mytiki'
);

sort ($sections);
$smarty->assign('sections', $sections);

ask_ticket('tc-sections');

// Display the template
$smarty->assign('mid', 'tiki-theme_control_sections.tpl');
$smarty->display("tiki.tpl");

?>
