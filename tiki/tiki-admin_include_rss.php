<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_rss.php,v 1.10 2004-05-01 01:06:19 damosoft Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}


if (isset($_REQUEST["rss"])) {
	check_ticket('admin-inc-rss');
	$tikilib->set_preference('max_rss_articles', $_REQUEST["max_rss_articles"]);
	$smarty->assign("max_rss_articles", $_REQUEST["max_rss_articles"]);
	$tikilib->set_preference('max_rss_directories', $_REQUEST["max_rss_directories"]);
	$smarty->assign("max_rss_directories", $_REQUEST["max_rss_directories"]);
	$tikilib->set_preference('max_rss_image_galleries', $_REQUEST["max_rss_image_galleries"]);
	$smarty->assign("max_rss_image_galleries", $_REQUEST["max_rss_image_galleries"]);
	$tikilib->set_preference('max_rss_file_galleries', $_REQUEST["max_rss_file_galleries"]);
	$smarty->assign("max_rss_file_galleries", $_REQUEST["max_rss_file_galleries"]);
	$tikilib->set_preference('max_rss_image_gallery', $_REQUEST["max_rss_image_gallery"]);
	$smarty->assign("max_rss_image_gallery", $_REQUEST["max_rss_image_gallery"]);
	$tikilib->set_preference('max_rss_file_gallery', $_REQUEST["max_rss_file_gallery"]);
	$smarty->assign("max_rss_file_gallery", $_REQUEST["max_rss_file_gallery"]);
	$tikilib->set_preference('max_rss_wiki', $_REQUEST["max_rss_wiki"]);
	$smarty->assign("max_rss_wiki", $_REQUEST["max_rss_wiki"]);
	$tikilib->set_preference('max_rss_blogs', $_REQUEST["max_rss_blogs"]);
	$smarty->assign("max_rss_blogs", $_REQUEST["max_rss_blogs"]);
	$tikilib->set_preference('max_rss_blog', $_REQUEST["max_rss_blog"]);
	$smarty->assign("max_rss_blog", $_REQUEST["max_rss_blog"]);
	$tikilib->set_preference('max_rss_forum', $_REQUEST["max_rss_forum"]);
	$smarty->assign("max_rss_forum", $_REQUEST["max_rss_forum"]);
	$tikilib->set_preference('max_rss_forums', $_REQUEST["max_rss_forums"]);
	$smarty->assign("max_rss_forums", $_REQUEST["max_rss_forums"]);
	$tikilib->set_preference('max_rss_mapfiles', $_REQUEST["max_rss_mapfiles"]);
	$smarty->assign("max_rss_mapfiles", $_REQUEST["max_rss_mapfiles"]);

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
	$smarty->assign("max_rss_directories", $tikilib->get_preference("max_rss_directories", 10));
	$smarty->assign("max_rss_articles", $tikilib->get_preference("max_rss_articles", 10));
	$smarty->assign("max_rss_wiki", $tikilib->get_preference("max_rss_wiki", 10));
	$smarty->assign("max_rss_blog", $tikilib->get_preference("max_rss_blog", 10));
	$smarty->assign("max_rss_blogs", $tikilib->get_preference("max_rss_blogs", 10));
	$smarty->assign("max_rss_forums", $tikilib->get_preference("max_rss_forums", 10));
	$smarty->assign("max_rss_forum", $tikilib->get_preference("max_rss_forum", 10));
	$smarty->assign("max_rss_mapfiles", $tikilib->get_preference("max_rss_mapfiles", 10));
	$smarty->assign("max_rss_file_galleries", $tikilib->get_preference("max_rss_file_galleries", 10));
	$smarty->assign("max_rss_image_galleries", $tikilib->get_preference("max_rss_image_galleries", 10));
	$smarty->assign("max_rss_file_gallery", $tikilib->get_preference("max_rss_file_gallery", 10));
	$smarty->assign("max_rss_image_gallery", $tikilib->get_preference("max_rss_image_gallery", 10));

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
