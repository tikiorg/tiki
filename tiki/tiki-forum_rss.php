<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-forum_rss.php,v 1.19 2006-08-29 20:19:02 sylvieg Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once('tiki-setup.php');
require_once('lib/tikilib.php');
require_once ('lib/rss/rsslib.php');

if ($rss_forum != 'y') {
        $errmsg=tra("rss feed disabled");
        require_once ('tiki-rss_error.php');
}

if($tiki_p_admin_forum != 'y' && $tiki_p_forum_read != 'y') {
        $errmsg=tra("Permission denied you cannot view this section");
        require_once ('tiki-rss_error.php');
}

if(!isset($_REQUEST["forumId"])) {
        $errmsg=tra("No forumId specified");
        require_once ('tiki-rss_error.php');
}

require_once('lib/commentslib.php');
if (!isset($commentslib)) {
	$commentslib = new Comments($dbTiki);
}

$feed = "forum";
$tmp = $commentslib->get_forum($_REQUEST["forumId"]);
$title = tra("Tiki RSS feed for forum: ").$tmp["name"];
$desc = $tmp["description"];
$now = date("U");
$id = "forumId";
$param = "threadId";
$descId = "data";
$dateId = "commentDate";
$authorId = "userName";
$titleId = "title";
$readrepl = "tiki-view_forum_thread.php?$id=%s&comments_parentId=%s";
$uniqueid = "$feed.$id=".$_REQUEST["$id"];

$tmp = $tikilib->get_preference('title_rss_'.$feed, '');
if ($tmp<>'') $title = $tmp;
$tmp = $tikilib->get_preference('desc_rss_'.$feed, '');
if ($desc<>'') $desc = $tmp;

$changes = $tikilib->list_forum_topics($_REQUEST["$id"],0, $max_rss_forum, $dateId.'_desc', '');
$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, $param, $id, $title, $titleId, $desc, $descId, $dateId, $authorId);

header("Content-type: ".$output["content-type"]);
print $output["data"];

?>
