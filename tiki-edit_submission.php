<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-edit_submission.php,v 1.36 2004-03-28 07:32:23 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/articles/artlib.php');

if ($feature_submissions != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_submissions");

	$smarty->display("error.tpl");
	die;
}

// Now check permissions to access this page
if ($tiki_p_submit_article != 'y') {
	$smarty->assign('msg', tra("Permission denied you cannot send submissions"));

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin != 'y') {
	if ($tiki_p_use_HTML != 'y') {
		$_REQUEST["allowhtml"] = 'off';
	}
}

if (isset($_REQUEST["subId"])) {
	$subId = $_REQUEST["subId"];
} else {
	$subId = 0;
}

$smarty->assign('subId', $subId);
$smarty->assign('articleId', $subId);
$smarty->assign('allowhtml', 'y');
$publishDate = date('U');
$expireDate = mktime (0,0,0,date("m"),  date("d"),  date("Y")+1);
$dc = &$tikilib->get_date_converter($user);
$smarty->assign('title', '');
$authorName = $tikilib->get_user_preference($user,'realName',$user);
$smarty->assign('authorName', $authorName);
$smarty->assign('topicId', '');
$smarty->assign('useImage', 'n');
$smarty->assign('isfloat', 'n');
$hasImage = 'n';
$smarty->assign('hasImage', 'n');
$smarty->assign('image_name', '');
$smarty->assign('image_type', '');
$smarty->assign('image_size', '');
$smarty->assign('image_x', 0);
$smarty->assign('image_y', 0);
$smarty->assign('heading', '');
$smarty->assign('body', '');
$smarty->assign('type', 'Article');
$smarty->assign('rating', 7);
$smarty->assign('edit_data', 'n');

if (isset($_REQUEST["templateId"]) && $_REQUEST["templateId"] > 0) {
	$template_data = $tikilib->get_template($_REQUEST["templateId"]);

	$_REQUEST["preview"] = 1;
	$_REQUEST["body"] = $template_data["content"];
}

// If the submissionId is passed then get the submission data
if (isset($_REQUEST["subId"])) {
	$article_data = $tikilib->get_submission($_REQUEST["subId"]);

	$publishDate = $article_data["publishDate"];
	$expireDate = $article_data["expireDate"];
	$smarty->assign('title', $article_data["title"]);
	$smarty->assign('authorName', $article_data["authorName"]);
	$smarty->assign('topicId', $article_data["topicId"]);
	$smarty->assign('useImage', $article_data["useImage"]);
	$smarty->assign('isfloat', $article_data["isfloat"]);
	$smarty->assign('image_name', $article_data["image_name"]);
	$smarty->assign('image_type', $article_data["image_type"]);
	$smarty->assign('image_size', $article_data["image_size"]);
	$smarty->assign('image_data', urlencode($article_data["image_data"]));
	$smarty->assign('reads', $article_data["reads"]);
	$smarty->assign('image_x', $article_data["image_x"]);
	$smarty->assign('image_y', $article_data["image_y"]);
	$smarty->assign('type', $article_data["type"]);
	$smarty->assign('rating', $article_data["rating"]);

	if (strlen($article_data["image_data"]) > 0) {
		$smarty->assign('hasImage', 'y');

		$hasImage = 'y';
	}

	$smarty->assign('heading', $article_data["heading"]);
	$smarty->assign('body', $article_data["body"]);
	$smarty->assign('edit_data', 'y');

	$data = $article_data["image_data"];
	$imgname = $article_data["image_name"];

	if ($hasImage == 'y') {
		$tmpfname = $tmpDir . "/articleimage" . "." . $_REQUEST["subId"];
		$fp = fopen($tmpfname, "wb");
		if ($fp) {
			fwrite($fp, $data);
			fclose ($fp);
			$smarty->assign('tempimg', $tmpfname);
		} else {
			$smarty->assign('tempimg', 'n');
		}
	}

	$body = $article_data["body"];
	$heading = $article_data["heading"];

	$parsed_body = $tikilib->parse_data($body);
	$parsed_heading = $tikilib->parse_data($heading);

	$smarty->assign('parsed_body', $parsed_body);
	$smarty->assign('parsed_heading', $parsed_heading);
}

