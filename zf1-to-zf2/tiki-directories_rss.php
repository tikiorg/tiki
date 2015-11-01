<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$rsslib = TikiLib::lib('rss');
if ($prefs['feed_directories'] != 'y') {
	$errmsg = tra("rss feed disabled");
	require_once ('tiki-rss_error.php');
}
if ($prefs['feature_directory'] != 'y') {
	$errmsg = tra("This feature is disabled") . ": feature_directory";
	require_once ('tiki-rss_error.php');
}
$res = $access->authorize_rss(
	array(
		'tiki_p_view_directory',
		'tiki_p_admin_directory'
	)
);
if ($res) {
	if ($res['header'] == 'y') {
		header('WWW-Authenticate: Basic realm="' . $tikidomain . '"');
		header('HTTP/1.0 401 Unauthorized');
	}
	$errmsg = $res['msg'];
	require_once ('tiki-rss_error.php');
}
$feed = "directories";
$uniqueid = $feed;
$output = $rsslib->get_from_cache($uniqueid);
if ($output["data"] == "EMPTY") {
	$title = $prefs['feed_directories_title'];
	$desc = $prefs['feed_directories_desc'];
	$id = "siteId";
	$titleId = "name";
	$descId = "description";
	$dateId = "created";
	$readrepl = "tiki-directory_redirect.php?$id=%s";
	$tmp = $prefs['feed_' . $feed . '_title'];
	if ($tmp <> '') {
		$title = $tmp;
	}
	$tmp = $prefs['feed_' . $feed . '_desc'];
	if ($desc <> '') {
		$desc = $tmp;
	}
	$changes = $tikilib->dir_list_all_valid_sites2(0, $prefs['feed_directories_max'], $dateId . '_desc', '');
	$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, '', $id, $title, $titleId, $desc, $descId, $dateId, '');
}
header("Content-type: " . $output["content-type"]);
print $output["data"];
