<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-edit_article.php,v 1.71.2.4 2007-11-26 16:21:07 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'cms';
require_once ('tiki-setup.php');
include_once ('lib/categories/categlib.php');
include_once ('lib/articles/artlib.php');

$smarty->assign('headtitle',tra('Edit article'));

if ($prefs['feature_freetags'] == 'y') {
	global $freetaglib;include_once('lib/freetag/freetaglib.php');
}

if ($prefs['feature_articles'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_articles");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin != 'y') {
	if ($tiki_p_use_HTML != 'y') {
		$_REQUEST["allowhtml"] = 'off';
	}
}

if (isset($_REQUEST["articleId"])) {
	$articleId = $_REQUEST["articleId"];
} else {
	$articleId = 0;
}

$smarty->assign('articleId', $articleId);

if (isset($_REQUEST["templateId"]) && $_REQUEST["templateId"] > 0) {
	$template_data = $tikilib->get_template($_REQUEST["templateId"]);

	$_REQUEST["preview"] = 1;
	$_REQUEST["body"] = $template_data["content"];
}

$smarty->assign('allowhtml', 'on');
$publishDate = $tikilib->now;
$cur_time = explode(',', $tikilib->date_format('%Y,%m,%d,%H,%M,%S', $publishDate));
$expireDate = $tikilib->make_time($cur_time[3], $cur_time[4], $cur_time[5], $cur_time[1], $cur_time[2], $cur_time[0]+1);
$smarty->assign('title', '');
$smarty->assign('topline', '');
$smarty->assign('subtitle', '');
$smarty->assign('linkto', '');
$smarty->assign('image_caption', '');
$smarty->assign('lang', $prefs['language']);
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
$smarty->assign('author', '');
$smarty->assign('type', 'Article');
$smarty->assign('rating', 7);
$smarty->assign('edit_data', 'n');
$smarty->assign('emails', '');

// If the articleId is passed then get the article data
// GGG - You have to check for the actual value of the articleId because it
//  will be 0 when you select preview while creating a new article. You
//  really do not want to do $tikilib->get_article if the articleId is 0
if (isset($_REQUEST["articleId"]) and $_REQUEST["articleId"] > 0) {
	$article_data = $tikilib->get_article($_REQUEST["articleId"]);
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

	$publishDate = $article_data["publishDate"];
	$expireDate = $article_data["expireDate"];
	$smarty->assign('title', $article_data["title"]);
  $smarty->assign('topline', $article_data["topline"]);
  $smarty->assign('subtitle', $article_data["subtitle"]);
  $smarty->assign('linkto', $article_data["linkto"]);
  $smarty->assign('image_caption', $article_data["image_caption"]);
  $smarty->assign('lang', $article_data["lang"]);
	$smarty->assign('authorName', $article_data["authorName"]);
	$smarty->assign('topicId', $article_data["topicId"]);
	$smarty->assign('useImage', $article_data["useImage"]);
	$smarty->assign('isfloat', $article_data["isfloat"]);
	$smarty->assign('image_name', $article_data["image_name"]);
	$smarty->assign('image_type', $article_data["image_type"]);
	$smarty->assign('image_size', $article_data["image_size"]);
	$smarty->assign('image_data', urlencode($article_data["image_data"]));
	$smarty->assign('image_x', $article_data["image_x"]);
	$smarty->assign('image_y', $article_data["image_y"]);
	$smarty->assign('reads', $article_data["nbreads"]);
	$smarty->assign('type', $article_data["type"]);
	$smarty->assign('author', $article_data["author"]);
	$smarty->assign('creator_edit', $article_data["creator_edit"]);
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
		$smarty->assign('tempimg', 'article_image.php?id='.$_REQUEST["articleId"]);
	} else {
		$smarty->assign('tempimg', 'n');
	}

	$body = $article_data["body"];
	$heading = $article_data["heading"];
	$smarty->assign('parsed_body', $tikilib->parse_data($body));
	$smarty->assign('parsed_heading', $tikilib->parse_data($heading));
}

// Now check permissions to access this page
// echo $tiki_p_edit_article.$article_data["author"].$article_data["creator_edit"];
if ($tiki_p_admin_cms != 'y' && !$tikilib->user_has_perm_on_object($user, $articleId, 'article', 'tiki_p_edit_article') and ($article_data["author"] != $user or $article_data["creator_edit"] != 'y')) {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied you cannot edit this article"));

	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["allowhtml"])) {
	if ($_REQUEST["allowhtml"] == "on") {
		$smarty->assign('allowhtml', 'y');
	}
}

$errors = array();
if (empty($_REQUEST['emails']) || $prefs['feature_cms_emails'] != 'y')
	$emails = '';
elseif (!empty($_REQUEST['emails'])) {
	$emails = split(',', $_REQUEST['emails']);
	foreach ($emails as $email) {
		if (!validate_email($email, 'y'))
			$errors[] = tra('Invalid email:').' '.$email;
	}
}

