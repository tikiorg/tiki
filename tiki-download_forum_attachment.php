<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-download_forum_attachment.php,v 1.4 2003-11-09 12:29:50 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup_base.php');

include_once ('lib/commentslib.php');

if ($tiki_p_forum_attach != 'y') {
	die;
}

if (!isset($_REQUEST["attId"])) {
	die;
}

$commentslib = new Comments($dbTiki);
$info = $commentslib->get_thread_attachment($_REQUEST["attId"]);

$type = &$info["filetype"];
$file = &$info["filename"];
$content = &$info["data"];

header ("Content-type: $type");
header ("Content-Disposition: inline; filename=\"$filei\"");

if ($info["dir"]) {
	readfile ($info["dir"] . $info["path"]);
} else {
	echo "$content";
}

?>
