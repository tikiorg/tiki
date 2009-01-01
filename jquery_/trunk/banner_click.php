<?php

// $Id: /cvsroot/tikiwiki/tiki/banner_click.php,v 1.10.2.1 2008-03-01 17:12:54 leyan Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# $Id: /cvsroot/tikiwiki/tiki/banner_click.php,v 1.10.2.1 2008-03-01 17:12:54 leyan Exp $

require_once ('tiki-setup.php');

include_once ('lib/banners/bannerlib.php');

if (!isset($bannerlib)) {
	$bannerlib = new BannerLib($dbTiki);
}

// CHECK FEATURE BANNERS HERE
if ($prefs['feature_banners'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_banners");

	$smarty->display("error.tpl");
	die;
}

$bannerlib->add_click($_REQUEST["id"]);
$url = urldecode($_REQUEST["url"]);
header ("location: $url");

?>
