<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
  
if (!isset($_REQUEST["zone"])) {
	die;
}

require_once ('tiki-setup.php');

include_once ('lib/banners/bannerlib.php');

if (!isset($bannerlib)) {
	$bannerlib = new BannerLib;
}

// CHECK FEATURE BANNERS HERE
$access->check_feature('feature_banners');

$banner = $bannerlib->select_banner($_REQUEST["zone"]);
print ($banner);
