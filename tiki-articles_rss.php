<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-articles_rss.php,v 1.19 2003-10-14 22:11:18 ohertel Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');

if ($rss_articles != 'y') {
	$smarty -> assign('msg', tra("This feature is disabled"));
	$smarty -> display("styles/$style_base/error.tpl");
	die; // TODO: output of rss file with message: rss disabled
}

if ($tiki_p_read_article != 'y') {
	$smarty -> assign('msg', tra("Permission denied you cannot view this section"));
	$smarty -> display("styles/$style_base/error.tpl");
	die; // TODO: output of rss file with message: permission denied
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