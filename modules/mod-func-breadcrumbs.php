<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @return array
 */
function module_breadcrumbs_info()
{
	return array(
		'name' => tra('Breadcrumbs'),
		'description' => tra('A hierarchy of where you are. Ex.: Home > Section1 > Subsection C.'),
		'prefs' => array('feature_breadcrumbs'),
		'params' => array(
			'label' => array(
				'name' => tra('Label'),
				'description' => tra('Label preceding the crumbs.'),
				'filter' => 'text',
				'default' => 'Location : ',
			),
			'menuId' => array(
				'name' => tra('Menu Id'),
				'description' => tra('Menu to take the crumb trail from.'),
				'filter' => 'int',
				'default' => 0,
				'profile_reference' => 'menu',
			),
			'menuStartLevel' => array(
				'name' => tra('Menu Start Level'),
				'description' => tra('Lowest level of the menu to display.'),
				'filter' => 'int',
				'default' => null,
			),
			'menuStopLevel' => array(
				'name' => tra('Menu Stop Level'),
				'description' => tra('Highest level of the menu to display.'),
				'filter' => 'int',
				'default' => null,
			),
			'showFirst' => array(
				'name' => tra('Show Site Crumb'),
				'description' => tra('Display the first crumb, usually the site, when using menu crumbs.'),
				'filter' => 'alpha',
				'default' => 'y',
			),
			'showLast' => array(
				'name' => tra('Show Page Crumb'),
				'description' => tra('Display the last crumb, usually the page, when using menu crumbs.'),
				'filter' => 'alpha',
				'default' => 'y',
			),
			'showLinks' => array(
				'name' => tra('Show Crumb Links'),
				'description' => tra('Display links on the crumbs.'),
				'filter' => 'alpha',
				'default' => 'y',
			),
		),
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_breadcrumbs($mod_reference, $module_params)
{
	global $prefs, $crumbs;
	$smarty = TikiLib::lib('smarty');
	if (!isset($module_params['label'])) {
		if ($prefs['feature_siteloclabel'] === 'y') {
			$module_params['label'] = 'Location : ';
		}
	}
	$binfo = module_breadcrumbs_info();
	$defaults = array();
	foreach ($binfo['params'] as $k => $v) {
		$defaults[$k] = $v['default'];
	}
	$module_params = array_merge($defaults, $module_params);

	if (!empty($module_params['menuId'])) {
		include_once('lib/breadcrumblib.php');

		$newCrumbs = breadcrumb_buildMenuCrumbs($crumbs, $module_params['menuId'], $module_params['menuStartLevel'], $module_params['menuStopLevel']);
		if ($newCrumbs !== $crumbs) {
			$crumbs = $newCrumbs;
		}
	}

	if ($module_params['showFirst'] === 'n') {
		$crumbs[0]->hidden = true;
	}
	if ($module_params['showLast'] === 'n' && ($module_params['showFirst'] === 'n' || count($crumbs) > 1)) {
		$crumbs[count($crumbs) - 1]->hidden = true;
	}

	$hide = true;
	foreach ($crumbs as $crumb) {
		if (!$crumb->hidden) {
			$hide = false;
		}
	}
	$smarty->assign('crumbs_all_hidden', $hide);

	$smarty->assign_by_ref('trail', $crumbs);

	$smarty->assign('module_params', $module_params);
}