if (isset($_REQUEST["subId"])) {
	if ($_REQUEST["subId"] > 0) {
		if ($tiki_p_edit_submission != 'y' and $article_data["author"] != $user) {
			$smarty->assign('msg', tra("Permission denied you cannot edit submissions"));

			$smarty->display("error.tpl");
			die;
		}
	}
}

if (isset($_REQUEST["allowhtml"])) {
	if ($_REQUEST["allowhtml"] == "on") {
		$smarty->assign('allowhtml', 'y');
	}
}

$smarty->assign('preview', 0);

// If we are in preview mode then preview it!
if (isset($_REQUEST["preview"])) {
	check_ticket('edit-submission'); 
	# convert from the displayed 'site' time to 'server' time
	$publishDate = $dc->getServerDateFromDisplayDate(mktime($_REQUEST["publish_Hour"], $_REQUEST["publish_Minute"],
		0, $_REQUEST["publish_Month"], $_REQUEST["publish_Day"], $_REQUEST["publish_Year"]));
	$expireDate = $dc->getServerDateFromDisplayDate(mktime($_REQUEST["expire_Hour"], $_REQUEST["expire_Minute"],
		0, $_REQUEST["expire_Month"], $_REQUEST["expire_Day"], $_REQUEST["expire_Year"]));

	$smarty->assign('reads', '0');
	$smarty->assign('preview', 1);
	$smarty->assign('edit_data', 'y');
	$smarty->assign('title', strip_tags($_REQUEST["title"], '<a><pre><p><img><hr>'));
	$smarty->assign('authorName', $_REQUEST["authorName"]);
	$smarty->assign('topicId', $_REQUEST["topicId"]);

	if (isset($_REQUEST["useImage"]) && $_REQUEST["useImage"] == 'on') {
		$useImage = 'y';
	} else {
		$useImage = 'n';
	}

	if (isset($_REQUEST["isfloat"]) && $_REQUEST["isfloat"] == 'on') {
		$isfloat = 'y';
	} else {
		$isfloat = 'n';
	}

	$smarty->assign('image_data', $_REQUEST["image_data"]);

	if (strlen($_REQUEST["image_data"]) > 0) {
		$smarty->assign('hasImage', 'y');

		$hasImage = 'y';
	}

	$smarty->assign('image_name', $_REQUEST["image_name"]);
	$smarty->assign('image_type', $_REQUEST["image_type"]);
	$smarty->assign('image_size', $_REQUEST["image_size"]);
	$smarty->assign('image_x', $_REQUEST["image_x"]);
	$smarty->assign('image_y', $_REQUEST["image_y"]);
	$smarty->assign('useImage', $useImage);
	$smarty->assign('isfloat', $isfloat);
	$smarty->assign('type', $_REQUEST["type"]);
	$smarty->assign('rating', $_REQUEST["rating"]);
	$smarty->assign('entrating', floor($_REQUEST["rating"]));
	$imgname = $_REQUEST["image_name"];
	$data = urldecode($_REQUEST["image_data"]);

	// Parse the information of an uploaded file and use it for the preview
	if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
		
		$file_name = $_FILES['userfile1']['name'];
		$file_tmp_name = $_FILES['userfile1']['tmp_name'];
		$tmp_dest = $tmpDir . "/" . $file_name;
		if (!move_uploaded_file($file_tmp_name, $tmp_dest)) {
			$smarty->assign('msg', tra('Errors detected'));
			$smarty->display("error.tpl");
			die();
		}

		$fp = fopen($tmp_dest, "rb");
		$data = fread($fp, filesize($tmp_dest));
		
		fclose ($fp);
		$imgtype = $_FILES['userfile1']['type'];
		$imgsize = $_FILES['userfile1']['size'];
		$imgname = $_FILES['userfile1']['name'];
		$smarty->assign('image_data', urlencode($data));
		$smarty->assign('image_name', $imgname);
		$smarty->assign('image_type', $imgtype);
		$smarty->assign('image_size', $imgsize);
		$hasImage = 'y';
		$smarty->assign('hasImage', 'y');
	}

	if ($hasImage == 'y') {
		$tmpfname = $tmpDir . "/articleimage" . "." . $_REQUEST["subId"];
		$fp = fopen($tmpfname, "wb");
		if ($fp) {
			fwrite($fp, $data);
			fclose ($fp);
			$smarty->assign('tempimg', $tmpfname);
		} else {
			$smarty->assign('tempimg', 'n');
		}
	}

	$smarty->assign('heading', $_REQUEST["heading"]);
	$smarty->assign('edit_data', 'y');

	if (isset($_REQUEST["allowhtml"]) && $_REQUEST["allowhtml"] == "on") {
		$body = $_REQUEST["body"];

		$heading = $_REQUEST["heading"];
	} else {
		$body = strip_tags($_REQUEST["body"], '<a><pre><p><img><hr>');

		$heading = strip_tags($_REQUEST["heading"], '<a><pre><p><img><hr>');
	}

	$smarty->assign('size', strlen($body));

	$parsed_body = $tikilib->parse_data($body);
	$parsed_heading = $tikilib->parse_data($heading);

	if ($cms_spellcheck == 'y') {
		if (isset($_REQUEST["spellcheck"]) && $_REQUEST["spellcheck"] == 'on') {
			$parsed_body = $tikilib->spellcheckreplace($body, $parsed_body, $language, 'subbody');

			$parsed_heading = $tikilib->spellcheckreplace($heading, $parsed_heading, $language, 'subheading');
			$smarty->assign('spellcheck', 'y');
		} else {
			$smarty->assign('spellcheck', 'n');
		}
	}

	$smarty->assign('parsed_body', $parsed_body);
	$smarty->assign('parsed_heading', $parsed_heading);

	$smarty->assign('body', $body);
	$smarty->assign('heading', $heading);
}

