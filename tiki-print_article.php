<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-print_article.php,v 1.21.2.1 2007-11-26 16:30:04 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'cms';
require_once ('tiki-setup.php');

include_once ('lib/articles/artlib.php');

if ($prefs['feature_cms_print'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_cms_print");

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["articleId"])) {
	$smarty->assign('msg', tra("No article indicated"));

	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["articleId"])) {
	$artlib->add_article_hit($_REQUEST["articleId"]);

	$smarty->assign('articleId', $_REQUEST["articleId"]);
	$article_data = $tikilib->get_article($_REQUEST["articleId"]);

	if ($article_data === false) {
		$smarty->assign('msg', tra('Permission denied'));
		$smarty->display('error.tpl');
		die;
	}
	if (!$article_data) {
		$smarty->assign('msg', tra("Article not found"));

		$smarty->display("error.tpl");
		die;
	}

	if (($article_data["publishDate"] > $tikilib->now) && ($tiki_p_admin != 'y')) {
		$smarty->assign('msg', tra("Article is not published yet"));

		$smarty->display("error.tpl");
		die;
	}

	$smarty->assign('title', $article_data["title"]);
	$smarty->assign('authorName', $article_data["authorName"]);
	$smarty->assign('topicId', $article_data["topicId"]);
	$smarty->assign('useImage', $article_data["useImage"]);
	$smarty->assign('image_name', $article_data["image_name"]);
	$smarty->assign('image_type', $article_data["image_type"]);
	$smarty->assign('image_size', $article_data["image_size"]);
	$smarty->assign('image_data', urlencode($article_data["image_data"]));
	$smarty->assign('reads', $article_data["nbreads"]);
	$smarty->assign('size', $article_data["size"]);

	if (strlen($article_data["image_data"]) > 0) {
		$smarty->assign('hasImage', 'y');

		$hasImage = 'y';
	}

	$smarty->assign('heading', $article_data["heading"]);
	$smarty->assign('body', $article_data["body"]);
	$smarty->assign('publishDate', $article_data["publishDate"]);
	$smarty->assign('edit_data', 'y');

	$body = $article_data["body"];
	$heading = $article_data["heading"];
	$smarty->assign('parsed_body', $tikilib->parse_data($body));
	$smarty->assign('parsed_heading', $tikilib->parse_data($heading));
}

ask_ticket('print-article');

include_once ('tiki-section_options.php');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('print_page', 'y');
$smarty->display("tiki-print_article.tpl");

?>
