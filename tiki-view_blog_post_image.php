<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-view_blog_post_image.php,v 1.4 2003-12-15 00:08:03 redflo Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/blogs/bloglib.php');

if (!isset($_REQUEST["imgId"])) {
	die;
}

$info = $bloglib->get_post_image($_REQUEST["imgId"]);
$type = &$info["filetype"];
$file = &$info["filename"];
$content = &$info["data"];
header ("Content-type: $type");
header ("Content-Disposition: inline; filename=$file");
echo "$content";

?>