// Pro
if (isset($_REQUEST["save"])) {
	check_ticket('edit-submission'); 
	include_once ("lib/imagegals/imagegallib.php");

	# convert from the displayed 'site' time to 'server' time
	$publishDate = $dc->getServerDateFromDisplayDate(mktime($_REQUEST["publish_Hour"], $_REQUEST["publish_Minute"],
		0, $_REQUEST["publish_Month"], $_REQUEST["publish_Day"], $_REQUEST["publish_Year"]));
	$expireDate = $dc->getServerDateFromDisplayDate(mktime($_REQUEST["expire_Hour"], $_REQUEST["expire_Minute"],
		0, $_REQUEST["expire_Month"], $_REQUEST["expire_Day"], $_REQUEST["expire_Year"]));

	if (isset($_REQUEST["allowhtml"]) && $_REQUEST["allowhtml"] == "on") {
		$body = $_REQUEST["body"];

		$heading = $_REQUEST["heading"];
	} else {
		$body = strip_tags($_REQUEST["body"], '<a><pre><p><img><hr>');

		$heading = strip_tags($_REQUEST["heading"], '<a><pre><p><img><hr>');
	}

	if (isset($_REQUEST["useImage"]) && $_REQUEST["useImage"] == 'on') {
		$useImage = 'y';
	} else {
		$useImage = 'n';
	}

	if (isset($_REQUEST["isfloat"]) && $_REQUEST["isfloat"] == 'on') {
		$isfloat = 'y';
	} else {
		$isfloat = 'n';
	}

	$imgdata = urldecode($_REQUEST["image_data"]);

	if (strlen($imgdata) > 0) {
		$hasImage = 'y';
	}

	$imgname = $_REQUEST["image_name"];
	$imgtype = $_REQUEST["image_type"];
	$imgsize = $_REQUEST["image_size"];

	if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
		$fp = fopen($_FILES['userfile1']['tmp_name'], "rb");

		$imgdata = fread($fp, filesize($_FILES['userfile1']['tmp_name']));
		fclose ($fp);
		$imgtype = $_FILES['userfile1']['type'];
		$imgsize = $_FILES['userfile1']['size'];
		$imgname = $_FILES['userfile1']['name'];
	}

	// Parse $edit and eliminate image references to external URIs (make them internal)
	$body = $imagegallib->capture_images($body);
	$heading = $imagegallib->capture_images($heading);

	// If page exists
	if (!isset($_REQUEST["topicId"])) {
		$smarty->assign('msg', tra("You have to create a topic first"));

		$smarty->display("error.tpl");
		die;
	}

	$subid = $artlib->replace_submission(strip_tags($_REQUEST["title"], '<a><pre><p><img><hr>'), $_REQUEST["authorName"], $_REQUEST["topicId"], $useImage, $imgname, $imgsize, $imgtype, $imgdata, $heading, $body, $publishDate, $expireDate, $user, $subId, $_REQUEST["image_x"], $_REQUEST["image_y"], $_REQUEST["type"], $_REQUEST["rating"], $isfloat);
	/*                            
  $links = $tikilib->get_links($body);
  $notcachedlinks = $tikilib->get_links_nocache($body);
  $cachedlinks = array_diff($links, $notcachedlinks);
  $tikilib->cache_links($cachedlinks); 

  $links = $tikilib->get_links($heading);
  $notcachedlinks = $tikilib->get_links_nocache($heading);
  $cachedlinks = array_diff($links, $notcachedlinks);
  $tikilib->cache_links($cachedlinks); 
*/
	if ($tiki_p_autoapprove_submission == 'y') {
		$artlib->approve_submission($subid);

		header ("location: tiki-view_articles.php");
		die;
	}
	$cat_type = 'article';
	$cat_objid = $subId;
	$cat_desc = substr($_REQUEST["heading"], 0, 200);
	$cat_name = $_REQUEST["title"];
	$cat_href = "tiki-read_article.php?articleId=" . $cat_objid;
	include_once ("categorize.php");

	header ("location: tiki-list_submissions.php");
	die;
}

