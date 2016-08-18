<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
require_once ('lib/shoutbox/shoutboxlib.php');
$rsslib = TikiLib::lib('rss');

$access->check_feature('feature_shoutbox');

if ($prefs['feed_shoutbox'] != 'y') {
	$errmsg=tra("rss feed disabled");
	require_once ('tiki-rss_error.php');
}

$res=$access->authorize_rss(array('tiki_p_view_shoutbox','tiki_p_admin_cms'));
if ($res) {
	if ($res['header'] == 'y') {
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
	$title = $prefs['feed_shoutbox_title'];
	$desc = $prefs['feed_shoutbox_desc'];

	$id = "msgId";
	$titleId = "msgId";
	$descId = "message";
	$dateId = "timestamp";
	$authorId = "user";
	$readrepl = "tiki-shoutbox.php?get=%s";

	$changes = $shoutboxlib -> list_shoutbox(0, $prefs['feed_articles_max'], $id.'_desc', false);
	$tmp = null;
	$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, '', $id, $title, $titleId, $desc, $descId, $dateId, $authorId);
}
header("Content-type: ".$output["content-type"]);
print $output["data"];
