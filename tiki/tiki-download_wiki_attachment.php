<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-download_wiki_attachment.php,v 1.5 2004-01-30 08:17:36 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
include_once("lib/init/initlib.php");
require_once ('tiki-setup_base.php');

if ($tiki_p_wiki_view_attachments != 'y' && $tiki_p_wiki_admin_attachments != 'y') {
	die;
}

if (!isset($_REQUEST["attId"])) {
	die;
}

$info = $tikilib->get_wiki_attachment($_REQUEST["attId"]);

$w_use_db = $tikilib->get_preference('w_use_db', 'y');
$w_use_dir = $tikilib->get_preference('w_use_dir', '');

$tikilib->add_wiki_attachment_hit($_REQUEST["attId"]);

$type = &$info["filetype"];
$file = &$info["filename"];
$content = &$info["data"];

//print("File:$file<br/>");
//die;
header ("Content-type: $type");
header ("Content-Disposition: attachment; filename=\"$file\"");

if ($info["path"]) {
	header("Content-Length: ". filesize( $w_use_dir.$info["path"] ) );
	readfile ($w_use_dir . $info["path"]);
} else {
	header("Content-Length: ". $info[ "filesize" ] );
	echo "$content";
}

?>
