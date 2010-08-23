<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/themecontrol/tcontrol.php');
include_once ('lib/categories/categlib.php');

$access->check_feature('feature_theme_control', '', 'look');
$access->check_permission('tiki_p_admin');

$auto_query_args = array('find', 'sort_mode', 'offset', 'theme', 'theme-option', 'section');
$smarty->assign('a_section', isset($_REQUEST['section']) ? $_REQUEST['section'] : '');

$tcontrollib->setup_theme_menus();

if (isset($_REQUEST['assign'])) {
	check_ticket('tc-sections');
	$tcontrollib->tc_assign_section($_REQUEST['section'], $_REQUEST['theme'], isset($_REQUEST['theme-option']) ? $_REQUEST['theme-option'] : '');
}
if (isset($_REQUEST['delete'])) {
	check_ticket('tc-sections');
	foreach(array_keys($_REQUEST["sec"]) as $sec) {
		$tcontrollib->tc_remove_section($sec);
	}
}
$channels = $tcontrollib->tc_list_sections(0, -1, 'section_asc', '');
$smarty->assign_by_ref('channels', $channels["data"]);
$smarty->assign('sections', $sections_enabled);
ask_ticket('tc-sections');
$smarty->assign('mid', 'tiki-theme_control_sections.tpl');
$smarty->display('tiki.tpl');
