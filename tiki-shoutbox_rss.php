<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: $

require_once ('tiki-setup.php');
require_once ('lib/shoutbox/shoutboxlib.php');
require_once ('lib/rss/rsslib.php');

$access->check_feature('feature_shoutbox');

if ($prefs['rss_shoutbox'] != 'y') {
	$errmsg=tra("rss feed disabled");
	require_once ('tiki-rss_error.php');
}

$res=$access->authorize_rss(array('tiki_p_view_shoutbox','tiki_p_admin_cms'));
if($res) {
   if($res['header'] == 'y') {
      header('WWW-Authenticate: Basic realm="'.$tikidomain.'"');
      header('HTTP/1.0 401 Unauthorized');
   }
   $errmsg=$res['msg'];
   require_once ('tiki-rss_error.php');
}

$feed = "shoutbox";
$uniqueid = $feed;
$output = $rsslib->get_from_cache($uniqueid);
if ($output["data"]=="EMPTY") {
	$title = (!empty($title_rss_articles)) ? $title_rss_articles : tra("Tiki RSS feed for shoutbox messages");
	$desc = (!empty($desc_rss_articles)) ? $desc_rss_articles : tra("Last shoutbox messages.");
	
	$id = "msgId";
	$titleId = "msgId";
	$descId = "message";
	$dateId = "timestamp";
	$authorId = "user";
	$readrepl = "tiki-shoutbox.php?get=%s";

	$tmp = $prefs['title_rss_'.$feed];
	if ($tmp<>'') $title = $tmp;
	$tmp = $prefs['desc_rss_'.$feed];
	if ($desc<>'') $desc = $tmp;

	$changes = $shoutboxlib -> list_shoutbox(0, $prefs['max_rss_articles'], $id.'_desc');
	$tmp = null;
	$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, '', $id, $title, $titleId, $desc, $descId, $dateId, $authorId);
}
header("Content-type: ".$output["content-type"]);
print $output["data"];
