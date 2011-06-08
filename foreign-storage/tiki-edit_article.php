<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'cms';
require_once ('tiki-setup.php');
include_once ('lib/categories/categlib.php');
include_once ('lib/articles/artlib.php');

$smarty->assign('headtitle',tra('Edit article'));

if ($prefs['feature_freetags'] == 'y') {
	global $freetaglib;
	include_once('lib/freetag/freetaglib.php');
}
$access->check_feature('feature_articles');

if ($tiki_p_admin != 'y') {
	if ($tiki_p_use_HTML != 'y') {
		$_REQUEST["allowhtml"] = 'off';
	}
}

if (!empty($_REQUEST['articleId'])) {
	$articleId = $_REQUEST['articleId'];
	$article_data = $artlib->get_article($_REQUEST['articleId']);
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
} else {
	$articleId = 0;
}

if (isset($_REQUEST['cancel_edit'])) {
	if (empty($articleId)) {
		header('localtion: tiki-view_articles.php');
		die;
	}
	include_once('tiki-sefurl.php');
	header ('location: '.filter_out_sefurl("tiki-read_article.php?articleId=$articleId", $smarty, 'article', $artice_data['title']));
	die;
}

// We need separate numbering of previews, since we access preview images by this number
if (isset($_REQUEST["previewId"])) {
	$previewId = $_REQUEST["previewId"];
} else {
	$previewId = rand();
}

$smarty->assign('articleId', $articleId);
$smarty->assign('previewId', $previewId);
$smarty->assign('imageIsChanged', (isset($_REQUEST['imageIsChanged']) && $_REQUEST['imageIsChanged']=='y')?'y':'n');

if (isset($_REQUEST["templateId"]) && $_REQUEST["templateId"] > 0) {
	global $templateslib; require_once 'lib/templates/templateslib.php';
	$template_data = $templateslib->get_template($_REQUEST["templateId"]);

	$_REQUEST["preview"] = 1;
	$_REQUEST["body"] = $template_data["content"];
}

$smarty->assign('allowhtml', '');
$publishDate = $tikilib->now;
$cur_time = explode(',', $tikilib->date_format('%Y,%m,%d,%H,%M,%S', $publishDate));
$expireDate = $tikilib->make_time($cur_time[3], $cur_time[4], $cur_time[5], $cur_time[1], $cur_time[2], $cur_time[0]+1);

//Use 12- or 24-hour clock for $publishDate time selector based on admin and user preferences
include_once ('lib/userprefs/userprefslib.php');
$smarty->assign('use_24hr_clock', $userprefslib->get_user_clock_pref($user));

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
$smarty->assign('image_data', '');
$smarty->assign('image_x', $prefs['article_image_size_x']);
$smarty->assign('image_y', $prefs['article_image_size_y']);
$smarty->assign('heading', '');
$smarty->assign('body', '');
$smarty->assign('author', '');
$smarty->assign('rating', 7);
$smarty->assign('edit_data', 'n');
$smarty->assign('emails', '');
$smarty->assign('userEmail', $userlib->get_user_email($user));
$smarty->assign('ispublished', '');

// If the articleId is passed then get the article data
// GGG - You have to check for the actual value of the articleId because it
//  will be 0 when you select preview while creating a new article.
if (isset($_REQUEST["articleId"]) and $_REQUEST["articleId"] > 0) {

		$cat_lang = $article_data["lang"];
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
	$smarty->assign('list_image_x', $article_data['list_image_x']);
	$smarty->assign('reads', $article_data["nbreads"]);
	$smarty->assign('type', $article_data["type"]);
	$smarty->assign('author', $article_data["author"]);
	$smarty->assign('creator_edit', $article_data["creator_edit"]);
	$smarty->assign('rating', $article_data["rating"]);
	$smarty->assign('ispublished', $article_data["ispublished"]);

	if (strlen($article_data["image_data"]) > 0) {
		$smarty->assign('hasImage', 'y');

		$hasImage = 'y';
	}

	$smarty->assign('heading', $article_data["heading"]);
	$smarty->assign('body', $article_data["body"]);
	$smarty->assign('edit_data', 'y');

	$data = $article_data["image_data"];
	$imgname = $article_data["image_name"];

	$body = $article_data["body"];
	$heading = $article_data["heading"];
	$smarty->assign('parsed_body', $tikilib->parse_data($body));
	$smarty->assign('parsed_heading', $tikilib->parse_data($heading));
}

