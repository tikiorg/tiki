<?php 

// Initialization
require_once('tiki-setup.php');
include_once('lib/tikilib.php');

if (isset($_SERVER['HTTP_REFERER'])) {
	$orig_url = $_SERVER['HTTP_REFERER'];
} else {
	$orig_url = $prefs['tikiIndex'];
}

if (isset($_GET['theme'])){
	$new_theme = $_GET['theme'];
	if (empty($new_theme) || $new_theme != $prefs['style']) {	// use default theme option when setting 'site default' or changing main theme
		$_GET['theme-option'] = '';
	}
	if ($prefs['feature_userPreferences'] == 'y' && $user && $prefs['change_theme'] == 'y' && $group_theme == '') {  
		$tikilib->set_user_preference($user,'theme',$new_theme);
		$prefs['style'] = $new_theme;
	} elseif ($prefs['change_theme'] == 'y') {
		$prefs['style'] = $new_theme;
		$_SESSION['s_prefs']['style'] = $new_theme;
	}
}

if (isset($_GET['theme-option'])){
	$new_theme_option = $_GET['theme-option'];
	if ($prefs['feature_userPreferences'] == 'y' && $user && $prefs['change_theme'] == 'y' && empty($group_style)) {  
		$tikilib->set_user_preference($user,'theme-option',$new_theme_option);
		$prefs['style_option'] = $new_theme_option;
	} elseif ($prefs['change_theme'] == 'y') {
		$prefs['style_option'] = $new_theme_option;
		$_SESSION['s_prefs']['style_option'] = $new_theme_option;
	}
}

header("location: $orig_url");
exit;
?>
