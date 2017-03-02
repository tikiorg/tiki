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
$rsslib = TikiLib::lib('rss');

$access->check_feature('feature_articles');

if ($prefs['feed_articles'] != 'y') {
	$errmsg=tra("rss feed disabled");
	require_once ('tiki-rss_error.php');
}

$res=$access->authorize_rss(array('tiki_p_read_article','tiki_p_admin_cms', 'tiki_p_articles_read_heading'));
if ($res) {
	if ($res['header'] == 'y') {
		header('WWW-Authenticate: Basic realm="'.$tikidomain.'"');
		header('HTTP/1.0 401 Unauthorized');
	}
	$errmsg=$res['msg'];
	require_once ('tiki-rss_error.php');
}

$feed = "articles";
if (isset($_REQUEST["topic"])) {
    $topic = $_REQUEST["topic"];
    $uniqueid = $feed.".".$topic;
    $topic = (int) preg_replace('/[^0-9]/', '', $topic);
} elseif (isset($_REQUEST['topicname'])) {
	$artlib = TikiLib::lib('art');
	$topic = $artlib->fetchtopicId($_REQUEST['topicname']);
	$uniqueid = $feed.".".$topic;
} else {
    $uniqueid = $feed;
    $topic = "";
}

if (isset($_REQUEST["type"])) {
        $type = $_REQUEST["type"];
        $uniqueid .= '-'.$type;
} else {
        $type = '';
}

if (isset($_REQUEST['lang'])) {
	$articleLang = $_REQUEST['lang'];
	$prefs['feed_language'] = $articleLang;
} else {
	$articleLang = '';
}
$uniqueid .= '/'.$articleLang;

$categId = '';
if (isset($_REQUEST["category"])) {
	$categlib = TikiLib::lib('categ');
	if (is_array($_REQUEST["category"]) ) {
		foreach ( $_REQUEST["category"] as $categname ) {
			$categIds[] = $categlib->get_category_id($categname);
		}
		sort($categIds);
		$categId = array('AND'=>$categIds);
		$uniqueid .= '-' . implode('-', $categIds);;
	} else {
		$categId = $categlib->get_category_id($_REQUEST["category"]);
		$uniqueid .= '-'.$categId;
	}
}
// Specifying categories by ID takes precedence, as it is more reliable
if (isset($_REQUEST["categId"])) {
	if (is_array($_REQUEST["categId"]) ) {
		sort($_REQUEST["categId"]);
		$categId = (int) $_REQUEST["categId"];
		$categId = array('AND'=>$_REQUEST["categId"]);
		$uniqueid .= '-' . implode('-', $_REQUEST["categId"]);;
	} else {
		$categId = (int) $_REQUEST["categId"];
		$uniqueid .= '-'.$categId;
	}
}

if ($topic and !$tikilib->user_has_perm_on_object($user, $topic, 'topic', 'tiki_p_topic_read')) {
	$smarty->assign('errortype', 401);
	$errmsg=tra("You do not have permission to view this section");
	require_once ('tiki-rss_error.php');
}

$output = $rsslib->get_from_cache($uniqueid);

if ($output["data"]=="EMPTY") {
	$title = $prefs['feed_articles_title'];
	$desc = $prefs['feed_articles_desc'];
	$id = "articleId";
	$titleId = "title";
	$descId = "heading";
	$dateId = "publishDate";
	$authorId = "author";
	$readrepl = "tiki-read_article.php?$id=%s";

	$tmp = $prefs['feed__'.$feed.'_title'];
	if ($tmp<>'') {
		$title = $tmp;
	}
	$tmp = $prefs['feed_'.$feed.'_desc'];
	if ($desc<>'') {
		$desc = $tmp;
	}

	$artlib = TikiLib::lib('art');
	$changes = $artlib->list_articles(0, $prefs['feed_articles_max'], $dateId.'_desc', '', 0, $tikilib->now, $user, $type, $topic, 'y', '', $categId, '', '', $articleLang, '', '', false, 'y');
	$tmp = array();
	include_once('tiki-sefurl.php');
	foreach ($changes["data"] as $data) {
		$data["$descId"] = $tikilib->parse_data($data[$descId], array('print'=>true));
		$data["body"] = null;
		$data['sefurl'] = filter_out_sefurl(sprintf($readrepl, $data['articleId']), 'article', $data['title']);
		$tmp[] = $data;
	}
	$changes["data"] = $tmp;
	$tmp = null;
	$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, '', $id, $title, $titleId, $desc, $descId, $dateId, $authorId);
}
header("Content-type: ".$output["content-type"]);
print $output["data"];
