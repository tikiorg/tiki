<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_cms.php,v 1.9 2004-03-29 21:26:28 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}

if (isset($_REQUEST["cmsfeatures"])) {
	check_ticket('admin-inc-cms');
	if (isset($_REQUEST["feature_cms_rankings"]) && $_REQUEST["feature_cms_rankings"] == "on") {
		$tikilib->set_preference("feature_cms_rankings", 'y');

		$smarty->assign("feature_cms_rankings", 'y');
	} else {
		$tikilib->set_preference("feature_cms_rankings", 'n');

		$smarty->assign("feature_cms_rankings", 'n');
	}

	if (isset($_REQUEST["cms_spellcheck"]) && $_REQUEST["cms_spellcheck"] == "on") {
		$tikilib->set_preference("cms_spellcheck", 'y');

		$smarty->assign("cms_spellcheck", 'y');
	} else {
		$tikilib->set_preference("cms_spellcheck", 'n');

		$smarty->assign("cms_spellcheck", 'n');
	}

	if (isset($_REQUEST["feature_article_comments"]) && $_REQUEST["feature_article_comments"] == "on") {
		$tikilib->set_preference("feature_article_comments", 'y');

		$smarty->assign("feature_article_comments", 'y');
	} else {
		$tikilib->set_preference("feature_article_comments", 'n');

		$smarty->assign("feature_article_comments", 'n');
	}

	if (isset($_REQUEST["feature_cms_templates"]) && $_REQUEST["feature_cms_templates"] == "on") {
		$tikilib->set_preference("feature_cms_templates", 'y');

		$smarty->assign("feature_cms_templates", 'y');
	} else {
		$tikilib->set_preference("feature_cms_templates", 'n');

		$smarty->assign("feature_cms_templates", 'n');
	}
}

if (isset($_REQUEST["cmsprefs"])) {
	check_ticket('admin-inc-cms');
	if (isset($_REQUEST["maxArticles"])) {
		$tikilib->set_preference("maxArticles", $_REQUEST["maxArticles"]);

		$smarty->assign('maxArticles', $_REQUEST["maxArticles"]);
	}
}

if (isset($_REQUEST["articlecomprefs"])) {
	check_ticket('admin-inc-cms');
	if (isset($_REQUEST["article_comments_per_page"])) {
		$tikilib->set_preference("article_comments_per_page", $_REQUEST["article_comments_per_page"]);

		$smarty->assign('article_comments_per_page', $_REQUEST["article_comments_per_page"]);
	}

	if (isset($_REQUEST["article_comments_default_ordering"])) {
		$tikilib->set_preference("article_comments_default_ordering", $_REQUEST["article_comments_default_ordering"]);

		$smarty->assign('article_comments_default_ordering', $_REQUEST["article_comments_default_ordering"]);
	}
}

if (isset($_REQUEST['artlist'])) {
	check_ticket('admin-inc-cms');
	if (isset($_REQUEST['art_list_title'])) {
		$tikilib->set_preference('art_list_title', 'y');
	} else {
		$tikilib->set_preference('art_list_title', 'n');
	}

	$smarty->assign('art_list_title', isset($_REQUEST['art_list_title']) ? 'y' : 'n');

	if (isset($_REQUEST['art_list_type'])) {
		$tikilib->set_preference('art_list_type', 'y');
	} else {
		$tikilib->set_preference('art_list_type', 'n');
	}

	$smarty->assign('art_list_type', isset($_REQUEST['art_list_type']) ? 'y' : 'n');

	if (isset($_REQUEST['art_list_topic'])) {
		$tikilib->set_preference('art_list_topic', 'y');
	} else {
		$tikilib->set_preference('art_list_topic', 'n');
	}

	$smarty->assign('art_list_topic', isset($_REQUEST['art_list_topic']) ? 'y' : 'n');

	if (isset($_REQUEST['art_list_date'])) {
		$tikilib->set_preference('art_list_date', 'y');
	} else {
		$tikilib->set_preference('art_list_date', 'n');
	}

	$smarty->assign('art_list_date', isset($_REQUEST['art_list_date']) ? 'y' : 'n');

	if (isset($_REQUEST['art_list_expire'])) {
		$tikilib->set_preference('art_list_expire', 'y');
	} else {
		$tikilib->set_preference('art_list_expire', 'n');
	}

	$smarty->assign('art_list_expire', isset($_REQUEST['art_list_expire']) ? 'y' : 'n');

	if (isset($_REQUEST['art_list_visible'])) {
		$tikilib->set_preference('art_list_visible', 'y');
	} else {
		$tikilib->set_preference('art_list_visible', 'n');
	}

	$smarty->assign('art_list_visible', isset($_REQUEST['art_list_visible']) ? 'y' : 'n');

	if (isset($_REQUEST['art_list_author'])) {
		$tikilib->set_preference('art_list_author', 'y');
	} else {
		$tikilib->set_preference('art_list_author', 'n');
	}

	$smarty->assign('art_list_author', isset($_REQUEST['art_list_author']) ? 'y' : 'n');

	if (isset($_REQUEST['art_list_reads'])) {
		$tikilib->set_preference('art_list_reads', 'y');
	} else {
		$tikilib->set_preference('art_list_reads', 'n');
	}

	$smarty->assign('art_list_reads', isset($_REQUEST['art_list_reads']) ? 'y' : 'n');

	if (isset($_REQUEST['art_list_size'])) {
		$tikilib->set_preference('art_list_size', 'y');
	} else {
		$tikilib->set_preference('art_list_size', 'n');
	}

	$smarty->assign('art_list_size', isset($_REQUEST['art_list_size']) ? 'y' : 'n');

	if (isset($_REQUEST['art_list_img'])) {
		$tikilib->set_preference('art_list_img', 'y');
	} else {
		$tikilib->set_preference('art_list_img', 'n');
	}

	$smarty->assign('art_list_img', isset($_REQUEST['art_list_img']) ? 'y' : 'n');

}
ask_ticket('admin-inc-cms');
?>
