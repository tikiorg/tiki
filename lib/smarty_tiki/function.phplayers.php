<?php
/*
PhpLayers in tikiwiki !

That smarty function is mostly intended to be used in .tpl files
syntax: {phplayers [type=tree|phptree|plain] [id=1] [file=/path/to/menufile]}

*/
function smarty_function_phplayers($params, &$smarty) {
	global $tikilib,$smarty;
	$smarty->assign('uses_phplayers','y');
	extract($params);

	$types['vert'] = 'layersmenu.inc.php';
        $types['horiz'] = 'layersmenu.inc.php';
	$types['tree'] = 'treemenu.inc.php';
	$types['phptree'] = 'phptreemenu.inc.php';
	$types['plain'] = 'phptreemenu.inc.php';

	$classes['vert'] = 'LayersMenu';
        $classes['horiz'] = 'LayersMenu';
	$classes['tree'] = 'TreeMenu';
	$classes['phptree'] = 'PHPTreeMenu';
	$classes['plain'] = 'PlainMenu';
	
	$struct['vert'] = 'vermenu2';
        $struct['horiz'] = 'hormenu3';
	$struct['tree'] = 'treemenu1';
	$struct['phptree'] = 'treemenu1';
	$struct['plain'] = 'treemenu1';

	$new['vert'] = 'newVerticalMenu';
        $new['horiz'] = 'newHorizontalMenu';
	$new['tree'] = 'newTreeMenu';
	$new['phptree'] = 'newTreeMenu';
	$new['plain'] = 'newTreeMenu';

	if (empty($type)) {
		$type = 'tree';
	}

	include_once ("lib/phplayers/lib/PHPLIB.php");
	include_once ("lib/phplayers/lib/layersmenu-common.inc.php");
	include_once ("lib/phplayers/lib/layersmenu.inc.php");
	include_once ("lib/phplayers/lib/".$types["$type"]);
	// beware ! that below is a variable class declaration
	$class = $classes["$type"];
	$phplayers = new $class();
	$phplayers->setDirrootCommon("lib/phplayers");
	$phplayers->setLibjsdir("lib/phplayers/libjs/");
	$phplayers->setImgdir("lib/phplayers/images/");
	$phplayers->setImgwww("lib/phplayers/images/");
	$phplayers->setTpldirCommon("lib/phplayers/templates/");
	
	if (!empty($id)) {
		$menu_info = $tikilib->get_menu($id);
		$channels = $tikilib->list_menu_options($id,0,-1,'position_asc','');
		$intended = false;
		$output = '';
		foreach ($channels["data"] as $cd) {
			if ($cd["type"] == 'o' and $indented) {
				$output.= ".";
			} elseif ($cd["type"] == 's') {
				$indented = true;
			}
			$output.= ".|".$cd["name"]."|".$cd["url"]."\n";
		}
		$phplayers->setMenuStructureString($output);
	} elseif (!empty($file)) {
		if (is_file($file)) {	
			$phplayers->setMenuStructureFile($file);
		} else {
			$phplayers->setMenuStructureFile("lib/phplayers/layersmenu-vertical-2.txt");
		}
	}
	
	$phplayers->parseStructureForMenu($struct["$type"]);
	if ($type == 'vert') {
		$phplayers->setDownArrowImg("down-galaxy.png");
		$phplayers->setForwardArrowImg("forward-galaxy.png");
		$phplayers->setVerticalMenuTpl("layersmenu-vertical_menu-galaxy.ihtml");
		$phplayers->setSubMenuTpl("layersmenu-sub_menu-galaxy.ihtml");
		$phplayers->$new["$type"]($struct["$type"]);
		$phplayers->printHeader();
		$phplayers->printMenu($struct["$type"]);
		$phplayers->printFooter();
	} elseif ($type == 'horiz') {
		$phplayers->setDownArrowImg("down-galaxy.png");
		$phplayers->setForwardArrowImg("forward-galaxy.png");
		$phplayers->setHorizontalMenuTpl("layersmenu-horizontal_menu.ihtml");
		$phplayers->setSubMenuTpl("layersmenu-sub_menu-galaxy.ihtml");
		$phplayers->$new["$type"]($struct["$type"]);
		$phplayers->printHeader();
		$phplayers->printMenu($struct["$type"]);
		$phplayers->printFooter();
	} else {
		echo $phplayers->$new["$type"]($struct["$type"]);
	}
	
}
?>
