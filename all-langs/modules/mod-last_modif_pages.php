<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// Parameter absurl set if the last_modif_pages url is absolute or not [y|n].
// If not set, default = relative

if (isset($module_params["minor"]) && $module_params["minor"] == 'n')
	$ranking = $tikilib->last_major_pages($module_rows);
else
	$ranking = $tikilib->last_pages($module_rows);

$smarty->assign('modLastModif', $ranking);
$smarty->assign('maxlen', isset($module_params["maxlen"]) ? $module_params["maxlen"] : 0);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
$smarty->assign('absurl', isset($module_params["absurl"]) ? $module_params["absurl"] : 'n');
$smarty->assign('url', isset($module_params["url"]) ? $module_params["url"] : 'tiki-lastchanges.php');
?>