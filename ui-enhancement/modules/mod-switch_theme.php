<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
global $prefs, $user, $tikilib, $smarty;

if ( isset($_COOKIE['tiki-theme']) && !($prefs['feature_userPreferences'] == 'y' && $user && $prefs['change_theme'] == 'y') ){
	$style = $_COOKIE['tiki-theme'];
}
if ( isset($_COOKIE['tiki-theme-option']) && !($prefs['feature_userPreferences'] == 'y' && $user && $prefs['change_theme'] == 'y') ){
	$style_option = $_COOKIE['tiki-theme-option'];
}

$smarty->assign('styleslist',$tikilib->list_styles());
$smarty->assign_by_ref( "style_options", $tikilib->list_style_options());

/* $styleName not found anywhere - TODO delete this if safe
if ( isset($style) ) {
	$smarty->assign('styleName', ereg_replace($tikidomain."/", "", $style));
}
if ( isset($style_option) ) {
	$smarty->assign('styleNameOption', ereg_replace($tikidomain."/", "", $style_option));
}*/
