<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
require_once ('lib/blogs/bloglib.php');
require_once ('lib/rss/rsslib.php');
$access->check_feature('feature_blogs');

if ($prefs['feed_blogs'] != 'y') {
	$errmsg = tra("rss feed disabled");
	require_once ('tiki-rss_error.php');
}
$res = $access->authorize_rss(array(
	'tiki_p_read_blog',
	'tiki_p_blog_admin',
	'tiki_p_blog_view_ref'
));
if ($res) {
	if ($res['header'] == 'y') {
		header('WWW-Authenticate: Basic realm="' . $tikidomain . '"');
		header('HTTP/1.0 401 Unauthorized');
	}
	$errmsg = $res['msg'];
	require_once ('tiki-rss_error.php');
}
$feed = "blogs";
$uniqueid = $feed;
$output = $rsslib->get_from_cache($uniqueid);
if ($output["data"] == "EMPTY") {
	$title = $prefs['feed_blogs_title'];
	$desc = $prefs['feed_blogs_desc'];
	$now = date("U");
	$id = "postId";
	$descId = "data";
	$dateId = "created";
	$titleId = "title";
	$authorId = "user";
	$readrepl = "tiki-view_blog_post.php?postId=%s";
	$tmp = $prefs['feed_' . $feed . '_title'];
	if ($tmp <> '') $title = $tmp;
	$tmp = $prefs['feed_' . $feed . '_desc'];
	if ($desc <> '') $desc = $tmp;
	$changes = $bloglib->list_all_blog_posts(0, $prefs['feed_blogs_max'], $dateId . '_desc', '', $now);
	$tmp = array();
	include_once ('tiki-sefurl.php');
	foreach($changes["data"] as $data) {
		global $bloglib;
		$data["$descId"] = $tikilib->parse_data($data[$descId], array(
			'print' => true
		));
		$data['sefurl'] = filter_out_sefurl(sprintf($readrepl, $data['postId'], $data['blogId']) , $smarty, 'blogpost', urlencode($data['title']));
		$tmp[] = $data;
	}
	$changes["data"] = $tmp;
	$tmp = null;
	$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, '', $id, $title, $titleId, $desc, $descId, $dateId, $authorId);
}
header("Content-type: " . $output["content-type"]);
print $output["data"];
