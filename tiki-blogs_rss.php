<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-blogs_rss.php,v 1.21 2003-10-12 12:37:29 ohertel Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
include_once ('lib/blogs/bloglib.php');

if ($rss_blogs != 'y') {
	$smarty -> assign('msg', tra("This feature is disabled"));
	$smarty -> display("styles/$style_base/error.tpl");
	die; // TODO: output of rss file with message: rss disabled
}

if ($tiki_p_read_blog != 'y') {
	$smarty -> assign('msg', tra("Permission denied you can not view this section"));
	$smarty -> display("styles/$style_base/error.tpl");
	die; // TODO: output of rss file with message: permission denied
}

$title = "Tiki RSS feed for weblogs"; // TODO: make configurable
$desc = "Last posts to weblogs."; // TODO: make configurable
$now = date("U");
$id = "blogId";
$descId = "data";
$dateId = "created";
$titleId = "blogtitle";
$readrepl = "tiki-view_blog_post.php?$id=";
$changes = $bloglib -> list_all_blog_posts(0, $max_rss_blogs, $dateId.'_desc', '', $now);

require ("tiki-rss.php");

?>
