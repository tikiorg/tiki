<?php
/*
PhpLayers in tikiwiki !

That smarty function is mostly intended to be used in .tpl files
syntax: {phplayers [type=tree|phptree|plain] [id=1] [file=/path/to/menufile]}

*/
function smarty_function_phplayers($params, &$smarty) {
	global $tikilib;
	extract($params);

	$types['tree'] = 'treemenu.inc.php';
	$types['phptree'] = 'phptreemenu.inc.php';
	$types['plain'] = 'phptreemenu.inc.php';

	$classes['tree'] = 'TreeMenu';
	$classes['phptree'] = 'PHPTreeMenu';
	$classes['plain'] = 'PlainMenu';

	if (empty($type)) {
		$type = 'tree';
	}

	include_once ("lib/phplayers/lib/PHPLIB.php");
	include_once ("lib/phplayers/lib/layersmenu-common.inc.php");
	include_once ("lib/phplayers/lib/".$types["$type"]);
	// beware ! that below is a variable class declaration
	$class = $classes["$type"];
	$phplayers = new $class();
	$phplayers->dirroot = "lib/phplayers";
	$phplayers->libjsdir = "lib/phplayers/libjs/";
	$phplayers->imgdir = "lib/phplayers/images/";
	$phplayers->imgwww = "lib/phplayers/images/";
	$phplayers->tpldir = "lib/phplayers/templates/";
	
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
	$phplayers->parseStructureForMenu("treemenu1");
	echo $phplayers->newTreeMenu("treemenu1");
}
?>
