<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_rss.php,v 1.3 2003-08-07 04:33:56 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
if (isset($_REQUEST["rss"])) {
	$tikilib->set_preference('max_rss_articles', $_REQUEST["max_rss_blogs"]);

	$smarty->assign("max_rss_blogs", $_REQUEST["max_rss_blogs"]);
	$tikilib->set_preference('max_rss_image_galleries', $_REQUEST["max_rss_image_galleries"]);
	$smarty->assign("max_rss_image_galleries", $_REQUEST["max_rss_image_galleries"]);
	$tikilib->set_preference('max_rss_file_galleries', $_REQUEST["max_rss_file_galleries"]);
	$smarty->assign("max_rss_file_galleries", $_REQUEST["max_rss_file_galleries"]);
	$tikilib->set_preference('max_rss_image_gallery', $_REQUEST["max_rss_image_gallery"]);
	$smarty->assign("max_rss_image_gallerys", $_REQUEST["max_rss_image_gallery"]);
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
	$smarty->assign("max_rss_articles", $tikilib->get_preference("max_rss_articles", 10));

	$smarty->assign("max_rss_wiki", $tikilib->get_preference("max_rss_wiki", 10));
	$smarty->assign("max_rss_blog", $tikilib->get_preference("max_rss_blog", 10));
	$smarty->assign("max_rss_blogs", $tikilib->get_preference("max_rss_blogs", 10));
	$smarty->assign("max_rss_forums", $tikilib->get_preference("max_rss_forums", 10));
	$smarty->assign("max_rss_forum", $tikilib->get_preference("max_rss_forum", 10));
	$smarty->assign("max_rss_file_galleries", $tikilib->get_preference("max_rss_file_galleries", 10));
	$smarty->assign("max_rss_image_galleries", $tikilib->get_preference("max_rss_image_galleries", 10));
	$smarty->assign("max_rss_file_gallery", $tikilib->get_preference("max_rss_file_gallery", 10));
	$smarty->assign("max_rss_image_gallery", $tikilib->get_preference("max_rss_image_gallery", 10));
}

?>