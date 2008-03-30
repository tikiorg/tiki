<?php
// $Id: /cvsroot/tikiwiki/tiki/lib/phplayers_tiki/tiki-phplayers.php,v 1.19.2.5 2008-02-27 15:18:45 nyloth Exp $
class TikiPhplayers extends TikiLib {
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
		} else {
			return array('', 0);
		}
	}
	function mkMenuEntry($idMenu, &$curOption, $sectionLevel='') {
		global $tikilib, $wikilib, $mylevel, $prefs;
		global $menulib; include_once('lib/menubuilder/menulib.php');
		$menu_info = $tikilib->get_menu($idMenu);
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
			$url = strtolower($url);
		}
		$realKey = 0;
		$level = 0;
		foreach ($channels['data'] as $key=>$cd) {
			$cd["name"] = tra($cd["name"]);
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
					$cd['url'] = strtolower($cd['url']);
				}
				if (($pos = strpos($url, $cd['url'])) !== false && ($pos == 0 || $url[$pos -1] == '/' || $url[$pos - 1] == '\\' || $url[$pos-1] == '=')) {
					$last = $pos + strlen($cd['url']);

					if ($last >= strlen($url) || $url['last'] == '#' || $url['last'] == '?' || $url['last'] == '&') {
						$curOption = $realKey;
						if ($cd['type'] != 's' && $cd['type'] != 'r') {
							for ($i = $level - 1; $i >= 0; --$i) {
								$res = str_replace($cur[$i], $cur[$i].'||||1', $res);
							}
						}
					}
				}
			}
			$res.= ".|".$cd["name"]."|";
			$res.= ($prefs['feature_sefurl'] == 'y' && !empty($cd['sefurl']))? $cd['sefurl']: $cd['url'];
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
		include_once ("lib/phplayers_tiki/lib/layersmenu-common.inc.php"); // include Tiki's modified version of that file to keep original intact (luci)
		include_once ('lib/phplayers/lib/layersmenu.inc.php');
		include_once ("lib/phplayers/lib/".$plType);
		global $$plClass;
		if (!isset($$plClass)) {
			$$plClass = new $plClass(); // to have 2 menus of the same type need no reinstanciation
		}
		$$plClass->setDirrootCommon("lib/phplayers/");
		$$plClass->setLibjsdir("lib/phplayers/libjs/");
		$$plClass->setTpldirCommon("lib/phplayers/templates/");

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
global $dbTiki;
$tikiphplayers = new TikiPhpLayers($dbTiki);
?>
