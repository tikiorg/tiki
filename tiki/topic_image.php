<?php

// $Header: /cvsroot/tikiwiki/tiki/topic_image.php,v 1.6 2003-11-03 21:11:54 dheltzel Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# $Header: /cvsroot/tikiwiki/tiki/topic_image.php,v 1.6 2003-11-03 21:11:54 dheltzel Exp $

// application to display an image from the database with 
// option to resize the image dynamically creating a thumbnail on the fly.
if (!isset($_REQUEST["id"])) {
	die;
}

$topiccachefile = "temp" . "/topic.".$_REQUEST["id"];

if (is_file($topiccachefile) and (!isset($_REQUEST["reload"]))) {
	$size = getimagesize($topiccachefile);
	header ("Content-type: ".$size['mime']);
	readfile($topiccachefile);
} else {
	include_once ('db/tiki-db.php');
	include_once ('lib/tikilib.php');
	$tikilib = new Tikilib($dbTiki);
	$data = $tikilib->get_topic_image($_REQUEST["id"]);
	$type = $data["image_type"];
	$data = $data["image_data"];
	if ($data["image_data"]) {
		$fp = fopen($topiccachefile,"wb");
		fputs($fp,$data);
		fclose($fp);
	}
}

header ("Content-type: $type");
readfile($topiccachefile);
?>
