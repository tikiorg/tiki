<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_breadcrumbs_info()
{
	return array(
		'name' => tra('Breadcrumbs'),
		'description' => tra('A hierarchy of where you are. Ex.: Home > Section1 > Subsection C.'),
		'prefs' => array('feature_breadcrumbs'),
		'params' => array(
			'label' => array(
				'name' => tra('Label'),
				'description' => tra('Label preceding the crumbs. Default "Location : '),
				'filter' => 'text',
				'default' => 'Location : ',
			),
			'menuId' => array(
				'name' => tra('Menu Id'),
				'description' => tra('Menu to take the crumb trail from.'),
				'filter' => 'int',
				'default' => 0,
			),
			'showFirst' => array(
				'name' => tra('Show Site Crumb'),
				'description' => 'y|n ' . tra('Display the first crumb, usually the site, when using menu crubms.'),
				'filter' => 'alpha',
				'default' => 'y',
			),
			'showLast' => array(
				'name' => tra('Show Page Crumb'),
				'description' => 'y|n ' . tra('Display the last crumb, usually the page, when using menu crubms.'),
				'filter' => 'alpha',
				'default' => 'y',
			),
		),
	);
}

function module_breadcrumbs($mod_reference, $module_params)
{
	global $prefs, $smarty, $crumbs;

	if (!isset($module_params['label'])) {
		if ($prefs['feature_siteloclabel'] === 'y') {
			$module_params['label'] = 'Location : ';
		}
	}

	if (!empty($module_params['menuId'])) {
		include_once('lib/breadcrumblib.php');

		$newCrumbs = breadcrumb_buildMenuCrumbs($crumbs, $module_params['menuId']);
		if ($newCrumbs !== $crumbs) {
			$crumbs = $newCrumbs;
			if (!empty($module_params['showFirst']) && $module_params['showFirst'] === 'n') {
				array_shift($crumbs);
			}
			if (!empty($module_params['showLast']) && $module_params['showLast'] === 'n') {
				array_pop($crumbs);
			}
			$smarty->assign('trail', $crumbs);
		}
	}

	$smarty->assign('module_params', $module_params);
}
