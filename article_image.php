<?php

// $Header: /cvsroot/tikiwiki/tiki/article_image.php,v 1.4 2003-11-11 21:27:21 dheltzel Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# $Header: /cvsroot/tikiwiki/tiki/article_image.php,v 1.4 2003-11-11 21:27:21 dheltzel Exp $

// application to display an image from the database with 
// option to resize the image dynamically creating a thumbnail on the fly.
if (!isset($_REQUEST["id"])) {
	die;
}

include_once ('db/tiki-db.php');
include_once ('lib/tikilib.php');
$tikilib = new Tikilib($dbTiki);
$data = $tikilib->get_article($_REQUEST["id"]);
$type = $data["image_type"];
//$data = $data["image_data"];
header ("Content-type: $type");
echo $data["image_data"];

?>