if (isset($_REQUEST["preview"]))
	$smarty->assign('preview', 1);
else
	$smarty->assign('preview', 0);

// If we are in preview mode then preview it!
if (isset($_REQUEST["preview"]) or !empty($errors)) {
	# convert from the displayed 'site' time to 'server' time
	if (isset($_REQUEST["publish_Hour"])) {
	$publishDate = $tikilib->make_time($_REQUEST["publish_Hour"], $_REQUEST["publish_Minute"], 0, $_REQUEST["publish_Month"], $_REQUEST["publish_Day"], $_REQUEST["publish_Year"]);
	} else {
		$publishDate = $tikilib->now;
	}
	if (isset($_REQUEST["expire_Hour"])) {
	$expireDate = $tikilib->make_time($_REQUEST["expire_Hour"], $_REQUEST["expire_Minute"], 0, $_REQUEST["expire_Month"], $_REQUEST["expire_Day"], $_REQUEST["expire_Year"]);
	} else {
		$expireDate = $publishDate;
	}

	$smarty->assign('reads', '0');
	$smarty->assign('edit_data', 'y');
	$smarty->assign('title', strip_tags($_REQUEST["title"], '<a><pre><p><img><hr><b><i>'));
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
	if (!isset($_REQUEST["topline"])) $_REQUEST['topline'] = '';
	if (!isset($_REQUEST["subtitle"])) $_REQUEST['subtitle'] = '';
	if (!isset($_REQUEST["linkto"])) $_REQUEST['linkto'] = '';
	if (!isset($_REQUEST["image_caption"])) $_REQUEST['image_caption'] = '';
	if (!isset($_REQUEST["lang"])) $_REQUEST['lang'] = '';
	if (!isset($_REQUEST["type"])) $_REQUEST['type'] = '';
	if (!isset($_REQUEST['emails'])) $_REQUEST['emails'] = '';

  $smarty->assign('topline', $_REQUEST["topline"]);
  $smarty->assign('subtitle', $_REQUEST["subtitle"]);
  $smarty->assign('linkto', $_REQUEST["linkto"]);
  $smarty->assign('image_caption', $_REQUEST["image_caption"]);
  $smarty->assign('lang', $_REQUEST["lang"]);
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
	$smarty->assign_by_ref('emails', $_REQUEST['emails']);
	$imgname = $_REQUEST["image_name"];
	$data = urldecode($_REQUEST["image_data"]);

	// Parse the information of an uploaded file and use it for the preview
	if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
		$fp = fopen($_FILES['userfile1']['tmp_name'], "rb");

		$data = fread($fp, filesize($_FILES['userfile1']['tmp_name']));
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
		$tmpfname = $prefs['tmpDir'] . "/articleimage" . "." . $_REQUEST["articleId"];
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
		$body = strip_tags($_REQUEST["body"], '<a><pre><p><img><hr><b><i>');

		$heading = strip_tags($_REQUEST["heading"], '<a><pre><p><img><hr><b><i>');
	}

	$smarty->assign('size', strlen($body));

	$parsed_body = $tikilib->parse_data($body);
	$parsed_heading = $tikilib->parse_data($heading);

	if ($prefs['cms_spellcheck'] == 'y') {
		if (isset($_REQUEST["spellcheck"]) && $_REQUEST["spellcheck"] == 'on') {
			$parsed_body = $tikilib->spellcheckreplace($body, $parsed_body, $prefs['language'], 'subbody');
			$parsed_heading = $tikilib->spellcheckreplace($heading, $parsed_heading, $prefs['language'], 'subheading');
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

if (isset($_REQUEST['save']) && empty($errors)) {
	check_ticket('edit-article');
	include_once ("lib/imagegals/imagegallib.php");

	# convert from the displayed 'site' time to 'server' time
	if (isset($_REQUEST["publish_Hour"])) {
	$publishDate = $tikilib->make_time($_REQUEST["publish_Hour"], $_REQUEST["publish_Minute"], 0, $_REQUEST["publish_Month"], $_REQUEST["publish_Day"], $_REQUEST["publish_Year"]);
	} else {
		$publishDate = $tikilib->now;
	}
	if (isset($_REQUEST["expire_Hour"])) {
	$expireDate = $tikilib->make_time($_REQUEST["expire_Hour"], $_REQUEST["expire_Minute"], 0, $_REQUEST["expire_Month"], $_REQUEST["expire_Day"], $_REQUEST["expire_Year"]);
	} else {
		$expireDate = $tikilib->now;
	}

	if (isset($_REQUEST["allowhtml"]) && $_REQUEST["allowhtml"] == "on") {
		$body = $_REQUEST["body"];

		$heading = $_REQUEST["heading"];
	} else {
		$body = strip_tags($_REQUEST["body"], '<a><pre><p><img><hr><b><i>');

		$heading = strip_tags($_REQUEST["heading"], '<a><pre><p><img><hr><b><i>');
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
		$topiccachefile = $prefs['tmpDir'];
		if ($tikidomain) { $topiccachefile.= "/$tikidomain"; }
		$topiccachefile.= "/article.".$_REQUEST["id"];
		@unlink($topiccachefile);
	}

	// Parse $edit and eliminate image references to external URIs (make them internal)
	$body = $imagegallib->capture_images($body);
	$heading = $imagegallib->capture_images($heading);

	if (!isset($_REQUEST["rating"])) $_REQUEST['rating'] = 0;
	if (!isset($_REQUEST['topicId']) || $_REQUEST['topicId'] == '') $_REQUEST['topicId'] = 0;

	if (!isset($_REQUEST["topline"])) $_REQUEST['topline'] = '';
	if (!isset($_REQUEST["subtitle"])) $_REQUEST['subtitle'] = '';
	if (!isset($_REQUEST["linkto"])) $_REQUEST['linkto'] = '';
	if (!isset($_REQUEST["image_caption"])) $_REQUEST['image_caption'] = '';
	if (!isset($_REQUEST["lang"])) $_REQUEST['lang'] = '';
	if (!isset($_REQUEST["type"])) $_REQUEST['type'] = '';

	if ($prefs['feature_multilingual'] == 'y' && $_REQUEST['lang'] && isset($article_data) && $article_data['lang'] != $_REQUEST["lang"]) {
		include_once("lib/multilingual/multilinguallib.php");
		if ($multilinguallib->updatePageLang('article', $article_data['articleId'], $_REQUEST["lang"], true)) {
			$_REQUEST['lang'] = $article_data['lang'];
			$smarty->assign('msg', tra("The language can't be changed as its set of translations has already this language"));
			$smarty->display("error.tpl");
			die;
		}
	}

	$artid = $artlib->replace_article(strip_tags($_REQUEST["title"], '<a><pre><p><img><hr><b><i>'), $_REQUEST["authorName"],
		$_REQUEST["topicId"], $useImage, $imgname, $imgsize, $imgtype, $imgdata, $heading, $body, $publishDate, $expireDate, $user,
		$articleId, $_REQUEST["image_x"], $_REQUEST["image_y"], $_REQUEST["type"], $_REQUEST["topline"], $_REQUEST["subtitle"],
		$_REQUEST["linkto"], $_REQUEST["image_caption"], $_REQUEST["lang"], $_REQUEST["rating"], $isfloat, $emails);

	$cat_type = 'article';
	$cat_objid = $artid;
	$cat_desc = substr($_REQUEST["heading"], 0, 200);
	$cat_name = $_REQUEST["title"];
	$cat_href = "tiki-read_article.php?articleId=" . $cat_objid;
	include_once("categorize.php");
	include_once ("freetag_apply.php");

	header ("location: tiki-read_article.php?articleId=$artid");
}
$smarty->assign_by_ref('errors', $errors);

// Set date to today before it's too late
$_SESSION["thedate"] = $tikilib->now;

// Armar un select con los topics
$topics = $artlib->list_topics();
$smarty->assign_by_ref('topics', $topics);

// get list of valid types
$types = $artlib->list_types_byname();
$smarty->assign_by_ref('types', $types);

if ($prefs['feature_cms_templates'] == 'y' && $tiki_p_use_content_templates == 'y') {
	$templates = $tikilib->list_templates('cms', 0, -1, 'name_asc', '');
}
$smarty->assign_by_ref('templates', $templates["data"]);

if ($prefs['feature_multilingual'] == 'y') {
	$languages = array();
	$languages = $tikilib->list_languages();
	$smarty->assign_by_ref('languages', $languages);
}

$cat_type = 'article';
$cat_objid = $articleId;
include_once ("categorize_list.php");

if ($prefs['feature_freetags'] == 'y') {
    include_once ("freetag_list.php");
    if ($_REQUEST["preview"]) {
	$smarty->assign('taglist',$_REQUEST["freetag_string"]);
    }
}

$smarty->assign('publishDate', $publishDate);
$smarty->assign('publishDateSite', $publishDate);
$smarty->assign('expireDate', $expireDate);
$smarty->assign('expireDateSite', $expireDate);
$smarty->assign('siteTimeZone', $prefs['display_timezone']);

include_once ('tiki-section_options.php');

include_once("textareasize.php");

global $wikilib; include_once('lib/wiki/wikilib.php');
$plugins = $wikilib->list_plugins(true);
$smarty->assign_by_ref('plugins', $plugins);

include_once ('lib/quicktags/quicktagslib.php');
$quicktags = $quicktagslib->list_quicktags(0,100,'taglabel_desc','','articles');
$smarty->assign_by_ref('quicktags', $quicktags["data"]);

ask_ticket('edit-article');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the Index Template
$smarty->assign('mid', 'tiki-edit_article.tpl');
$smarty->display("tiki.tpl");

?>
