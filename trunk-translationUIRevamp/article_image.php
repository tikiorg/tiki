<?php

// $Id: /cvsroot/tikiwiki/tiki/article_image.php,v 1.18.2.1 2008-03-01 17:12:54 leyan Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// application to display an image from the database with 
// option to resize the image dynamically creating a thumbnail on the fly.

// This handles three types of images, depending on the image_type parameter: 
// "article": Images for articles
// "submission": Images for article submissions
// "topic": Images for topics associated to articles
// "preview": Images for article and article submissions previews
// Any other value is invalid
// If image_type has no value, we default to "article" to preserve previous behaviour

require_once ('tiki-setup.php');

if ($prefs['feature_articles'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_articles");

	$smarty->display("error.tpl");
	die;
}

// Now check permissions to access this page
if(($tiki_p_read_article != 'y') && ($tiki_p_articles_read_heading != 'y')) {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg',tra("Permission denied. You cannot view pages"));
	$smarty->display("error.tpl");
	die;  
}

if (!isset($_REQUEST["id"])) {
	die;
}

// If image_type has no value, we default to "article" to preserve previous behaviour
if(!isset($_REQUEST["image_type"])) {
	$_REQUEST["image_type"]="article";
}

switch ($_REQUEST["image_type"]) {
	case "article":
		$image_cache_prefix="article";
		break;
	case "submission":
		$image_cache_prefix="article_submission";
		break;
	case "topic":
		$image_cache_prefix="article_topic";
		break;
	case "preview":
		$image_cache_prefix="article_preview";
		break;
	default:
		die;
}

$cachefile = $prefs['tmpDir'];
if ($tikidomain) { $cachefile.= "/$tikidomain"; }
$cachefile.= "/$image_cache_prefix.".$_REQUEST["id"];

// If "reload" parameter is set, recreate the cached image file from database values.
// This does not make sense if "image_type" is "preview".
if ( (isset($_REQUEST["reload"])) || (!is_file($cachefile))) {
	switch ($_REQUEST["image_type"]) {
		case "article":
			$storedData = $tikilib->get_article_image($_REQUEST["id"]);
			break;
		case "submission":
			$storedData = $tikilib->get_submission($_REQUEST["id"]);
			break;
		case "topic":
			$storedData = $tikilib->get_topic_image($_REQUEST["id"]);
			break;
		case "preview":
			// We can't get the data from the database. No fallback solution.
			// No image displayed
			break;
		default:
			// Invalid value
			die;
	}
	// if blank then die, otherwise we offer to download strangeness
	// this also catches invalid id's
	if (!$storedData) {
		die;
	}
	$type = $storedData["image_type"];
	$data = $storedData["image_data"];
	if ($data["image_data"]) {
		$fp = fopen($cachefile,"wb");
		fputs($fp,$data);
		fclose($fp);
	}
}

// If cached file exists, display cached file
if (is_file($cachefile)) {
	$size = getimagesize($cachefile);
	header ("Content-type: ".$size['mime']);
	readfile($cachefile);
} else {
	// Just in case creation of cache file failed, but data was
	// retrieved from database
	header ("Content-type: ".$type);
	echo $data;
}
