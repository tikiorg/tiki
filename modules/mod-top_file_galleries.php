<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}

global $filegallib;
if (!is_object($filegallib)) {
	include_once ('lib/filegals/filegallib.php');
}
$ranking = $filegallib->list_visible_file_galleries(0, $module_rows, 'hits_desc', 'admin', '');

$smarty->assign('modTopFileGalleries', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>
