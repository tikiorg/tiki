<?php

// $Header: /cvsroot/tikiwiki/tiki/select_banner.php,v 1.7 2004-03-28 07:32:22 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# $Header: /cvsroot/tikiwiki/tiki/select_banner.php,v 1.7 2004-03-28 07:32:22 mose Exp $

// application to display an image from the database with 
// option to resize the image dynamically creating a thumbnail on the fly.
if (!isset($_REQUEST["zone"])) {
	die;
}

include_once("lib/init/initlib.php");
include_once ('db/tiki-db.php');
include_once ('lib/tikilib.php');
include_once ('lib/banners/bannerlib.php');

if (!isset($bannerlib)) {
	$bannerlib = new BannerLib($dbTiki);
}

$tikilib = new Tikilib($dbTiki);
$banner = $bannerlib->select_banner($_REQUEST["zone"]);
print ($banner);

?>
