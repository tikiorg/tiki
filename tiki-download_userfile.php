<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-download_userfile.php,v 1.12.2.1 2008-03-01 17:12:54 leyan Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$force_no_compression = true;
require_once ('tiki-setup.php');

include_once ('lib/userfiles/userfileslib.php');

if (!isset($_REQUEST["fileId"])) {
	die;
}

if ($prefs['feature_userfiles'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_userfiles");

	$smarty->display("error.tpl");
	die;
}

$info = $userfileslib->get_userfile($user, $_REQUEST["fileId"]);
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

?>
