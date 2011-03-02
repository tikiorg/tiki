<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
require_once ('lib/articles/artlib.php');
require_once ('lib/rss/rsslib.php');

$access->check_feature('feature_articles');

if ($prefs['feed_articles'] != 'y') {
	$errmsg=tra("rss feed disabled");
	require_once ('tiki-rss_error.php');
}

$res=$access->authorize_rss(array('tiki_p_read_article','tiki_p_admin_cms', 'tiki_p_articles_read_heading'));
if($res) {
   if($res['header'] == 'y') {
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
    $topic = (int) preg_replace('/[^0-9]/','', $topic);
} elseif (isset($_REQUEST['topicname'])) {
	global $artlib; require_once 'lib/articles/artlib.php';
	$topic = $artlib->fetchtopicId($_REQUEST['topicname']);
	$uniqueid = $feed.".".$topic;
} else {
    $uniqueid = $feed;
    $topic = "";
}

if (isset($_REQUEST["type"])) {
        $uniqueid .= '-'.$type;
        $type = $_REQUEST["type"];
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

if ($topic and !$tikilib->user_has_perm_on_object($user,$topic,'topic','tiki_p_topic_read')) {
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
	if ($tmp<>'') $title = $tmp;
	$tmp = $prefs['feed_'.$feed.'_desc'];
	if ($desc<>'') $desc = $tmp;

	$changes = $artlib -> list_articles(0, $prefs['feed_articles_max'], $dateId.'_desc', '', 0, $tikilib->now, $user, $type, $topic, 'y', '', '', '', '', $articleLang, '', '', false, 'y');
	$tmp = array();
	include_once('tiki-sefurl.php');
	foreach ($changes["data"] as $data)  {
		$data["$descId"] = $tikilib->parse_data($data[$descId], array('print'=>true));
		$data["body"] = null;
		$data['sefurl'] = filter_out_sefurl(sprintf($readrepl, $data['articleId']), $smarty, 'article', $data['title']);
		$tmp[] = $data;
	}
	$changes["data"] = $tmp;
	$tmp = null;
	$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, '', $id, $title, $titleId, $desc, $descId, $dateId, $authorId);
}
header("Content-type: ".$output["content-type"]);
print $output["data"];
