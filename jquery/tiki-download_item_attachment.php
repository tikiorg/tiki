<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-download_item_attachment.php,v 1.18 2007-10-12 07:55:26 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$force_no_compression = true;
require_once ('tiki-setup.php');

include_once ('lib/trackers/trackerlib.php');

if (!isset($_REQUEST["attId"])) {
	die;
}

$info = $trklib->get_item_attachment($_REQUEST["attId"]);
$itemInfo = $trklib->get_tracker_item($info["itemId"]);

if (isset($info['user']) && $info['user'] == $user) {
} elseif ((isset($itemInfo['status']) and $itemInfo['status'] == 'p' && !$tikilib->user_has_perm_on_object($user, $itemInfo['trackerId'], 'tracker', 'tiki_p_view_trackers_pending')) 
	||  (isset($itemInfo['status']) and $itemInfo['status'] == 'c' && !$tikilib->user_has_perm_on_object($user, $itemInfo['trackerId'], 'tracker', 'tiki_p_view_trackers_closed'))
	||  ($tiki_p_admin_trackers != 'y' && !$tikilib->user_has_perm_on_object($user, $itemInfo['trackerId'], 'tracker', 'tiki_p_view_trackers')
	) ) {
	$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra('Permission denied'));
		$smarty->display('error.tpl');
		die;
}

$trklib->add_item_attachment_hit($_REQUEST["attId"]);

$type = &$info["filetype"];
$file = &$info["filename"];
$content = &$info["data"];

session_write_close();
//print("File:$file<br />");
//die;
header ("Content-type: $type");
header( "Content-Disposition: attachment; filename=\"$file\"" );
//header ("Content-Disposition: inline; filename=\"".urlencode($file)."\"");
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

?>
