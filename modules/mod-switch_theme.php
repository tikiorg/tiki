<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
global $prefs, $user;

if ( isset($_COOKIE['tiki-theme']) && !($prefs['feature_userPreferences'] == 'y' && $user && $prefs['change_theme'] == 'y') ){
	$style = $_COOKIE['tiki-theme'];
}

$smarty->assign('styleslist',$tikilib->list_styles());

if ( isset($style) ) {
	$smarty->assign('styleName', ereg_replace($tikidomain."/", "", $style));
}
?>
