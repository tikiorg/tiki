<?php

// $Id$

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

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

	header ("Content-type: $type");
	readfile($bannercachefile);
	exit;
}

require_once ('tiki-setup.php');

// CHECK FEATURE BANNERS HERE
$access->check_feature('feature_banners');

$bannercachefile = $prefs['tmpDir'];
if ($tikidomain) { $bannercachefile.= "/$tikidomain"; }
$bannercachefile.= "/banner.". (int)$_REQUEST["id"];

if (is_file($bannercachefile) and (!isset($_REQUEST["reload"]))) {
	$size = getimagesize($bannercachefile);
	$type = $size['mime'];
} else {
	include_once ('lib/banners/bannerlib.php');
	if (!isset($bannerlib)) {
		$bannerlib = new BannerLib;
	}
	$data = $bannerlib->get_banner($_REQUEST["id"]);
	if (!$data) {
		die;
	}
	$type = $data["imageType"];
	$data = $data["imageData"];
	if ($data) {
		$fp = fopen($bannercachefile,"wb");
		fputs($fp,$data);
		fclose($fp);
	}
}

header ("Content-type: $type");
if (is_file($bannercachefile)) {
	readfile($bannercachefile);
} else {
	echo $data;
}
