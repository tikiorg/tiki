<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');
$menulib = TikiLib::lib('menu');
$access->check_permission(['tiki_p_edit_menu_option']);
if (! isset($_REQUEST["menuId"])) {
	$smarty->assign('msg', tra("No menu indicated"));
	$smarty->display("error.tpl");
	die;
}
$auto_query_args = [
	'menuId',
	'preview_css',
	'preview_type',
];

$smarty->assign('menuId', $_REQUEST["menuId"]);
$editable_menu_info = $menulib->get_menu($_REQUEST["menuId"]);
$smarty->assign('editable_menu_info', $editable_menu_info);


$smarty->assign('preview_type', isset($_REQUEST['preview_type']) && $_REQUEST['preview_type'] === 'horiz' ? 'horiz' : 'vert');
$smarty->assign('preview_css', isset($_REQUEST['preview_css']) && $_REQUEST['preview_css'] === 'On' ? 'y' : 'n');

$headerlib->add_js('var permNames = ' . json_encode(TikiLib::lib('user')->get_permission_names_for('all')) . ';');
$feature_prefs = [];
foreach ($prefs as $k => $v) {	// attempt to filter out non-feature prefs (still finds 133!)
	if (strpos($k, 'feature') !== false && preg_match_all('/_/m', $k, $m) === 1) {
		$feature_prefs[] = $k;
	}
}
$headerlib->add_js('var prefNames = ' . json_encode($feature_prefs) . ';');

$options = $menulib->list_menu_options($_REQUEST["menuId"], 0, -1, 'position_asc', '', true, 0, true);
$options = $menulib->prepare_options_for_editing($options);
$smarty->assign_by_ref('cant_pages', $options["cant"]);
$smarty->assign_by_ref('options', $options["data"]);
if (isset($info['groupname']) && ! is_array($info['groupname'])) {
	$info['groupname'] = explode(',', $info['groupname']);
}
$all_groups = $userlib->list_all_groups();
if (is_array($all_groups)) {
	foreach ($all_groups as $g) {
		$option_groups[$g] = (is_array($info['groupname']) && in_array($g, $info['groupname'])) ? 'selected="selected"' : '';
	}
}
$smarty->assign_by_ref('option_groups', $option_groups);

$access->checkAuthenticity();

$headerlib->add_jsfile('lib/menubuilder/tiki-admin_menu_options.js')
	->add_jsfile('vendor_bundled/vendor/jquery/plugins/nestedsortable/jquery.ui.nestedSortable.js');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template
$smarty->assign('mid', 'tiki-admin_menu_options.tpl');
$smarty->display("tiki.tpl");
