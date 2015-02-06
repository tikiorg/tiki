<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @return array
 */
function module_menupage_info()
{
	return array(
		'name' => tra('Menu Page'),
		'description' => tra('Displays a Wiki page.'),
		'prefs' => array('feature_wiki'),
		'params' => array(
			'pagemenu' => array(
				'name' => tra('Page'),
				'description' => tra('Page to display in the menu. Example value: HomePage.'),
				'filter' => 'pagename',
				'required' => true,
				'profile_reference' => 'wiki_page',
			),
			'use_namespace' => array(
				'name' => tra('Use default namespace'),
				'description' => tra('Prepend the default namespace to the page name for localized menus per workspace (1/0)'),
				'filter' => 'int',
				'default' => 0,
				'required' => false,
			),
			'menu_id' => array(
				'name' => tra('DOM #id'),
				'description' => tra('Id of the menu in the DOM'),
				'filter' => 'text',
				'required' => false,
			),
			'menu_class' => array(
				'name' => tra('CSS class'),
				'description' => tra('Class of the menu container'),
				'filter' => 'text',
				'required' => false,
			),
			'menu_type' => array(
				'name' => tra('Menu style'),
				'description' => tra('Display the page as a menu (horiz / vert)'),
				'filter' => 'alpha',
				'required' => false,
			),
		)
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_menupage($mod_reference, $module_params)
{
	if (!empty($module_params['pagemenu'])) {
		$wikilib = TikiLib::lib('wiki');
		$menulib = TikiLib::lib('menu');
		$smarty = TikiLib::lib('smarty');

		$pagemenu = $module_params['pagemenu'];

		if (! empty($module_params['use_namespace'])) {
			$pagemenu = $wikilib->include_default_namespace($pagemenu);
		}

		$perms = Perms::get(array('object' => $pagemenu, 'type' => 'wiki page'));

		if ($perms->view) {
			$content = $wikilib->get_parse($pagemenu, $dummy, true);
		} else {
			$content = '<label class="alert-warning">' . tra("You are not logged in") . '</label>';
		}

		if (! empty($content) && ! empty($module_params['menu_type']) && in_array($module_params['menu_type'], array('horiz', 'vert'))) {
			$class = 'cssmenu_' . $module_params['menu_type'];
			$content = preg_replace_callback(
				'/<(ul|ol|li)([^>]*)>/Umi',
				function ($matches) use ($class) {
					if ($matches[1] == 'li') {
						$class = 'menuSection';
					}
					return "<{$matches[1]} class=\"$class\" {$matches[2]}>";
				},
				$content
			);
			$content = $menulib->clean_menu_html($content);
		}

		$smarty->assign('tpl_module_title', $wikilib->get_without_namespace($pagemenu));
		$smarty->assign_by_ref('contentmenu', $content);
	}
}
