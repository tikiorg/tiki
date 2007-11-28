<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-articles_rss.php,v 1.35.2.2 2007-11-28 21:58:37 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
require_once ('lib/rss/rsslib.php');

if ($prefs['rss_articles'] != 'y') {
	$errmsg=tra("rss feed disabled");
	require_once ('tiki-rss_error.php');
}

if ($tiki_p_read_article != 'y') {
        $errmsg=tra("Permission denied you cannot view this section");
        require_once ('tiki-rss_error.php');
}

$feed = "articles";
if (isset($_REQUEST["topic"])) {
    $topic = $_REQUEST["topic"];
    $uniqueid = $feed.".".$topic;
    $topic = (int) ereg_replace("[^0-9]","", $topic);
} elseif (isset($_REQUEST['topicname'])) {
	$topic = $tikilib->fetchtopicId($_REQUEST['topicname']);
	$uniqueid = $feed.".".$topic;
} else {
    $uniqueid = $feed;
    $topic = "";
}
if (isset($_REQUEST['lang'])) {
	$articleLang = $_REQUEST['lang'];
	$prefs['rssfeed_language'] = $articleLang;
} else {
	$articleLang = '';
}
$uniqueid .= '/'.$articleLang;

if ($topic and !$tikilib->user_has_perm_on_object($user,$topic,'topic','tiki_p_topic_read')) {
	$errmsg=tra("Permission denied you cannot view this section");
	require_once ('tiki-rss_error.php');
}

$output = $rsslib->get_from_cache($uniqueid);

if ($output["data"]=="EMPTY") {
	$tmp = tra("Tiki RSS feed for articles");
	$title = (!empty($title_rss_articles)) ? $title_rss_articles : $tmp;
	$tmp = tra("Last articles.");
	$desc = (!empty($desc_rss_articles)) ? $desc_rss_articles : $tmp;
	$id = "articleId";
	$titleId = "title";
	$descId = "heading";
	$dateId = "publishDate";
	$authorId = "author";
	$readrepl = "tiki-read_article.php?$id=%s";

	$tmp = $prefs['title_rss_'.$feed];
	if ($tmp<>'') $title = $tmp;
	$tmp = $prefs['desc_rss_'.$feed];
	if ($desc<>'') $desc = $tmp;

	$changes = $tikilib -> list_articles(0, $prefs['max_rss_articles'], $dateId.'_desc', '', $tikilib->now, $user, '', $topic, 'y', '', '', '', '', $articleLang);
	$tmp = array();
	foreach ($changes["data"] as $data)  {
		$data["$descId"] = $tikilib->parse_data($data["$descId"]);
		$data["body"] = null;
		$tmp[] = $data;
	}
	$changes["data"] = $tmp;
	$tmp = null;
	$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, '', $id, $title, $titleId, $desc, $descId, $dateId, $authorId);
}
header("Content-type: ".$output["content-type"]);
print $output["data"];

?>
