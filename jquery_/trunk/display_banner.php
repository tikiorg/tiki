<?php

// $Id: /cvsroot/tikiwiki/tiki/display_banner.php,v 1.16.2.1 2008-03-01 17:12:54 leyan Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# $Id: /cvsroot/tikiwiki/tiki/display_banner.php,v 1.16.2.1 2008-03-01 17:12:54 leyan Exp $

// Only to be called from edit_banner or view_banner to display the banner without adding
// impressions to the banner

if (!isset($_REQUEST["id"])) {
	die;
}

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

$data = $bannerlib->get_banner($_REQUEST["id"]);
$id = $data["bannerId"];

switch ($data["which"]) {
case 'useHTML':
	$raw = $data["HTMLData"];

	break;

case 'useImage':
	$raw = "<img border=\"0\" src=\"banner_image.php?id=" . $id . "\" />";

	break;

case 'useFixedURL':
	$fp = fopen($data["fixedURLData"], "r");

	if ($fp) {
		$raw = '';

		while (!feof($fp)) {
			$raw .= fread($fp, 8192);
		}
	}

	fclose ($fp);
	break;

case 'useText':
	$raw = $data["textData"];

	break;
}

print ($raw);

?>
