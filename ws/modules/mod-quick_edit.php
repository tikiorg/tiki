<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
global $smarty;
if( $prefs['feature_mootools'] == 'y' ) {
	global $headerlib;
	$headerlib->add_jsfile( 'lib/mootools/Observer.js' );
	$headerlib->add_jsfile( 'lib/mootools/Autocompleter.js' );
	$headerlib->add_jsfile( 'lib/mootools/Autocompleter.Request.js' );
	$headerlib->add_cssfile( 'lib/mootools/Autocompleter.css' );
}

$smarty->assign('module_title', isset($module_params["title"]) ? $module_params["title"] : tra("Quick Edit a Wiki Page"));


if (isset($module_params["templateId"])) {
	$templateId = $module_params["templateId"];
} else {
	$templateId = false;
}

if (isset($module_params['action'])) {
	$qe_action = $module_params['action'];
} else {
	$qe_action = 'tiki-editpage.php';
}

if (isset($module_params["submit"])) {
	$submit = $module_params["submit"];
} else {
	$submit = tra('Create/Edit','',true);
}
if (isset($module_params["size"])) {
	$size = $module_params["size"];
	$smarty->assign('size', $size);
}/* else {
	$size = 15;
}*/
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
$smarty->assign('qe_action', $qe_action);
$smarty->assign('submit', $submit);

if( !isset( $qe_usage_counter ) )
	$qe_usage_counter = 0;
$smarty->assign('qefield', 'qe-' . ++$qe_usage_counter);


