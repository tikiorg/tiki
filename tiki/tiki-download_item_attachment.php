<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-download_item_attachment.php,v 1.13 2006-09-19 16:33:15 ohertel Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
include_once("lib/init/initlib.php");
require_once ('tiki-setup_base.php');

include_once ('lib/trackers/trackerlib.php');

if ($tiki_p_view_trackers != 'y') {
	die;
}

if (!isset($_REQUEST["attId"])) {
	die;
}

$info = $trklib->get_item_attachment($_REQUEST["attId"]);

$t_use_db = $tikilib->get_preference('t_use_db', 'y');
$t_use_dir = $tikilib->get_preference('t_use_dir', '');

$trklib->add_item_attachment_hit($_REQUEST["attId"]);

$type = &$info["filetype"];
$file = &$info["filename"];
$content = &$info["data"];

session_write_close();
//print("File:$file<br />");
//die;
header ("Content-type: $type");
header( "Content-Disposition: attachment; filename=$file" );
//header ("Content-Disposition: inline; filename=\"".urlencode($file)."\"");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Pragma: public");

if ($info["path"]) {
	if (!file_exists($t_use_dir.$info["path"])) {
		$str = sprintf(tra("Error : The file %s doesn't exist."), $_REQUEST["attId"]). tra("Please contact the website administrator.");
		 header("Content-Length: ". strlen($str));
		echo $str;
	} else {
		header("Content-Length: ". filesize( $t_use_dir.$info["path"] ) );
		readfile ($t_use_dir . $info["path"]);
	}
} else {
	header("Content-Length: ". $info[ "filesize" ] );
	echo "$content";
}

?>
