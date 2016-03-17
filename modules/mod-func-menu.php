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
function module_menu_info()
{
	return array(
		'name' => tra('Menu'),
		'description' => tra('Displays a menu or a structure as a menu.'),
		'params' => array(
			'id' => array(
				'name' => tra('Menu'),
				'description' => tra('Identifier of a menu (from tiki-admin_menus.php)'),
				'filter' => 'int',
				'profile_reference' => 'menu',
			),
			'structureId' => array(
				'name' => tra('Structure'),
				'description' => tra('Identifier of a structure of wiki pages (name or number from tiki-admin_structures.php)'),
				'filter' => 'text',
				'profile_reference' => 'structure',
			),
			'type' => array(
				'name' => tra('Type'),
				'description' => tra('Direction for menu: horiz or vert (default vert)'),
				'filter' => 'text',
			),
			'bootstrap' => array(
				'name' => tra('Use Bootstrap menus'),
				'description' => tra('').' ( y / n )',
				'default' => 'y',
			),
			'navbar_toggle' => array(
				'name' => tra('Show Navbar Toggle Button'),
				'description' => tra('Used in Bootstrap navbar menus when viewport is too narrow for menu items').' ( y / n )',
				'default' => 'y',
			),
			'navbar_brand' => array(
				'name' => tra('The URL of the navbar brand(logo)'),
				'description' => tra('Used in Bootstrap navbar menus, if there is a Brand logo to be attached to the menu').' ( y / n )',
				'default' => '',
			),
			'navbar_class' => array(
				'name' => tra('CSS class for the menu nav element'),
				'description' => tra(''),
				'default' => 'navbar navbar-default',
			),
			'css' => array(
				'name' => tra('CSS/Superfish'),
				'description' => tra('Use CSS Superfish menu (if bootstrap = n). y|n (default y)'),
				'filter' => 'alpha',
			),
			'menu_id' => array(
				'name' => tra('DOM #id'),
				'description' => tra('Id of the menu in the DOM'),
			),
			'menu_class' => array(
				'name' => tra('CSS class'),
				'description' => tra('Class of the menu container'),
				'filter' => 'text',
			),
			'sectionLevel' => array(
				'name' => tra('Limit low visibles levels'),
				'description' => tra('All the submenus beginning at this level will be displayed if the url matches one of the option of this level or above or below.'),
				'filter' => 'int',
			),
			'toLevel' => array(
				'name' => tra('Limit top visible levels'),
				'description' => tra('Do not display options higher than this level.'),
				'filter' => 'int',
			),
			'link_on_section' => array(
				'name' => tra('Link on Section'),
				'description' => tra('Create links on menu sections') . ' ' . tra('(y/n default y)'),
				'filter' => 'alpha',
			),
			'translate' => array(
				'name' => tra('Translate'),
				'description' => tra('Translate labels') . ' ' . tra('(y/n default y)'),
				'filter' => 'alpha',
			),
			'menu_cookie' => array(
				'name' => tra('Menu Cookie'),
				'description' => tra('Open the menu to show current option if possible') . ' ' . tra('(y/n default y)'),
				'filter' => 'alpha',
			),
			'show_namespace' => array(
				'name' => tra('Show Namespace'),
				'description' => tra('Show namespace prefix in page names').' ( y / n )',	// Do not translate y/n	
				'default' => 'y'
			),
			'setSelected' => array(
				'name' => tra('Set Selected'),
				'description' => tra('Process all menu items to show currently selected item and other dynamic states. Useful when disabled on very large menus where performance becomes an issue.').' ( y / n )',
				'default' => 'y'
			),
		)
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_menu($mod_reference, $module_params)
{
	$smarty = TikiLib::lib('smarty');
	$smarty->assign('module_error', '');
	if (empty($module_params['id']) && empty($module_params['structureId'])) {
		$smarty->assign('module_error', tr('One of these parameters has to be set:') . ' ' . tr('Menu') . ', ' . tr('Structure') . '.');
	}
	if (!empty($module_params['structureId'])) {
		$structlib = TikiLib::lib('struct');

		if (empty($module_params['title'])) {
			$smarty->assign('tpl_module_title', $module_params['structureId']);
		}
	}
	$smarty->assign('module_type', empty($module_params['css']) || $module_params['css'] === 'y' ? 'cssmenu' : 'menu');
	$show_namespace = isset($module_params['show_namespace']) ? $module_params['show_namespace'] : 'y';
	$smarty->assign('show_namespace',$show_namespace);
}
