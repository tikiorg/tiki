<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiPhplayers extends TikiLib
{
	/* Build the input to the phplayers lib for a category tree  */
	function mkCatEntry($categId, $indent="", $back, $categories, $urlEnd, $tpl='') {
		global $smarty, $prefs, $categlib;
		include_once('lib/smarty_tiki/modifier.tr_if.php');
		$kids = array();
		foreach ($categories as $cat) {
			if ($cat['parentId'] == $categId) {
				$kids[] = $cat;
			}
		}

		if (count($kids)) {
			$total = 0;
			foreach ($kids as $k) {
				$k['name'] = $categlib->get_category_name($k['categId']);
				$k['name'] = smarty_modifier_tr_if($k['name']);
				list($subTree, $count) = $this->mkCatEntry($k['categId'],".$indent",'', $categories, $urlEnd, $tpl);
				$count += $k['objects'];
				$total += $count;
				$back .= "$indent|";
				if ($tpl != '') {
					$smarty->assign('categoryName', $k['name']);
					$smarty->assign('categoryLocal', $k['objects']);
					$smarty->assign('categoryTotal', $count);
					$back .= $smarty->fetchLang($prefs['language'], $tpl);
				} else 
					$back .= $k['name'];
				$back .= '|tiki-browse_categories.php?parentId='.$k['categId'].$urlEnd;
				$back .= $subTree;
			}
			return array($back, $total);
		} elseif( $categId == 0 ) {
			$roots = $categlib->findRoots( $categories );
			$out = '';
			$count = 0;

			foreach( $roots as $id ) {
				list($subTree, $subCount) = $this->mkCatEntry($id,'','', $categories, $urlEnd, $tpl);
				$out .= $subTree;
				$count += $subCount;
			}

			return array( $out, $count );
		} else {
			return array('', 0);
		}
	}

	function mkMenuEntry($idMenu, &$curOption, $sectionLevel='', $translate='y', &$use_items_icons = null) {
		global $tikilib, $wikilib, $mylevel, $prefs;
		global $menulib; include_once('lib/menubuilder/menulib.php');
		$menu_info = $tikilib->get_menu($idMenu);
		$use_items_icons = ( $prefs['menus_items_icons'] == 'y' ) && ( $menu_info['use_items_icons'] == 'y' );

		$channels = $tikilib->list_menu_options($idMenu, 0, -1, 'position_asc', '','',$mylevel);
		$channels = $menulib->setSelected($channels, $sectionLevel);
		if (empty($channels['data'])) {
			return;
		}
		$indented = '';
		$res = '';
		$curOption = 0;
		$url = urldecode($_SERVER['REQUEST_URI']);
		global $wikilib; include_once('lib/wiki/wikilib.php');
		$url = str_replace('tiki-editpage.php', 'tiki-index.php', $url);
		$homePage = strtolower($wikilib->get_default_wiki_page());
		if (preg_match('/.*tiki.index.php$/', $url)) {
			$url .= "?page=$homePage";
		} elseif (preg_match('/tiki-index.php/', $url)) {
			$url = ($url);
		}
		$realKey = 0;
		$level = 0;
		$display_icon = false;
		foreach ($channels['data'] as $key=>$cd) {
			if ($translate != 'n') {
				$cd["name"] = tra($cd["name"]);
			}
			if ($cd['type'] == 'o') {
				$res .= $indented;
			} elseif ($cd['type'] == 's' or $cd['type'] == 'r') {
				$indented = '.';
				$level = 1;
			} elseif ($cd['type'] == '-') {
				$indented = substr($indented, 1);
				--$level;
				continue;
			} else {
				$indented = str_pad('', $cd['type'], '.');
				$level = $cd['type'] + 1;
				$res .= $indented;
				$indented .= '.';
			}
			++$realKey;
			if (!empty($cd['url']) && empty($curOption)) {
				if ($cd['url'] == 'tiki-index.php') {
					$cd['url'] .= "?page=$homePage";
				}
				if (preg_match('/tiki-index.php/', $cd['url'])) {
					$cd['url'] = ($cd['url']);
				}
				if (($pos = strpos($url, $cd['url'])) !== false && ($pos == 0 || $url[$pos -1] == '/' || $url[$pos - 1] == '\\' || $url[$pos-1] == '=')) {
					$last = $pos + strlen($cd['url']);

					if ($last >= strlen($url) || $url['last'] == '#' || $url['last'] == '?' || $url['last'] == '&') {
						$curOption = $realKey;
						if ($cd['type'] != 's' && $cd['type'] != 'r') {
							for ($i = $level - 1; $i >= 0; --$i) {
								$res = str_replace($cur[$i], $cur[$i].'||1', $res);
							}
						}
					}
				}
			}

			$res .= ".|".$cd["name"]."|";
			$res .= ($prefs['feature_sefurl'] == 'y' && !empty($cd['sefurl']))? $cd['sefurl']: $cd['url'];
			$res .= '||';

			$display_icon = ( $level == 0 || ( $level == 1 && ( $cd['type'] == 's' || $cd['type'] == 'r' ) ) );
			if ( $use_items_icons && $display_icon ) {
				global $smarty;
				require_once('lib/smarty_tiki/function.icon.php');
				$res .= smarty_function_icon(array(
					'_id' => $cd['icon'],
					'_notag' => 'y',
					'_defaultdir' => $prefs['menus_items_icons_path']
				), $smarty);
			}
			if (empty($curOption) && $cd['type'] != 'o' && $cd['type'] != '-') {
				$cur[$level - 1] = $res;
			}
 			$res .= "\n";
		}

		return $res;
	}
	function getParamsStyle($style) {
		switch ($style) {
		case 'vert':
			$type =  'layersmenu.inc.php';
			$class = 'LayersMenu';
			$new = 'newVerticalMenu';
			$tplFct = 'setVerticalMenuTpl';
			$tpl = 'layersmenu-vertical_menu-galaxy.ihtml';
			break;
		case 'horiz':
			$type = 'layersmenu.inc.php';
			$class = 'LayersMenu';
			$new = 'newHorizontalMenu';
			$tplFct = 'setHorizontalMenuTpl';
			$tpl = 'layersmenu-horizontal_menu.ihtml';
			break;
		case 'tree':
		default:
			$type = 'treemenu.inc.php';
			$class =  'TreeMenu';
			$new = 'newTreeMenu';
			$tplFct = '';
			$tpl = '';
			break;
		case 'phptree':
			$type = 'phptreemenu.inc.php';
			$class = 'PHPTreeMenu';
			$new =  'newPHPTreeMenu';
			$tplFct = '';
			$tpl = '';
			break;
		case 'plain':
			$type = 'plainmenu.inc.php';
			$class = 'PlainMenu';
			$new = 'newPlainMenu';
			$tplFct = '';
			$tpl = '';
			break;
		}
		return array($type, $class, $new, $tplFct, $tpl);		
	}
	function mkMenu($itall, $name, $style, $file='', $curOption = 0, $expandedDefault = 0) {
		static $name_counter = 0;
		if ( empty($name) ) {
			// Name must never be empty to avoid function names conflicts
			$name = '_tikilayer_'.$name_counter;
			$name_counter++;
		}
		list($plType, $plClass, $plNew, $plTplFct, $plTpl) = $this->getParamsStyle($style);
		include_once ("lib/phplayers/lib/PHPLIB.php");
		include_once ('lib/phplayers_tiki/lib/layersmenu-common.inc.php');
		include_once ('lib/phplayers/lib/layersmenu.inc.php');
		include_once ("lib/phplayers/lib/".$plType);
		global $$plClass;
		if (!isset($$plClass)) {
			$$plClass = new $plClass(); // to have 2 menus of the same type need no reinstanciation
		}
		$$plClass->setDirrootCommon("lib/phplayers/");
		$$plClass->setLibjsdir("lib/phplayers/libjs/");
		$$plClass->setTpldirCommon('../../lib/phplayers_tiki/templates/');

		if ( $style == 'tree' || $style == 'phptree' ) {
			// Use Tikiwiki icons for tree menus (especially to have famfamfam folders icons)
			$$plClass->setImgdir('../../pics/icons/');
			$$plClass->setImgwww('pics/icons/');
			$$plClass->setIcondir('../../pics/icons/');
			$$plClass->setIconwww('pics/icons/');
		} else {
			$$plClass->setImgdir("lib/phplayers/images/");
			$$plClass->setImgwww("lib/phplayers/images/");
		}

		if ($itall) {
			$$plClass->setMenuStructureString($itall);
		} elseif ($file && is_file($file)) {
			$$plClass->setMenuStructureFile($file);
		}
		$$plClass->parseStructureForMenu($name);
		if (!empty($curOption)) {
		  $$plClass->setSelectedItemByCount($name, $curOption);
		}

		$res = '';
		if ($style == 'vert' || $style == 'horiz') {
			$$plClass->setDownArrowImg('down-galaxy.png');
			$$plClass->setForwardArrowImg('forward-galaxy.png');
			$$plClass->$plTplFct($plTpl);
			$$plClass->setSubMenuTpl('layersmenu-sub_menu-galaxy.ihtml');
			$$plClass->$plNew($name);
			$res .= $$plClass->getMenu($name);
//			makeHeader and makeFooter are done in the footer.tpl (if there is more than one LayersMenus)
		} else {
			if ( $style == 'tree' || $style == 'phptree' ) {
				if ( $expandedDefault > 0 && $style == 'phptree' ) {
					$$plClass->{"set{$plClass}DefaultExpansion"}($expandedDefault);
				}
			}
			$res .= $$plClass->$plNew($name);
		}

		return $res;
	}
}
$tikiphplayers = new TikiPhpLayers();
