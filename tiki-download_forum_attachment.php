<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$force_no_compression = true;
require_once ('tiki-setup.php');

// roysinn: shouldn't need attach permission for download . . .
//if ($tiki_p_forum_attach != 'y') {
//	die;
//}

if (!isset($_REQUEST["attId"])) {
	die;
}

$commentslib = TikiLib::lib('comments');
$info = $commentslib->get_thread_attachment($_REQUEST["attId"]);

if ( empty($info['filetype']) || $info['filetype'] == 'application/x-octetstream' || $info['filetype'] == 'application/octet-stream' ) {
	$mimelib = TikiLib::lib('mime');
	$info['filetype'] = $mimelib->from_filename($info['filename']);
}
$type = &$info["filetype"];
$file = &$info["filename"];
$content = &$info["data"];

session_write_close();
header("Content-type: $type");
header("Content-Disposition: inline; filename=\"$file\"");

// Added Damian March04 request of Akira123
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Pragma: Public");

if ($info["dir"]) {
	readfile($info["dir"] . $info["path"]);
} else {
	echo "$content";
}
