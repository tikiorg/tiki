<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-blogs_rss.php,v 1.25 2004-03-15 21:27:27 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
include_once ('lib/blogs/bloglib.php');

if ($rss_blogs != 'y') {
        $errmsg=tra("rss feed disabled");
        require_once ('tiki-rss_error.php');
}

if ($tiki_p_read_blog != 'y') {
        $errmsg=tra("Permission denied you cannot view this section");
        require_once ('tiki-rss_error.php');
}

$feed = "blogs";
$title = tra("Tiki RSS feed for weblogs");
$desc = tra("Last posts to weblogs.");
$now = date("U");
$id = "blogId";
$descId = "data";
$dateId = "created";
$titleId = "blogtitle";
$readrepl = "tiki-view_blog_post.php?$id=";
$uniqueid = $feed;

require ("tiki-rss_readcache.php");

if ($output == "EMPTY") {
  $changes = $bloglib -> list_all_blog_posts(0, $max_rss_blogs, $dateId.'_desc', '', $now);
  $output="";
}

require ("tiki-rss.php");

?>
