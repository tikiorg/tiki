<?php

// $Header: /cvsroot/tikiwiki/tiki/user_avatar.php,v 1.1 2003-10-31 21:24:50 dheltzel Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# $Header: /cvsroot/tikiwiki/tiki/user_avatar.php,v 1.1 2003-10-31 21:24:50 dheltzel Exp $

// application to display an image from the database with 
// option to resize the image dynamically creating a thumbnail on the fly.
if (!isset($_REQUEST["user"])) {
	die;
}

if (is_file("temp/avatar.".$_REQUEST["user"]) and (!isset($_REQUEST["reload"]))) {
	$size = getimagesize("temp/avatar.".$_REQUEST["user"]);
} else {
	include_once ('db/tiki-db.php');
	include_once ('lib/tikilib.php');
	$tikilib = new Tikilib($dbTiki);
	//$data = $tikilib->get_avatar($_REQUEST["user"]);
	$data = $tikilib->get_user_avatar('admin');
	if ($data) {
		$fp = fopen("temp/avatar.".$_REQUEST["user"],"wb");
		fputs($fp,$data);
		fclose($fp);
	}
}

header ("Content-type: ");
readfile("temp/avatar.".$_REQUEST["user"]);
?>
