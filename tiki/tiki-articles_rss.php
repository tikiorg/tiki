<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-articles_rss.php,v 1.20 2004-01-15 09:56:26 redflo Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
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
$title = "Tiki RSS feed for articles"; // TODO: make configurable
$desc = "Last articles."; // TODO: make configurable
$now = date("U");
$id = "articleId";
$titleId = "title";
$descId = "heading";
$dateId = "publishDate";
$readrepl = "tiki-read_article.php?$id=";

require ("tiki-rss_readcache.php");

if ($output == "EMPTY") {
  $changes = $tikilib -> list_articles(0, $max_rss_articles, $dateId.'_desc', '', $now, $user);
  $output = "";
}

require ("tiki-rss.php");

?>
