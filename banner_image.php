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
if (!isset($_REQUEST["id"])) {
	die;
}

$id = (int) $_REQUEST['id'];
$defaultCache = 'temp';
$bannercachefile = "$defaultCache/banner.$id";

if (is_file($bannercachefile) and (!isset($_REQUEST["reload"]))) {
	$size = getimagesize($bannercachefile);
	$type = $size['mime'];

	header("Content-type: $type");
	readfile($bannercachefile);
	exit;
}

require_once ('tiki-setup.php');

$access->check_feature('feature_banners');

$bannercachefile = $prefs['tmpDir'];

if ($tikidomain) { 
	$bannercachefile .= "/$tikidomain"; 
}

$bannercachefile.= "/banner.". (int)$_REQUEST["id"];

if (is_file($bannercachefile) and (!isset($_REQUEST["reload"]))) {
	$size = getimagesize($bannercachefile);
	$type = $size['mime'];
} else {
	$bannerlib = TikiLib::lib('banner');
	$data = $bannerlib->get_banner($_REQUEST["id"]);
	if (!$data) {
		die;
	}
	$type = $data["imageType"];
	$data = $data["imageData"];
	if ($data) {
		$fp = fopen($bannercachefile, "wb");
		fputs($fp, $data);
		fclose($fp);
	}
}

header("Content-type: $type");
if (is_file($bannercachefile)) {
	readfile($bannercachefile);
} else {
	echo $data;
}
