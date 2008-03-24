<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

$smarty->assign('module_title', isset($module_params["title"]) ? $module_params["title"] : tra("Quick Edit a Wiki Page"));


if (isset($module_params["templateId"])) {
	$templateId = $module_params["templateId"];
} else {
	$templateId = false;
}

if (isset($module_params["submit"])) {
	$submit = $module_params["submit"];
} else {
	$submit = tra('Edit','',true);
}
if (isset($module_params["size"])) {
	$size = $module_params["size"];
} else {
	$size = 15;
}
if (isset($module_params["mod_quickedit_heading"])) {
	$mod_quickedit_heading = $module_params["mod_quickedit_heading"];
} else {
	$mod_quickedit_heading = false;
}
if (isset($module_params["categId"])) {
	$categId = $module_params["categId"];
} else {
	$categId = '';
}
$smarty->assign('categId', $categId);
$smarty->assign('mod_quickedit_heading', $mod_quickedit_heading);
$smarty->assign('templateId', $templateId);
$smarty->assign('size', $size);
$smarty->assign('submit', $submit);

?>
