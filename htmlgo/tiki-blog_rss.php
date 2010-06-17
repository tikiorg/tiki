<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
require_once ('lib/blogs/bloglib.php');
require_once ('lib/rss/rsslib.php');
if ($prefs['rss_blogs'] != 'y' && $prefs['rss_blog'] != 'y') {
	$errmsg = tra("rss feed disabled");
	require_once ('tiki-rss_error.php');
}
if (!isset($_REQUEST["blogId"])) {
	$errmsg = tra("No blogId specified");
	require_once ('tiki-rss_error.php');
}
$smarty->assign('individual', 'n');
$tikilib->get_perm_object($_REQUEST["blogId"], 'blog');

if ($tiki_p_read_blog != 'y') {
	$smarty->assign('errortype', 401);
	$errmsg = tra("Permission denied. You cannot view this section");
	require_once ('tiki-rss_error.php');
}
$feed = "blog";
$id = "blogId";
$uniqueid = "$feed.$id=" . $_REQUEST["$id"];
$output = $rsslib->get_from_cache($uniqueid);
if ($output["data"] == "EMPTY") {
	$tmp = $tikilib->get_blog($_REQUEST["$id"]);
	$title = (!empty($prefs['title_rss_' . $feed])) ? $prefs['title_rss_' . $feed] : tra('Tiki RSS feed for blog: ');
	$title.= $tmp['title'];
	$desc.= (!empty($prefs['desc_rss_' . $feed])) ? $prefs['desc_rss_' . $feed] : tra('Last modifications to the blog.');
	$desc.= $tmp["description"];
	$descId = "data";
	$dateId = "created";
	$authorId = "user";
	$titleId = "title";
	$readrepl = "tiki-view_blog_post.php?postId=%s";
	$changes = $bloglib->list_blog_posts($_REQUEST["$id"], false, 0, $prefs['max_rss_blog'], $dateId . '_desc', '', '', $tikilib->now);
	$tmp = array();
	include_once ('tiki-sefurl.php');
	foreach($changes["data"] as $data) {
		$data["$descId"] = $tikilib->parse_data($data[$descId], array(
			'print' => true
		));
		$data['sefurl'] = filter_out_sefurl(sprintf($readrepl, $data['postId']) , $smarty, 'blogpost', $data['title']);
		$tmp[] = $data;
	}
	$changes["data"] = $tmp;
	$tmp = null;
	$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, 'blogId', '', $title, $titleId, $desc, $descId, $dateId, $authorId, false);
}
header("Content-type: " . $output["content-type"]);
print $output["data"];
