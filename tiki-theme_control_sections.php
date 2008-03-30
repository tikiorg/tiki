<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-theme_control_sections.php,v 1.14 2007-10-12 07:55:32 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/themecontrol/tcontrol.php');
include_once ('lib/categories/categlib.php');

if ($prefs['feature_theme_control'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_theme_control");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

$list_styles = $tikilib->list_styles();
$smarty->assign_by_ref('styles', $list_styles);

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

$smarty->assign('sections', $sections_enabled);

ask_ticket('tc-sections');

// Display the template
$smarty->assign('mid', 'tiki-theme_control_sections.tpl');
$smarty->display("tiki.tpl");

?>
