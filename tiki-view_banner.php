<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-view_banner.php,v 1.23 2007-10-12 07:55:33 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

require_once ('lib/tikilib.php'); # httpScheme()
include_once ('lib/banners/bannerlib.php');

if (!isset($bannerlib)) {
	$bannerlib = new BannerLib($dbTiki);
}

if ($prefs['feature_banners'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_banners");

	$smarty->display("error.tpl");
	die;
}

// CHECK FEATURE BANNERS AND ADMIN PERMISSION HERE
if (!isset($_REQUEST["bannerId"])) {
	$smarty->assign('msg', tra("No banner indicated"));

	$smarty->display("error.tpl");
	die;
}

$info = $bannerlib->get_banner($_REQUEST["bannerId"]);

if (!$info) {
	$smarty->assign('msg', tra("Banner not found"));

	$smarty->display("error.tpl");
	die;
}

// Check user is admin or the client
if (($user != $info["client"]) && ($tiki_p_admin_banners != 'y')) {
	$smarty->assign('msg', tra("You do not have permission to edit this banner"));

	$smarty->display("error.tpl");
	die;
}

$smarty->assign('bannerId', $info["bannerId"]);
$smarty->assign('created', $info["created"]);
$smarty->assign('client', $info["client"]);
$smarty->assign('maxImpressions', $info["maxImpressions"]);
$impressions = $info["impressions"];
$clicks = $info["clicks"];
$smarty->assign('impressions', $impressions);
$smarty->assign('clicks', $clicks);

if ($impressions) {
	$smarty->assign('ctr', $clicks / $impressions);
} else {
	$smarty->assign('ctr', 0);
}

$smarty->assign('fromDate', $info["fromDate"]);
$smarty->assign('toDate', $info["toDate"]);
$smarty->assign('useDates', $info["useDates"]);
$smarty->assign("fromTime_h", substr($info["hourFrom"], 0, 2));
$smarty->assign("fromTime_m", substr($info["hourFrom"], 2, 2));
$smarty->assign("toTime_h", substr($info["hourTo"], 0, 2));
$smarty->assign("toTime_m", substr($info["hourTo"], 2, 2));
$smarty->assign("Dmon", $info["mon"]);
$smarty->assign("Dtue", $info["tue"]);
$smarty->assign("Dwed", $info["wed"]);
$smarty->assign("Dthu", $info["thu"]);
$smarty->assign("Dfri", $info["fri"]);
$smarty->assign("Dsat", $info["sat"]);
$smarty->assign("Dsun", $info["sun"]);
$smarty->assign("use", $info["which"]);
$smarty->assign("zone", $info["zone"]);
$smarty->assign("HTMLData", $info["HTMLData"]);
$smarty->assign("fixedURLdata", $info["fixedURLData"]);
$smarty->assign("textData", $info["textData"]);
$smarty->assign("url", $info["url"]);
$smarty->assign("imageName", $info["imageName"]);
$smarty->assign("imageData", urlencode($info["imageData"]));
$smarty->assign("imageType", $info["imageType"]);
$smarty->assign("hasImage", 'n');

if (strlen($info["imageData"]) > 0) {
	$tmpfname = $prefs['tmpDir'] . "/bannerimage" . "." . $_REQUEST["bannerId"];
	$fp = fopen($tmpfname, "wb");
	if ($fp) {
		fwrite($fp, $data);
		fclose ($fp);
		$smarty->assign('tempimg', $tmpfname);
		$smarty->assign('hasImage', 'y');
	} else {
		$smarty->assign('tempimg', 'n');
		$smarty->assign('hasImage', 'n');
	}
}

$bannerId = $info["bannerId"];

$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1 = str_replace("tiki-view_banner", "display_banner", $foo["path"]);

$raw = '';

if ($fp = @fopen($tikilib->httpPrefix(). $foo1 . "?id=$bannerId", "r")) {
	while (!feof($fp)) {
		$raw .= fread($fp, 8192);
	}

	fclose ($fp);
}

$smarty->assign_by_ref('raw', $raw);

ask_ticket('view-banner');

$smarty->assign('mid', 'tiki-view_banner.tpl');
$smarty->display("tiki.tpl");

?>
