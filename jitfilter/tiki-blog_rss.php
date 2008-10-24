<?php
// $Id: /cvsroot/tikiwiki/tiki/tiki-blog_rss.php,v 1.35.2.1 2008-01-17 17:47:01 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
require_once ('lib/blogs/bloglib.php');
require_once ('lib/rss/rsslib.php');

if ($prefs['rss_blog'] != 'y') {
        $errmsg=tra("rss feed disabled");
        require_once ('tiki-rss_error.php');
}

if (!isset($_REQUEST["blogId"])) {
        $errmsg=tra("No blogId specified");
        require_once ('tiki-rss_error.php');
}

$smarty->assign('individual', 'n');

if ($userlib->object_has_one_permission($_REQUEST["blogId"], 'blog')) {
	$smarty->assign('individual', 'y');

	if ($tiki_p_admin != 'y') {
		// Now get all the permissions that are set for this type of permissions 'image gallery'
		$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'blogs');

		foreach ($perms["data"] as $perm) {
			$permName = $perm["permName"];

			if ($userlib->object_has_permission($user, $_REQUEST["blogId"], 'blog', $permName)) {
				$$permName = 'y';

				$smarty->assign("$permName", 'y');
			} else {
				$$permName = 'n';

				$smarty->assign("$permName", 'n');
			}
		}
	}
}

if ($tiki_p_blog_admin == 'y') {
	$tiki_p_read_blog = 'y';
	$smarty->assign('tiki_p_read_blog', 'y');
}

if ($tiki_p_read_blog != 'y') {
	$smarty->assign('errortype', 401);
	$errmsg=tra("Permission denied you cannot view this section");
	require_once ('tiki-rss_error.php');
}

$feed = "blog";
$id = "blogId";
$uniqueid = "$feed.$id=".$_REQUEST["$id"];
$output = $rsslib->get_from_cache($uniqueid);

if ($output["data"]=="EMPTY") {
	$tmp = $tikilib -> get_blog($_REQUEST["$id"]);
	$title = (!empty($prefs['title_rss_'.$feed]))? $prefs['title_rss_'.$feed]: tra('Tiki RSS feed for blog: ');
	$title .= $tmp['title'];
	$desc .= (!empty($prefs['desc_rss_'.$feed]))? $prefs['desc_rss_'.$feed]: tra('Last modifications to the blog.');
	$desc .= $tmp["description"];
	$descId = "data";
	$dateId = "created";
	$authorId = "user";
	$titleId = "title";
	$readrepl = "tiki-view_blog_post.php?$id=%s&postId=%s";

	$changes = $bloglib -> list_blog_posts($_REQUEST["$id"], 0, $prefs['max_rss_blog'], $dateId.'_desc', '', $tikilib->now);
	$tmp = array();
	foreach ($changes["data"] as $data)  {
		$data["$descId"] = $tikilib->parse_data($data["$descId"]);
		$tmp[] = $data;
	}
	$changes["data"] = $tmp;
	$tmp = null;
	$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, 'postId', $id, $title, $titleId, $desc, $descId, $dateId, $authorId, false);
}
header("Content-type: ".$output["content-type"]);
print $output["data"];

?>
