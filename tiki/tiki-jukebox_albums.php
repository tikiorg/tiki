<?php

// Initialization
require_once ('tiki-setup.php');

if ($feature_jukebox != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_jukebox");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_jukebox_albums != 'y') {
	$smarty->assign('msg', tra("Permission denied you can not view this section"));

	$smarty->display("error.tpl");
	die;
}

ask_ticket('jukebox-albums');

// Display the template
$smarty->assign('mid', 'tiki-jukebox_albums.tpl');
$smarty->display("tiki.tpl");

?>
