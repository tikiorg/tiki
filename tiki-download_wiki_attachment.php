<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-download_wiki_attachment.php,v 1.11 2005-09-04 01:20:28 rlpowell Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
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

session_write_close();
//print("File:$file<br />");
//die;
header ("Content-type: $type");

// This used to say "header ("Content-Disposition: attachment; filename=\"$file\"");"
// which made everything try to download instead of the browser picking what to do.
// If people want the old behaviour, the right thing is probably to add an argument to 
// tiki-download_wiki_attachment.php, such as "&download", and then code in the attachment
// plugin, but the old behaviour really seems like The Wrong Thing to me.  -rlpowell
header ("Content-Disposition: filename=\"$file\"");

// Added March04 Damian, Akira123 reported test
header ("Expires: 0");
header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header ("Pragma: public");

if ($info["path"]) {
	header("Content-Length: ". filesize( $w_use_dir.$info["path"] ) );
	readfile ($w_use_dir . $info["path"]);
} else {
	header("Content-Length: ". $info[ "filesize" ] );
	echo "$content";
}

?>
