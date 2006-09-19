<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
PhpLayers in tikiwiki !

That smarty function is mostly intended to be used in .tpl files
syntax: {phplayers [type=tree|phptree|plain|hort|vert] [id=1] [file=/path/to/menufile]}

*/
function smarty_function_phplayers($params, &$smarty) {
	global $feature_phplayers;
	global $tikiphplayers; include_once('lib/phplayers_tiki/tiki-phplayers.php');
	if ($feature_phplayers != 'y') {
	  echo tra("phplayers are not available on this site");
	  return;
	}
	//$smarty->assign('uses_phplayers','y'); doesn't seem to be use
	extract($params);

	if (empty($type)) {
		$type = 'tree';
	}

	include_once ("lib/phplayers/lib/PHPLIB.php");
	include_once ("lib/phplayers_tiki/lib/layersmenu-common.inc.php"); // include Tiki's modified version of that file to keep original intact (luci)
	include_once ("lib/phplayers/lib/layersmenu.inc.php");
	include_once ("lib/phplayers/lib/".$types["$type"]);
	// beware ! that below is a variable class declaration
	$class = $classes["$type"];
	$phplayers = new $class();
	$phplayers->setDirrootCommon("lib/phplayers");
	$phplayers->setLibjsdir("lib/phplayers/libjs/");
	$phplayers->setImgdir("lib/phplayers/images/");
	$phplayers->setImgwww("lib/phplayers/menuimages/");
	$phplayers->setTpldirCommon("lib/phplayers/templates/");
	
	if (!empty($id)) {
		$output = $tikiphplayers->mkMenuEntry($id);
	}
	$name = 'usermenu'.$id;

	echo $tikiphplayers->mkmenu($output, $name, $type, $file);
}
?>