// Set date to today before it's too late
$_SESSION["thedate"] = date("U");

// Armar un select con los topics
$topics = $artlib->list_topics();
$smarty->assign_by_ref('topics', $topics);

$types = $artlib->list_types();
$smarty->assign_by_ref('types', $types);

if ($feature_cms_templates == 'y' && $tiki_p_use_content_templates == 'y') {
	$templates = $tikilib->list_templates('cms', 0, -1, 'name_asc', '');
}

$smarty->assign_by_ref('templates', $templates["data"]);

$cat_type = 'article';
$cat_objid = $subId;
include_once ("categorize_list.php");

$smarty->assign('publishDate', $publishDate);
$smarty->assign('publishDateSite', $dc->getDisplayDateFromServerDate($publishDate));
$smarty->assign('expireDate', $expireDate);
$smarty->assign('expireDateSite', $dc->getDisplayDateFromServerDate($expireDate));
$smarty->assign('siteTimeZone', $dc->getTzName());

include_once("textareasize.php");

include_once ('lib/quicktags/quicktagslib.php');
$quicktags = $quicktagslib->list_quicktags(0,100,'taglabel_desc','');
$smarty->assign_by_ref('quicktags', $quicktags["data"]);
ask_ticket('edit-submission');

// Display the Index Template
$smarty->assign('mid', 'tiki-edit_submission.tpl');
$smarty->assign('show_page_bar', 'n');
$smarty->display("tiki.tpl");

?>
