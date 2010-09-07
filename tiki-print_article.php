<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'cms';
require_once ('tiki-setup.php');
include_once ('lib/articles/artlib.php');
$access->check_feature('feature_cms_print');
if (!isset($_REQUEST["articleId"])) {
	$smarty->assign('msg', tra("No article indicated"));
	$smarty->display("error.tpl");
	die;
}
if (isset($_REQUEST["articleId"])) {
	$artlib->add_article_hit($_REQUEST["articleId"]);
	$smarty->assign('articleId', $_REQUEST["articleId"]);
	$article_data = $artlib->get_article($_REQUEST["articleId"]);
	$tikilib->get_perm_object($_REQUEST['articleId'], 'article');
	if ($article_data === false) {
		$smarty->assign('errortype', 401);
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
	$smarty->assign('image_x', $article_data["image_x"]);
	$smarty->assign('image_y', $article_data["image_y"]);
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