// Now check permissions to access this page
// echo $tiki_p_edit_article.$article_data["author"].$article_data["creator_edit"];
if ($tiki_p_admin_cms != 'y' && !$tikilib->user_has_perm_on_object($user, $articleId, 'article', 'tiki_p_edit_article') and ($article_data["author"] != $user or $article_data["creator_edit"] != 'y')) {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to edit this article"));

	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["allowhtml"])) {
	if ($_REQUEST["allowhtml"] == "on") {
		$smarty->assign('allowhtml', 'y');
	} else {
		$smarty->assign('allowhtml', 'n');
	}
}
if (isset($_REQUEST["ispublished"])) {
	if ($_REQUEST["ispublished"] == "on") {
		$smarty->assign('ispublished', 'y');
	} else {
		$smarty->assign('ispublished', 'n');
	}
}


$errors = array();
if (empty($_REQUEST['emails']) || $prefs['feature_cms_emails'] != 'y')
	$emails = '';
elseif (!empty($_REQUEST['emails'])) {
	$emails = explode(',', $_REQUEST['emails']);
	foreach ($emails as $email) {
		if (!validate_email($email, $prefs['validateEmail']))
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
	if (isset($_REQUEST["_Hour"])) {
		//Convert 12-hour clock hours to 24-hour scale to compute time
		if (!empty($_REQUEST['publish_Meridian'])) {
			$_REQUEST['publish_Hour'] = date('H', strtotime($_REQUEST['publish_Hour'] . ':00 ' . $_REQUEST['publish_Meridian']));
		}
		$publishDate = $tikilib->make_time($_REQUEST["publish_Hour"], $_REQUEST["publish_Minute"], 0, $_REQUEST["publish_Month"], $_REQUEST["publish_Day"], $_REQUEST["publish_Year"]);
	} else {
		$publishDate = $tikilib->now;
	}
	if (isset($_REQUEST["expire_Hour"])) {
	//Convert 12-hour clock hours to 24-hour scale to compute time
		if (!empty($_REQUEST['expire_Meridian'])) {
			$_REQUEST['expire_Hour'] = date('H', strtotime($_REQUEST['expire_Hour'] . ':00 ' . $_REQUEST['expire_Meridian']));
		}
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
	if (!isset($_REQUEST['from'])) $_REQUEST['from'] = '';

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
	$smarty->assign('image_x', $_REQUEST['list_image_x']);
	$smarty->assign('useImage', $useImage);
	$smarty->assign('isfloat', $isfloat);
	$smarty->assign('type', $_REQUEST["type"]);
	$smarty->assign('rating', $_REQUEST["rating"]);
	$smarty->assign('entrating', floor($_REQUEST["rating"]));
	$smarty->assign_by_ref('emails', $_REQUEST['emails']);
	$smarty->assign_by_ref('from', $_REQUEST['from']);
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
		// Create preview cache image, for display afterwards
		$cachefile = $prefs['tmpDir'];
		if ($tikidomain) { $cachefile.= "/$tikidomain"; }
		$cachefile.= "/article_preview.".$previewId;
		if (move_uploaded_file($_FILES['userfile1']['tmp_name'], $cachefile)) {
			$smarty->assign('imageIsChanged', 'y');
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
		//Convert 12-hour clock hours to 24-hour scale to compute time
		if (!empty($_REQUEST['publish_Meridian'])) {
			$_REQUEST['publish_Hour'] = date('H', strtotime($_REQUEST['publish_Hour'] . ':00 ' . $_REQUEST['publish_Meridian']));
		}
		$publishDate = $tikilib->make_time($_REQUEST["publish_Hour"], $_REQUEST["publish_Minute"], 0, $_REQUEST["publish_Month"], $_REQUEST["publish_Day"], $_REQUEST["publish_Year"]);
	} else {
		$publishDate = $tikilib->now;
	}
	if (isset($_REQUEST["expire_Hour"])) {
	//Convert 12-hour clock hours to 24-hour scale to compute time
		if (!empty($_REQUEST['expire_Meridian'])) {
			$_REQUEST['expire_Hour'] = date('H', strtotime($_REQUEST['expire_Hour'] . ':00 ' . $_REQUEST['expire_Meridian']));
		}
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
		$file_name = $_FILES['userfile1']['name'];
		$file_tmp_name = $_FILES['userfile1']['tmp_name'];
		$tmp_dest = $prefs['tmpDir'] . "/" . $file_name.".tmp";
		if (!move_uploaded_file($file_tmp_name, $tmp_dest)) {
			$smarty->assign('msg', tra('Errors detected'));
			$smarty->display("error.tpl");
			die();
	  }
    $fp = fopen($tmp_dest, "rb");
		$imgdata = fread($fp, filesize($tmp_dest));
		fclose ($fp);
		if(is_file($tmp_dest)) {
			@unlink($tmp_dest);
		}
		$imgtype = $_FILES['userfile1']['type'];
		$imgsize = $_FILES['userfile1']['size'];
		$imgname = $_FILES['userfile1']['name'];
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
	if($_REQUEST['ispublished'] == "on")
		$ispublished = 'y';
	else
		$ispublished = 'n';
	$_REQUEST['title'] = strip_tags($_REQUEST["title"], '<a><pre><p><img><hr><b><i>');
	$artid = $artlib->replace_article($_REQUEST['title']
																	, $_REQUEST["authorName"]
																		, $_REQUEST["topicId"]
																		, $useImage
																		, $imgname
																		, $imgsize
																		, $imgtype
																		, $imgdata
																		, $heading
																		, $body
																		, $publishDate
																		, $expireDate
																		, $user
																		, $articleId
																		, $_REQUEST["image_x"]
																		, $_REQUEST["image_y"]
																		, $_REQUEST["type"]
																		, $_REQUEST["topline"]
																		, $_REQUEST["subtitle"]
																		, $_REQUEST["linkto"]
																		, $_REQUEST["image_caption"]
																		, $_REQUEST["lang"]
																		, $_REQUEST["rating"]
																		, $isfloat
																		, $emails
																		, $_REQUEST['from']
																		, $_REQUEST['list_image_x']
																		, $ispublished
																		);
	$cat_type = 'article';
	$cat_objid = $artid;
	$cat_desc = substr($_REQUEST["heading"], 0, 200);
	$cat_name = $_REQUEST["title"];
	$cat_object_exists = (bool) $artid;
	$cat_lang = $_REQUEST['lang'];
	$cat_href = "tiki-read_article.php?articleId=" . $cat_objid;
	include_once("categorize.php");
	include_once ("freetag_apply.php");
	// Add attributes
	if ($prefs["article_custom_attributes"] == 'y') {
		 $valid_att = $artlib->get_article_type_attributes($_REQUEST["type"]);
		 $attributeArray = array();
		 foreach ($valid_att as $att) {
		 	// need to convert . to _ for matching
		 	$toMatch = str_replace('.', '_', $att["itemId"]);
		 	if (isset($_REQUEST[$toMatch])) {
		 		$attributeArray[$att["itemId"]] = $_REQUEST[$toMatch];
		 	}	
		 }
		 $artlib->set_article_attributes($artid, $attributeArray);
	}

	if ($prefs['geo_locate_article'] == 'y' && ! empty($_REQUEST['geolocation'])) {
		TikiLib::lib('geo')->set_coordinates('article', $artid, $_REQUEST['geolocation']);
	}

	// Remove image cache because image may have changed, and we
	// don't want to show the old image
	@$artlib->delete_image_cache("article",$_REQUEST["id"]);
	// Remove preview cache because it won't be used any more
	@$artlib->delete_image_cache("preview",$previewId);

	include_once('tiki-sefurl.php');
	header ('location: '.	filter_out_sefurl("tiki-read_article.php?articleId=$artid", $smarty, 'article', $_REQUEST['title']));
}
$smarty->assign_by_ref('errors', $errors);

// Set date to today before it's too late
$_SESSION["thedate"] = $tikilib->now;

// Armar un select con los topics
$topics = $artlib->list_topics();
$smarty->assign_by_ref('topics', $topics);

// get list of valid types
$types = $artlib->list_types_byname();
if (empty($article_data)) {
if (array($types)) {
	foreach ($types as $type=>$val) {
		break;
	}
} else {
	$type = '';
}
$smarty->assign('type', $type);
}
if ($prefs["article_custom_attributes"] == 'y') {
	$article_attributes = $artlib->get_article_attributes($_REQUEST["articleId"]);	
	$smarty->assign('article_attributes', $article_attributes);
	$all_attributes = array();
	$js_string = '';
	foreach($types as &$t) {
		// javascript needs htmlid to show/hide to be properties of basic array
		$type_attributes = $artlib->get_article_type_attributes($t["type"]);
		$all_attributes = array_merge($all_attributes, $type_attributes);
		foreach ($type_attributes as $att) {
			$htmlid = str_replace('.','_',$att['itemId']);
			$t[$htmlid] = 'y';
			$js_string .= "'$htmlid', 'y', ";
		}
	}
	$smarty->assign('all_attributes', $all_attributes);	
	$headerlib->add_js("articleCustomAttributes = new Array(); articleCustomAttributes = [$js_string];");
}
$smarty->assign_by_ref('types', $types);

if ($prefs['feature_cms_templates'] == 'y' && $tiki_p_use_content_templates == 'y') {
	global $templateslib; require_once 'lib/templates/templateslib.php';
	$templates = $templateslib->list_templates('cms', 0, -1, 'name_asc', '');
}

$smarty->assign_by_ref('templates', $templates["data"]);

if ($prefs['feature_multilingual'] == 'y') {
	$languages = array();
	$languages = $tikilib->list_languages();
	$smarty->assign_by_ref('languages', $languages);
}

if( $prefs['geo_locate_article'] == 'y' ) {
	$smarty->assign('geolocation_string', TikiLib::lib('geo')->get_coordinates_string('article', $articleId));
}

$cat_type = 'article';
$cat_objid = $articleId;
$cat_object_exists = (bool) $articleId;
include_once ("categorize_list.php");

if ($prefs['feature_freetags'] == 'y') {
    include_once ("freetag_list.php");
    if (isset($_REQUEST['preview'])) {
	$smarty->assign('taglist',$_REQUEST["freetag_string"]);
    }
}

$smarty->assign('publishDate', $publishDate);
$smarty->assign('publishDateSite', $publishDate);
$smarty->assign('expireDate', $expireDate);
$smarty->assign('expireDateSite', $expireDate);
$smarty->assign('siteTimeZone', $prefs['display_timezone']);

include_once ('tiki-section_options.php');

global $wikilib; include_once('lib/wiki/wikilib.php');
$plugins = $wikilib->list_plugins(true, 'body');
$smarty->assign_by_ref('plugins', $plugins);

ask_ticket('edit-article');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the Index Template
$smarty->assign('mid', 'tiki-edit_article.tpl');
$smarty->display("tiki.tpl");
