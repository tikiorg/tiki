<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$force_no_compression = true;
require_once ('tiki-setup.php');

include_once ('lib/userfiles/userfileslib.php');

if (!isset($_REQUEST["fileId"])) {
	die;
}
$access->check_feature('feature_userfiles');

$info = $userfileslib->get_userfile($user, $_REQUEST["fileId"]);
if ( empty($info['filetype']) || $info['filetype'] == 'application/x-octetstream' || $info['filetype'] == 'application/octet-stream' ) {
	include_once('lib/mime/mimelib.php');
	$info['filetype'] = tiki_get_mime($info['filename'], 'application/octet-stream');
}
$type = &$info["filetype"];
$file = &$info["filename"];
$content = &$info["data"];

session_write_close();
header ("Content-type: $type");
header ("Content-Disposition: inline; filename=\"$file\"");

if ($info["path"]) {
	readfile ($prefs['uf_use_dir'] . $info["path"]);
} else {
	echo "$content";
}
