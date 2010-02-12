<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
require_once ('lib/rss/rsslib.php');

$access->check_feature('feature_forums');

if ($prefs['rss_forums'] != 'y') {
	$errmsg=tra("rss feed disabled");
	require_once ('tiki-rss_error.php');
}

$res=$access->authorize_rss(array('tiki_p_admin_forum','tiki_p_forum_read'));
if($res) {
   if($res['header'] == 'y') {
      header('WWW-Authenticate: Basic realm="'.$tikidomain.'"');
      header('HTTP/1.0 401 Unauthorized');
   }
   $errmsg=$res['msg'];
   require_once ('tiki-rss_error.php');
}

$feed = "forums";
$uniqueid = $feed;
$output = $rsslib->get_from_cache($uniqueid);

if ($output["data"]=="EMPTY") {
	$title = (!empty($title_rss_forums)) ? $title_rss_forums :  tra("Tiki RSS feed for forums");
	$desc = (!empty($desc_rss_forums)) ? $desc_rss_forums : tra("Last topics in forums.");
	$id = 'object';
	$param = "threadId";
	$descId = "data";
	$dateId = "commentDate";
	$authorId = "userName";
	$titleId = "title";
	$readrepl = 'tiki-view_forum_thread.php?forumId=%s&comments_parentId=%s';

        $tmp = $prefs['title_rss_'.$feed];
        if ($tmp<>'') $title = $tmp;
        $tmp = $prefs['desc_rss_'.$feed];
        if ($desc<>'') $desc = $tmp;

	$changes = $tikilib -> list_all_forum_topics(0, $prefs['max_rss_forums'], $dateId.'_desc', '');
	$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, $param, $id, $title, $titleId, $desc, $descId, $dateId, $authorId);
}
header("Content-type: ".$output["content-type"]);
print $output["data"];
