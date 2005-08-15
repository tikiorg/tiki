<?php

// $Header: /cvsroot/tikiwiki/tiki/modules/mod-categories.php,v 1.2 2005-08-15 12:04:53 sylvieg Exp $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
if (!function_exists("categories_help")) {
	function categories_help() {
		return "type=wiki page|article|faq|blog|image gallery|image|file gallery|tracker|trackerItem|quiz|poll|survey|sheet|...,deep=on|off, categId=0";
	}
}


if ($feature_categories != 'y') {
	$smarty->assign('module_error', tra("This feature is disabled").": feature_categories");
} elseif ($tiki_p_view_categories != 'y') {
	$smarty->assign('module_error', tra("You do not have permission to use this feature"));
} else {
	global $user;
	global $categlib; include_once ('lib/categories/categlib.php');
	$ctall = $categlib->get_all_categories_respect_perms($user, 'tiki_p_view_categories');
	if (isset($module_params["type"])) {
		$type = $module_params["type"];
		$urlEnd = "&amp;type=".urlencode($type);
	} else {
		$type = '';
		$urlEnd = "";
	}
	if (isset($module_params["deep"]))
		$deep = $module_params["deep"];
	else
		$deep= 'on';
	$urlEnd .= "&amp;deep=$deep";
	if (isset($module_params["categId"]))
		$categId = $module_params["categId"];
	else
		$categId = 0;
	if ($categId == 0)
		$name = tra("Top");
	else {
		$car = $categlib->get_category($categId);
		$name = $car["name"];
	}
	if ($feature_phplayers == 'y') {
		$urlEnd .= "\n";
		if (!function_exists("mktree2")) {
			function mktree2($ind,$indent="",$back, $ctall, $urlEnd) {
				$kids = array();
				foreach ($ctall as $v) {
					if ($v['parentId'] == $ind) {
						$kids[] = $v;
					}
				}
				if (count($kids)) {
					foreach ($kids as $k) {
						$back.= $indent."|".$k['name']."|tiki-browse_categories.php?parentId=".$k['categId'].$urlEnd;
						$back.= mktree2($k['categId'],".$indent","", $ctall, $urlEnd);
					}
					return $back;
				} else {
					return "";
				}
			}
		}
		$itall = '.|'.$name.'|tiki-browse_categories.php?parentId='.$categId.$urlEnd;
		$itall .= mktree2($categId,"..","", $ctall, $urlEnd);
		include_once ("lib/phplayers/lib/PHPLIB.php");
		include_once ("lib/phplayers/lib/layersmenu-common.inc.php");
		include_once ("lib/phplayers/lib/treemenu.inc.php");
		if (!@is_object($phplayers))
			$phplayers = new TreeMenu();
		$phplayers->setDirrootCommon("lib/phplayers");
		$phplayers->setLibjsdir("lib/phplayers/libjs/");
		$phplayers->setImgdir("lib/phplayers/images/");
		$phplayers->setImgwww("lib/phplayers/images/");
		$phplayers->setTpldirCommon("lib/phplayers/templates/");
		if ($itall) {
			$phplayers->setMenuStructureString($itall);
		}
		$phplayers->parseStructureForMenu("modulecategory");
		$phpitall = '<a href="tiki-browse_categories.php?parentId=0&amp;type='.urlencode($type).'&amp;deep=$deep">'.tra("Top").'</a><br />';
		$phpitall = $phplayers->newTreeMenu("modulecategory");
		$smarty->assign('tree', $phpitall);
	} else {
		include_once ('lib/tree/categ_browse_tree.php');
		$tree_nodes = array();
		foreach ($ctall as $c) {
			$tree_nodes[] = array(
				"id" => $c["categId"],
				"parent" => $c["parentId"],
				"data" => '<a class="catname" href="tiki-browse_categories.php?parentId=' . $c["categId"] .$urlEnd.'">' . $c["name"] . '</a><br />'
			);
		}
		$tm = new CatBrowseTreeMaker("categ");
		$res = $tm->make_tree($categId, $tree_nodes);
		$smarty->assign('tree', $res);
	}
}
?>
