<?php
// $Header: /cvsroot/tikiwiki/_mods/features/jukebox/tikiroot/tiki-jukebox_tracks.php,v 1.1 2004-10-28 21:00:45 damosoft Exp $

// Jukebox Tracks
// Damian Parker

require_once ('tiki-setup.php');
require_once ('lib/jukebox/jukeboxlib.php');

if ($feature_jukebox != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_jukebox");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_jukebox_tracks != 'y') {
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

if(!isset($_REQUEST["trackId"])) {
	$_REQUEST["trackId"]=0;
}

if(isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

if (!isset($_REQUEST["offset"])) {
        $offset = 0;
} else {
        $offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

if (!isset($_REQUEST["sort_mode"])) {
        $sort_mode = "trackName_asc";
} else {
        $sort_mode = $_REQUEST["sort_mode"];
}

$smarty->assign_by_ref('sort_mode', $sort_mode);

$smarty->assign('find',$find);
$smarty->assign('trackId',$_REQUEST["trackId"]);

$listtracks = $jukeboxlib->list_tracks($offset, $maxRecords, $sort_mode, $find);

$cant_pages = ceil($listtracks["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($listtracks["cant"] > ($offset + $maxRecords)) {
        $smarty->assign('next_offset', $offset + $maxRecords);
} else {
        $smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
        $smarty->assign('prev_offset', $offset - $maxRecords);
} else {
        $smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('listtracks', $listgenres["data"]);

ask_ticket('jukebox-tracks');

// Display the template
$smarty->assign('mid', 'tiki-jukebox_tracks.tpl');
$smarty->display("tiki.tpl");

?>
