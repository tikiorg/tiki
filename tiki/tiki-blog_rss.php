<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-blog_rss.php,v 1.23 2004-03-07 23:12:01 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
include_once ('lib/blogs/bloglib.php');

if ($rss_blog != 'y') {
        $errmsg=tra("rss feed disabled");
        require_once ('tiki-rss_error.php');
}

if ($tiki_p_read_blog != 'y') {
        $errmsg=tra("Permission denied you cannot view this section");
        require_once ('tiki-rss_error.php');
}

if (!isset($_REQUEST["blogId"])) {
        $errmsg=tra("No blogId specified");
        require_once ('tiki-rss_error.php');
}

$feed = "blog";
$id = "blogId";
$tmp = $tikilib -> get_blog($_REQUEST["$id"]);
$title = "Tiki RSS feed for blog ".$tmp["title"]; // TODO: make configurable
$desc = $tmp["description"]; // TODO: make configurable
$now = date("U");
$descId = "data";
$dateId = "created";
$titleId = "title";
$readrepl = "tiki-view_blog_post.php?$id=";
$uniqueid = "$feed.$id=".$_REQUEST["$id"];

require ("tiki-rss_readcache.php");

if ($output == "EMPTY") {
  $changes = $bloglib -> list_blog_posts($_REQUEST["$id"], 0, $max_rss_blog, $dateId.'_desc', '', $now);
  $output = "";
}

require ("tiki-rss.php");

?>
