<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// application to display an image from the database with 
// option to resize the image dynamically creating a thumbnail on the fly.

// This handles 4 types of images, depending on the image_type parameter: 
// "article": Images for articles
// "submission": Images for article submissions
// "topic": Images for topics associated to articles
// "preview": Special case used for article previews. A recently uploaded custom article image. Not cached nor generated here. See tiki-edit_article.php
// Any other value is invalid
// If image_type has no value, we default to "article" to preserve previous behaviour

require_once ('tiki-setup.php');
$artlib = TikiLib::lib('art');

$access->check_feature('feature_articles');

// Now check permissions to access this page
$access->check_permission_either(array('tiki_p_read_article','tiki_p_articles_read_heading'));

if (!isset($_REQUEST["id"])) {
	die;
}
$useCache = isset($_REQUEST['cache']) && $_REQUEST['cache'] == 'y'; // cache only the image in list mode

// If image_type has no value, we default to "article" to preserve previous behaviour
if (!isset($_REQUEST["image_type"])) {
	$_REQUEST["image_type"]="article";
}

switch ($_REQUEST["image_type"]) {
	case "article":
		$imagePrefix="article";
    	break;
	case "submission":
		$imagePrefix="article_submission";
    	break;
	case "topic":
		$imagePrefix="article_topic";
    	break;
	case "preview":
		$imagePrefix="article_preview";
    	break;
	default:
		die;
}
$temporaryFile = $prefs['tmpDir'];
if ($tikidomain) { 
	$temporaryFile .= "/$tikidomain";
}
$temporaryFile .= "/$imagePrefix." . $_REQUEST["id"];


if ($imagePrefix == "article_preview" && !is_file($temporaryFile)) {
	header("HTTP/1.1 404 Not Found");
} elseif ( $imagePrefix != "article_preview" && (isset($_REQUEST["reload"]) || !$useCache) || !is_file($temporaryFile) ) {
	// If "reload" parameter is set, recreate the cached image file from database values.
	
	switch ($_REQUEST["image_type"]) {
		case "article":
			$storedData = $artlib->get_article_image($_REQUEST["id"]);
    		break;
		case "submission":
			$storedData = $artlib->get_submission($_REQUEST["id"]);
    		break;
		case "topic":
			$storedData = $artlib->get_topic_image($_REQUEST["id"]);
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
	$data =& $storedData["image_data"];
	header("Content-type: ".$type);
	if (!empty($_REQUEST['width'])) {
		require_once('lib/images/images.php');
		$image = new Image($data);
		if ($image->get_width() > $_REQUEST['width']) {
			$image->resize($_REQUEST['width'], 0);
			$data = $image->display();
		}
		if (empty($data)) die;
	}
	if ($useCache && $data) {
		$fp = fopen($temporaryFile, "wb");
		fputs($fp, $data);
		fclose($fp);
	}
	echo $data;
} else {
	$size = getimagesize($temporaryFile);
	header("Content-type: ".$size['mime']);
	readfile($temporaryFile);
}
