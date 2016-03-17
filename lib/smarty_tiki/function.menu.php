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

/* params
 * - link_on_section
 * - css = use suckerfish menu
 * - type = vert|horiz
 * - id = menu ID (mandatory)
 * - translate = y|n , n means no option translation (default y)
 * - menu_cookie=y|n (default y) n, it will automatically open the submenu the url is in
 * - sectionLevel: displays from this level only
 * - toLevel : displays to this level only
 * - drilldown ??
 * - bootstrap : navbar|basic (equates to horiz or vert in old menus)
 * - setSelected=y|n (default=y) processes all menu items to show currently selected item, also sets open states, sectionLevel, toLevel etc
 * 								so menu_cookie, sectionLevel and toLevel will be ignored if this is set to n
 */
function smarty_function_menu($params, $smarty)
{
	global $prefs;
	$headerlib = TikiLib::lib('header');

	$default = array('css' => 'y');
	if (isset($params['params'])) {
		$params = array_merge($params, $params['params']);
		unset($params['params']);
	}
	$params = array_merge($default, $params);
	extract($params, EXTR_SKIP);

	if ($prefs['javascript_enabled'] !== 'y') {
		$params['css'] = 'y';
		$params['bootstrap'] = 'n';
		$params['type'] = 'horiz';
	}

	if (empty($link_on_section) || $link_on_section == 'y') {
		$smarty->assign('link_on_section', 'y');
	} else {
		 $smarty->assign('link_on_section', 'n');
	}
	if (empty($translate)) {
		$translate = 'y';
	}
	$smarty->assignByRef('translate', $translate);
	if (empty($menu_cookie)) {
		$menu_cookie = 'y';
	}
	$smarty->assignByRef('menu_cookie', $menu_cookie);
	if (empty($drilldown)) {
		$drilldown = 'n';
	}
	if ($params['css'] !== 'n' && $prefs['feature_cssmenus'] == 'y') {
		static $idCssmenu = 0;
		if (empty($params['type'])) {
			$params['type'] = 'vert';
		}
		$headerlib->add_jsfile('lib/menubuilder/menu.js');
		$tpl = 'tiki-user_cssmenu.tpl';
		$smarty->assign('menu_type', $params['type']);
		if (! isset($css_id)) {//adding $css_id parameter to customize menu id and prevent automatic id renaming when a menu is removed
			$smarty->assign('idCssmenu', $idCssmenu++);
		} else {
			$smarty->assign('idCssmenu', $css_id);
		}
		if ($drilldown == 'y') {
			$smarty->assign('drilldownmenu', $drilldown);
		}
	} else {
		$tpl = 'tiki-user_menu.tpl';
	}

	list($menu_info, $channels) = get_menu_with_selections($params);
	$smarty->assign('menu_channels', $channels['data']);
	$smarty->assign('menu_info', $menu_info);

	if (isset($params['bootstrap']) && $params['bootstrap'] !== 'n') {
		$structured = array();
		$activeSection = null;
		foreach ($channels['data'] as $element) {
			if ($element['type'] == 's') {
				if ($activeSection) {
					$structured[] = $activeSection;
				}

				$activeSection = $element;
				$activeSection['children'] = array();

			} elseif ($element['type'] == 'o' || // bootstrap menus don't support more than one item level so include 1, 2 and 3 as options
					$element['type'] == '1' || $element['type'] == '2' || $element['type'] == '3') {

				if ($activeSection) {
					$activeSection['children'][] = $element;
				} else {
					$structured[] = $element;
				}
			} elseif($element['type'] == '-') {
				if ($activeSection) {
					$structured[] = $activeSection;
				}
				$activeSection = null;
			}
		}

		if ($activeSection) {
			$structured[] = $activeSection;
		}
		$smarty->assign('list', $structured);
		switch ($params['bootstrap']) {
		case 'navbar':
			return $smarty->fetch('bootstrap_menu_navbar.tpl');
		case  'y':
			if(isset($params['type']) && $params['type'] == "horiz"){
				return $smarty->fetch('bootstrap_menu_navbar.tpl');
			}else{
				return $smarty->fetch('bootstrap_menu.tpl');
			}
		default:
			return $smarty->fetch('bootstrap_menu.tpl');
		}
	}

	$data = $smarty->fetch($tpl);
	$menulib = TikiLib::lib('menu');
	return $menulib->clean_menu_html($data);
}

function compare_menu_options($a, $b)
{
	return strcmp(tra($a['name']), tra($b['name']));
}

function get_menu_with_selections($params)
{
	global $user, $prefs;
	$tikilib = TikiLib::lib('tiki');
	$menulib = TikiLib::lib('menu');
	$cachelib = TikiLib::lib('cache');
	$cacheName = isset($prefs['mylevel']) ? $prefs['mylevel'] : 0;
	$cacheName .= '_'.$prefs['language'].'_'.md5(implode("\n", $tikilib->get_user_groups($user)));

	extract($params, EXTR_SKIP);

	if (isset($structureId)) {
		$cacheType = 'structure_'.$structureId . '_';
	} else {
		$cacheType = 'menu_'. $id .'_';
	}

	if ( $cdata = $cachelib->getSerialized($cacheName, $cacheType) ) {
		list($menu_info, $channels) = $cdata;
	} elseif (!empty($structureId)) {
		$structlib = TikiLib::lib('struct');

		if (!is_numeric($structureId)) {
			$structureId = $structlib->get_struct_ref_id($structureId);
		}

		$channels = $structlib->build_subtree_toc($structureId);
		$structure_info =  $structlib->s_get_page_info($structureId);
		$channels = $structlib->to_menu($channels, $structure_info['pageName'], 0, 0, $params);
		$menu_info = array('type'=>'d', 'menuId'=> $structureId, 'structure' => 'y');
	} else if (!empty($id)) {
		$menu_info = $menulib->get_menu($id);
		$channels = $menulib->list_menu_options($id, 0, -1, 'position_asc', '', '', isset($prefs['mylevel'])?$prefs['mylevel']:0);
		$channels = $menulib->sort_menu_options($channels);
	} else {
		return '<span class="alert-warning">menu function: Menu or Structure ID not set</span>';
	}
	if (strpos($_SERVER['SCRIPT_NAME'], 'tiki-register') === false) {
		$cachelib->cacheItem($cacheName, serialize(array($menu_info, $channels)), $cacheType);
	}
	if (!isset($setSelected) || $setSelected !== 'n') {
		$channels = $menulib->setSelected($channels, isset($sectionLevel) ? $sectionLevel : '', isset($toLevel) ? $toLevel : '', $params);
	}

	foreach ($channels['data'] as & $item) {
		if (!empty($menu_info['parse']) && $menu_info['parse'] === 'y') {
			if (TikiLib::lib('parser')->contains_html_block($item['name'])) {
				$item['block'] = true;
			} else {
				$item['block'] = false;
			}
			$item['name'] = preg_replace('/(.*)\n$/', '$1', $item['name']);	// parser adds a newline to everything
		}
	}

	return array($menu_info, $channels);
}
