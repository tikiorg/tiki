<?php
if(isset($_COOKIE['tiki-theme']) && !($feature_userPreferences == 'y' && $user && $change_theme == 'y')){
	$style = $_COOKIE['tiki-theme'];
}

$smarty->assign('styleslist',$tikilib->list_styles());

if(isset($style)){
	$smarty->assign('style', $style);
}
?>
