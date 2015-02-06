<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$themecontrollib = TikiLib::lib('themecontrol');
$categlib = TikiLib::lib('categ');
$themelib = TikiLib::lib('theme');

$access->check_feature('feature_theme_control', '', 'look');
$access->check_permission('tiki_p_admin');

$auto_query_args = array('find', 'sort_mode', 'offset', 'theme', 'theme_option', 'section');
$smarty->assign('a_section', isset($_REQUEST['section']) ? $_REQUEST['section'] : '');

$themes = $themelib->list_themes_and_options();
$smarty->assign('themes', $themes);

if (isset($_REQUEST['assign'])) {
	check_ticket('tc-sections');
	$themecontrollib->tc_assign_section($_REQUEST['section'], $_REQUEST['theme']);
}
if (isset($_REQUEST['delete'])) {
	check_ticket('tc-sections');
	foreach (array_keys($_REQUEST["sec"]) as $sec) {
		$themecontrollib->tc_remove_section($sec);
	}
}
$channels = $themecontrollib->tc_list_sections(0, -1, 'section_asc', '');
$smarty->assign_by_ref('channels', $channels["data"]);
$smarty->assign('sections', $sections_enabled);
ask_ticket('tc-sections');
$smarty->assign('mid', 'tiki-theme_control_sections.tpl');
$smarty->display('tiki.tpl');
