<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-download_item_attachment.php,v 1.4 2003-11-09 12:29:50 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
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

//print("File:$file<br/>");
//die;
header ("Content-type: $type");
//header( "Content-Disposition: attachment; filename=$file" );
header ("Content-Disposition: inline; filename=\"$file\"");

if ($info["path"]) {
	readfile ($t_use_dir . $info["path"]);
} else {
	echo "$content";
}

?>
