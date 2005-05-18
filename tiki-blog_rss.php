<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-blog_rss.php,v 1.28 2005-05-18 10:58:55 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
require_once ('lib/blogs/bloglib.php');
require_once ('lib/rss/rsslib.php');

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
$title = tra("Tiki RSS feed for blog: ").$tmp["title"];
$desc = $tmp["description"];
$now = date("U");
$descId = "data";
$dateId = "created";
$authorId = "user";
$titleId = "title";
$readrepl = "tiki-view_blog_post.php?$id=%s";
$uniqueid = "$feed.$id=".$_REQUEST["$id"];

$tmp = $tikilib->get_preference('title_rss_'.$feed, '');
if ($tmp<>'') $title = $tmp;
$tmp = $tikilib->get_preference('desc_rss_'.$feed, '');
if ($desc<>'') $desc = $tmp;

$changes = $bloglib -> list_blog_posts($_REQUEST["$id"], 0, $max_rss_blog, $dateId.'_desc', '', $now);
$tmp = array();
foreach ($changes["data"] as $data)  {
	$data["$descId"] = $tikilib->parse_data($data["$descId"]);
	$tmp[] = $data;
}
$changes["data"] = $tmp;
$tmp = null;
$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, '', $id, $title, $titleId, $desc, $descId, $dateId, $authorId);

header("Content-type: ".$output["content-type"]);
print $output["data"];

?>
