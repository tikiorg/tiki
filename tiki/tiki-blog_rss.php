<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-blog_rss.php,v 1.20 2003-10-12 12:37:28 ohertel Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
include_once ('lib/blogs/bloglib.php');

if ($rss_blog != 'y') {
	$smarty -> assign('msg', tra("This feature is disabled"));
	$smarty -> display("styles/$style_base/error.tpl");
	die; // TODO: output of rss file with message: rss disabled
}

if ($tiki_p_read_blog != 'y') {
	$smarty -> assign('msg', tra("Permission denied you can not view this section"));
	$smarty -> display("styles/$style_base/error.tpl");
	die; // TODO: output of rss file with message: permission denied
}

if (!isset($_REQUEST["blogId"])) {
	$smarty -> assign('msg', tra("No blogId specified"));
	$smarty -> display("styles/$style_base/error.tpl");
	die; // TODO: output of rss file with message: object not found
}

$id = "blogId";
$tmp = $tikilib -> get_blog($_REQUEST["$id"]);
$title = "Tiki RSS feed for blog ".$tmp["title"]; // TODO: make configurable
$desc = $tmp["description"]; // TODO: make configurable
$now = date("U");
$descId = "data";
$dateId = "created";
$titleId = "created";
$readrepl = "tiki-view_blog_post.php?$id=";
$changes = $bloglib -> list_blog_posts($_REQUEST["$id"], 0, $max_rss_blog, $dateId.'_desc', '', $now);

require ("tiki-rss.php");

?>
