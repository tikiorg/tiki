<?php 

// Initialization
require_once('tiki-setup.php');
include_once('lib/tikilib.php');

if (isset($_SERVER['HTTP_REFERER'])) {
	$orig_url = $_SERVER['HTTP_REFERER'];
} else {
	$orig_url = $tikiIndex;
}

if (isset($_GET['theme'])){
	$new_theme = $_GET['theme'];
	if($feature_userPreferences == 'y' && $user && $change_theme == 'y') {  
		$tikilib->set_user_preference($user,'theme',$new_theme);
	} else {
		$a = setcookie('tiki-theme', $new_theme, time()+3600*24*30*12);
	}
}

header("location: $orig_url");
exit;
?>
