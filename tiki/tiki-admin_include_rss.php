<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_rss.php,v 1.13 2005-01-22 22:54:52 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


$feeds=array('articles','directories','image_galleries','file_galleries','image_gallery','file_gallery', 'wiki','blogs','blog','forum','forums','mapfiles');
if (isset($_REQUEST["rss"])) {
	check_ticket('admin-inc-rss');

	foreach($feeds as $feed){
		$smarty->assign('max_rss_'.$feed, htmlentities(trim($_REQUEST['max_rss_'.$feed])));
		$tikilib->set_preference('max_rss_'.$feed, htmlentities(trim($_REQUEST['max_rss_'.$feed])));
		$smarty->assign('title_rss_'.$feed, htmlentities(trim($_REQUEST['title_rss_'.$feed])));
		$tikilib->set_preference('title_rss_'.$feed, htmlentities(trim($_REQUEST["title_rss_".$feed])));
		$smarty->assign('desc_rss_'.$feed, htmlentities(trim($_REQUEST['desc_rss_'.$feed])));
		$tikilib->set_preference('desc_rss_'.$feed, htmlentities(trim($_REQUEST["desc_rss_".$feed])));
	}
	

	$tikilib->set_preference('rssfeed_default_version', $_REQUEST["rssfeed_default_version"]);
	$smarty->assign("rssfeed_default_version", $_REQUEST["rssfeed_default_version"]);
	$tikilib->set_preference('rssfeed_language', $_REQUEST["rssfeed_language"]);
	$smarty->assign("rssfeed_language", $_REQUEST["rssfeed_language"]);
	$tikilib->set_preference('rssfeed_editor', $_REQUEST["rssfeed_editor"]);
	$smarty->assign("rssfeed_editor", $_REQUEST["rssfeed_editor"]);
	$tikilib->set_preference('rssfeed_publisher', $_REQUEST["rssfeed_publisher"]);
	$smarty->assign("rssfeed_publisher", $_REQUEST["rssfeed_publisher"]);
	$tikilib->set_preference('rssfeed_webmaster', $_REQUEST["rssfeed_webmaster"]);
	$smarty->assign("rssfeed_webmaster", $_REQUEST["rssfeed_webmaster"]);
	$tikilib->set_preference('rssfeed_creator', $_REQUEST["rssfeed_creator"]);
	$smarty->assign("rssfeed_creator", $_REQUEST["rssfeed_creator"]);

	if (isset($_REQUEST["rssfeed_css"]) && $_REQUEST["rssfeed_css"] == "on") {
		$tikilib->set_preference("rssfeed_css", 'y');

		$smarty->assign('rssfeed_css', 'y');
	} else {
		$tikilib->set_preference("rssfeed_css", 'n');

		$smarty->assign('rssfeed_css', 'n');
	}

	if (isset($_REQUEST["rss_directories"]) && $_REQUEST["rss_directories"] == "on") {
		$tikilib->set_preference("rss_directories", 'y');

		$smarty->assign('rss_directories', 'y');
	} else {
		$tikilib->set_preference("rss_directories", 'n');

		$smarty->assign('rss_directories', 'n');
	}

	if (isset($_REQUEST["rss_articles"]) && $_REQUEST["rss_articles"] == "on") {
		$tikilib->set_preference("rss_articles", 'y');

		$smarty->assign('rss_articles', 'y');
	} else {
		$tikilib->set_preference("rss_articles", 'n');

		$smarty->assign('rss_articles', 'n');
	}

	if (isset($_REQUEST["rss_blogs"]) && $_REQUEST["rss_blogs"] == "on") {
		$tikilib->set_preference("rss_blogs", 'y');

		$smarty->assign('rss_blogs', 'y');
	} else {
		$tikilib->set_preference("rss_blogs", 'n');

		$smarty->assign('rss_blogs', 'n');
	}

	if (isset($_REQUEST["rss_image_galleries"]) && $_REQUEST["rss_image_galleries"] == "on") {
		$tikilib->set_preference("rss_image_galleries", 'y');

		$smarty->assign('rss_image_galleries', 'y');
	} else {
		$tikilib->set_preference("rss_image_galleries", 'n');

		$smarty->assign('rss_image_galleries', 'n');
	}

	if (isset($_REQUEST["rss_file_galleries"]) && $_REQUEST["rss_file_galleries"] == "on") {
		$tikilib->set_preference("rss_file_galleries", 'y');

		$smarty->assign('rss_file_galleries', 'y');
	} else {
		$tikilib->set_preference("rss_file_galleries", 'n');

		$smarty->assign('rss_file_galleries', 'n');
	}

	if (isset($_REQUEST["rss_wiki"]) && $_REQUEST["rss_wiki"] == "on") {
		$tikilib->set_preference("rss_wiki", 'y');

		$smarty->assign('rss_wiki', 'y');
	} else {
		$tikilib->set_preference("rss_wiki", 'n');

		$smarty->assign('rss_wiki', 'n');
	}

	if (isset($_REQUEST["rss_forum"]) && $_REQUEST["rss_forum"] == "on") {
		$tikilib->set_preference("rss_forum", 'y');

		$smarty->assign('rss_forum', 'y');
	} else {
		$tikilib->set_preference("rss_forum", 'n');

		$smarty->assign('rss_forum', 'n');
	}

	if (isset($_REQUEST["rss_forums"]) && $_REQUEST["rss_forums"] == "on") {
		$tikilib->set_preference("rss_forums", 'y');

		$smarty->assign('rss_forums', 'y');
	} else {
		$tikilib->set_preference("rss_forums", 'n');

		$smarty->assign('rss_forums', 'n');
	}

	if (isset($_REQUEST["rss_mapfiles"]) && $_REQUEST["rss_mapfiles"] == "on") {
		$tikilib->set_preference("rss_mapfiles", 'y');

		$smarty->assign('rss_mapfiles', 'y');
	} else {
		$tikilib->set_preference("rss_mapfiles", 'n');

		$smarty->assign('rss_mapfiles', 'n');
	}

	if (isset($_REQUEST["rss_blog"]) && $_REQUEST["rss_blog"] == "on") {
		$tikilib->set_preference("rss_blog", 'y');

		$smarty->assign('rss_blog', 'y');
	} else {
		$tikilib->set_preference("rss_blog", 'n');

		$smarty->assign('rss_blog', 'n');
	}

	if (isset($_REQUEST["rss_image_gallery"]) && $_REQUEST["rss_image_gallery"] == "on") {
		$tikilib->set_preference("rss_image_gallery", 'y');

		$smarty->assign('rss_image_gallery', 'y');
	} else {
		$tikilib->set_preference("rss_image_gallery", 'n');

		$smarty->assign('rss_image_gallery', 'n');
	}

	if (isset($_REQUEST["rss_file_gallery"]) && $_REQUEST["rss_file_gallery"] == "on") {
		$tikilib->set_preference("rss_file_gallery", 'y');

		$smarty->assign('rss_file_gallery', 'y');
	} else {
		$tikilib->set_preference("rss_file_gallery", 'n');

		$smarty->assign('rss_file_gallery', 'n');
	}
} else {
	foreach($feeds as $feed){
		$smarty->assign("max_rss_".$feed, $tikilib->get_preference("max_rss_".$feed, 10));
		$smarty->assign("title_rss_".$feed, $tikilib->get_preference("title_rss_".$feed));
		$smarty->assign("desc_rss_".$feed, $tikilib->get_preference("desc_rss_".$feed));
	}

	$smarty->assign("rssfeed_default_version", $tikilib->get_preference("rssfeed_default_version","2"));
	$smarty->assign("rssfeed_language", $tikilib->get_preference("rssfeed_language","en-us"));
	$smarty->assign("rssfeed_editor", $tikilib->get_preference("rssfeed_editor",""));
	$smarty->assign("rssfeed_publisher", $tikilib->get_preference("rssfeed_publisher",""));
	$smarty->assign("rssfeed_webmaster", $tikilib->get_preference("rssfeed_webmaster",""));
	$smarty->assign("rssfeed_creator", $tikilib->get_preference("rssfeed_creator",""));
	$smarty->assign("rssfeed_css", $tikilib->get_preference("rssfeed_css","y"));
}
ask_ticket('admin-inc-rss');
?>
