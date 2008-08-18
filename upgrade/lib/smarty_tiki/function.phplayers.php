<?php
  // $header: $
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
	global $prefs, $tikiphplayers;
	include_once('lib/phplayers_tiki/tiki-phplayers.php');
	if ($prefs['feature_phplayers'] != 'y') {
	  echo tra("phplayers are not available on this site");
	  return;
	}
	//$smarty->assign('uses_phplayers','y'); doesn't seem to be use
	extract($params);

	if (empty($type)) {
		$type = 'tree';
	}
	if (!isset($sectionLevel)) {
		$sectionLevel = '';
	}
	if (!empty($id)) {
	  $output = $tikiphplayers->mkMenuEntry($id, $curOption, $sectionLevel);
	}
	$name = 'usermenu'.$id;
	if (!isset($file))
		$file = '';

	echo $tikiphplayers->mkMenu($output, $name, $type, $file, $curOption);
}
?>
