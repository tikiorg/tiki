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

if (!isset($feature_jukebox_files)) {
        $smarty->assign('msg', tra("This feature has not been configured yet in its admin panel."));
        $smarty->display("error.tpl");
        die;
}

if (!is_dir($feature_jukebox_files)) {
        $smarty->assign('msg', tra("Please create a directory named $feature_jukebox_files to hold your audio files."));
        $smarty->display('error.tpl');
        die;
}

if(!isset($_REQUEST["albumId"])) {
	$_REQUEST["albumId"]=0;
}

if(isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find',$find);
$smarty->assign('albumId',$_REQUEST["albumId"]);

if(isset($_REQUEST["edit_mode"])&&$_REQUEST["edit_mode"]) {
	$smarty->assign('edit_mode','y');
	$smarty->assign('edited','y');
	if($_REQUEST["albumId"]>0) {
//		$info = $jukeboxlib->get_album_info($_REQUEST["albumId"]);

//		$smarty->assign_by_ref('title',$info["title"]);
//		$smarty->assign_by_ref('description',$info["description"]);

//		$smarty->assign_by_ref('maxRows',$info["maxRows"]);
//		$smarty->assign_by_ref('public',$info["public"]);
	}
}



ask_ticket('jukebox-albums');

// Display the template
$smarty->assign('mid', 'tiki-jukebox_albums.tpl');
$smarty->display("tiki.tpl");

?>
