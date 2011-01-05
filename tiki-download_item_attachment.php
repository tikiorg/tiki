<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$force_no_compression = true;
require_once ('tiki-setup.php');

include_once ('lib/trackers/trackerlib.php');

if (empty($_REQUEST['attId']) && !empty($_REQUEST['itemId']) && !empty($_REQUEST['fieldId'])) {
	$_REQUEST['attId'] = $trklib->get_item_value(0, $_REQUEST['itemId'], $_REQUEST['fieldId']);
}

if (empty($_REQUEST['attId'])) {
	$smarty->assign('msg', tra('Incorrect param'));
	$smarty->display('error.tpl');
	die;
}

$info = $trklib->get_item_attachment($_REQUEST['attId']);
if (empty($info)) {
	$smarty->assign('msg', tra('Incorrect param'));
	$smarty->display('error.tpl');
	die;
}
$itemInfo = $trklib->get_tracker_item($info["itemId"]);
$itemUser = $trklib->get_item_creator($itemInfo['trackerId'], $itemInfo['itemId']);

if (isset($info['user']) && $info['user'] == $user) {
} elseif (!empty($itemUser) && $user == $itemUser) {
} elseif ((isset($itemInfo['status']) and $itemInfo['status'] == 'p' && !$tikilib->user_has_perm_on_object($user, $itemInfo['trackerId'], 'tracker', 'tiki_p_view_trackers_pending')) 
	||  (isset($itemInfo['status']) and $itemInfo['status'] == 'c' && !$tikilib->user_has_perm_on_object($user, $itemInfo['trackerId'], 'tracker', 'tiki_p_view_trackers_closed'))
	||  ($tiki_p_admin_trackers != 'y' && !$tikilib->user_has_perm_on_object($user, $itemInfo['trackerId'], 'tracker', 'tiki_p_view_trackers') )
    ) {
	$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra('Permission denied'));
		$smarty->display('error.tpl');
		die;
}

$trklib->add_item_attachment_hit($_REQUEST["attId"]);

if ( empty($info['filetype']) || $info['filetype'] == 'application/x-octetstream' || $info['filetype'] == 'application/octet-stream' ) {
	include_once('lib/mime/mimelib.php');
	$info['filetype'] = tiki_get_mime($info['filename'], 'application/octet-stream');
}
$type = &$info["filetype"];
$file = &$info["filename"];
$content = &$info["data"];

session_write_close();
//print("File:$file<br />");
//die;
header ("Content-type: $type");
if (isset($_REQUEST["display"])) {
//die;
	header ("Content-Disposition: inline; filename=\"".urlencode($file)."\"");
} else {
	header( "Content-Disposition: attachment; filename=\"$file\"" );
}
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Pragma: public");

if ($info["path"]) {
	if (!file_exists($prefs['t_use_dir'].$info["path"])) {
		$str = sprintf(tra("Error : The file %s doesn't exist."), $_REQUEST["attId"]). tra("Please contact the website administrator.");
		 header("Content-Length: ". strlen($str));
		echo $str;
	} else {
		header("Content-Length: ". filesize( $prefs['t_use_dir'].$info["path"] ) );
		readfile ($prefs['t_use_dir'] . $info["path"]);
	}
} else {
	header("Content-Length: ". $info[ "filesize" ] );
	echo "$content";
}
