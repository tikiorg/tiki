<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-articles_rss.php,v 1.25 2005-01-22 22:54:52 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');

if ($rss_articles != 'y') {
	$errmsg=tra("rss feed disabled");
	require_once ('tiki-rss_error.php');
}

if ($tiki_p_read_article != 'y') {
        $errmsg=tra("Permission denied you cannot view this section");
        require_once ('tiki-rss_error.php');
}

$feed = "articles";
$title = (!empty($title_rss_articles)) ? $title_rss_articles : tra("Tiki RSS feed for articles");
$desc = (!empty($desc_rss_articles)) ? $desc_rss_articles : tra("Last articles.") ;
$now = date("U");
$id = "articleId";
$titleId = "title";
$descId = "heading";
$dateId = "publishDate";
$readrepl = "tiki-read_article.php?$id=";
$uniqueid = $feed;

require ("tiki-rss_readcache.php");

if ($output == "EMPTY") {
  $changes = $tikilib -> list_articles(0, $max_rss_articles, $dateId.'_desc', '', $now, $user);
  $output = "";
}

require ("tiki-rss.php");

?>
