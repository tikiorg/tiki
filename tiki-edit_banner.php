<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-edit_banner.php,v 1.29.2.1 2007-11-26 14:41:03 sylvieg Exp $

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

// CHECK FEATURE BANNERS AND ADMIN PERMISSION HERE
if ($prefs['feature_banners'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_banners");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin_banners != 'y') {
	$smarty->assign('msg', tra("You do not have permissions to edit banners"));

	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["bannerId"]) && $_REQUEST["bannerId"] > 0) {
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

	$fromTime = substr($info["hourFrom"], 0, 2).":".substr($info["hourFrom"], 2, 2);
	$toTime = substr($info["hourTo"], 0 , 2).":".substr($info["hourTo"], 2, 2);
	$smarty->assign('bannerId', $info["bannerId"]);
	$smarty->assign('client', $info["client"]);
	$smarty->assign('maxImpressions', $info["maxImpressions"]);
	$smarty->assign('fromDate', $info["fromDate"]);
	$smarty->assign('toDate', $info["toDate"]);
	$smarty->assign('useDates', $info["useDates"]);
	$smarty->assign("fromTime", $fromTime);
	$smarty->assign("toTime", $toTime);
	$smarty->assign("Dmon", $info["mon"]);
	$smarty->assign("Dtue", $info["tue"]);
	$smarty->assign("Dwed", $info["wed"]);
	$smarty->assign("Dthu", $info["thu"]);
	$smarty->assign("Dfri", $info["fri"]);
	$smarty->assign("Dsat", $info["sat"]);
	$smarty->assign("Dsun", $info["sun"]);
	$smarty->assign("use", $info["which"]);
	$smarty->assign("zone", $info["zone"]);
	if ($info["which"] == 'useFlash') {
		$matches=array();
		preg_match('/SWFFix\.embedSWF\([\'" ]*([^,\'"]*)[\'" ]*,[\'" ]*([^,\'"]*)[\'" ]*,[\'" ]*([^,\'"]*)[\'" ]*,[\'" ]*([^,\'"]*)[\'" ]*,[\'" ]*([^,\'"]*)[\'" ]*,[\'" ]*([^,\'"]*)[\'" ]*,[\'" ]*([^,\'"]*)[\'" ]*,[\'" ]*([^,\'"]*)[\'" ]*,[\'" ]*([^,\'")]*)[\'" ]*/', $info["HTMLData"], $matches);
		$smarty->assign("movieUrl", $matches[1]);
		$smarty->assign("movieId", $matches[2]);
		$smarty->assign("movieWidth", $matches[3]);
		$smarty->assign("movieHeight", $matches[4]);
		$smarty->assign("movieVersion", $matches[5]);
		$smarty->assign("movieInstallUrl", $matches[6]);
		$smarty->assign("movieFlashVars", $matches[7]);
		$smarty->assign("movieParams", $matches[8]);
		$smarty->assign("movieAttributes", $matches[9]);
	
	}
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

} else {
	$smarty->assign('client', '');
	$smarty->assign('maxImpressions', 1000);
	$smarty->assign('fromDate', $tikilib->now);
	$cur_time = explode(',', $tikilib->date_format('%Y,%m,%d,%H,%M,%S', $publishDate));
	$smarty->assign('toDate', $tikilib->make_time($cur_time[3], $cur_time[4], $cur_time[5], $cur_time[1], $cur_time[2], $cur_time[0]+1));
	$smarty->assign('useDates', 'n');
	$smarty->assign('fromTime', '00:00');
	$smarty->assign('toTime', '23:59');
	// Variables for dates are fromDate_ and toDate_ plus fromTime_ and toTime_
	$smarty->assign('Dmon', 'y');
	$smarty->assign('Dtue', 'y');
	$smarty->assign('Dwed', 'y');
	$smarty->assign('Dthu', 'y');
	$smarty->assign('Dfri', 'y');
	$smarty->assign('Dsat', 'y');
	$smarty->assign('Dsun', 'y');
	$smarty->assign('bannerId', 0);
	$smarty->assign('zone', '');
	$smarty->assign('use', 'useHTML');
	$smarty->assign('HTMLData', '');
	$smarty->assign('fixedURLData', '');
	$smarty->assign('textData', '');
	$smarty->assign('url', '');
	$smarty->assign('imageData', '');
	$smarty->assign('hasImage', 'n');
	$smarty->assign('imageName', '');
	$smarty->assign('imageType', '');
}

if (isset($_REQUEST["removeZone"])) {
  $area = 'delbannerzone';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$bannerlib->banner_remove_zone($_REQUEST["removeZone"]);
  } else {
    key_get($area);
  }
}

// Now assign if the set button was pressed
if (isset($_REQUEST["save"]) || isset($_REQUEST["create_zone"])) {
	check_ticket('edit-banner');
	$fromDate = mktime(0, 0, 0, $_REQUEST["fromDate_Month"], $_REQUEST["fromDate_Day"], $_REQUEST["fromDate_Year"]);
	$toDate = mktime(0, 0, 0, $_REQUEST["toDate_Month"], $_REQUEST["toDate_Day"], $_REQUEST["toDate_Year"]);
	$fromTime = ''.$_REQUEST["fromTimeHour"].$_REQUEST["fromTimeMinute"].'';
	$toTime = ''.$_REQUEST["toTimeHour"].$_REQUEST["toTimeMinute"].'';
	$smarty->assign('fromDate', $fromDate);
	$smarty->assign('toDate', $toDate);
	$smarty->assign('fromTime', $_REQUEST["fromTimeHour"].':'.$_REQUEST["fromTimeMinute"]);
	$smarty->assign('toTime', $_REQUEST["toTimeHour"].':'.$_REQUEST["toTimeMinute"]);
	$smarty->assign('client', $_REQUEST["client"]);
	$smarty->assign('maxImpressions', $_REQUEST["maxImpressions"]);
	$smarty->assign('HTMLData', $_REQUEST["HTMLData"]);
	$smarty->assign('fixedURLData', $_REQUEST["fixedURLData"]);
	$smarty->assign('textData', $_REQUEST["textData"]);

	if (isset($_REQUEST["zone"])) {
		$smarty->assign('zone', $_REQUEST["zone"]);
	} else {
		$smarty->assign('zone', '');
	}

	if (substr($_REQUEST["url"], 0, 4) != 'http') {
		$_REQUEST["url"] = $tikilib->httpScheme(). '://' . $_REQUEST["url"];
	}

	$smarty->assign('url', $_REQUEST["url"]);

	if (isset($_REQUEST["use"])) {
		$smarty->assign('use', $_REQUEST["use"]);
	}

	if (isset($_REQUEST["useDates"]) && $_REQUEST["useDates"] == 'on') {
		$smarty->assign('useDates', 'y');

		$useDates = 'y';
	} else {
		$smarty->assign('useDates', 'n');

		$useDates = 'n';
	}

	if (isset($_REQUEST["Dmon"]) && $_REQUEST["Dmon"] == 'on') {
		$smarty->assign('Dmon', 'y');

		$Dmon = 'y';
	} else {
		$smarty->assign('Dmon', 'n');

		$Dmon = 'n';
	}

	if (isset($_REQUEST["Dtue"]) && $_REQUEST["Dtue"] == 'on') {
		$smarty->assign('Dtue', 'y');

		$Dtue = 'y';
	} else {
		$smarty->assign('Dtue', 'n');

		$Dtue = 'n';
	}

	if (isset($_REQUEST["Dwed"]) && $_REQUEST["Dwed"] == 'on') {
		$smarty->assign('Dwed', 'y');

		$Dwed = 'y';
	} else {
		$smarty->assign('Dwed', 'n');

		$Dwed = 'n';
	}

	if (isset($_REQUEST["Dthu"]) && $_REQUEST["Dthu"] == 'on') {
		$smarty->assign('Dthu', 'y');

		$Dthu = 'y';
	} else {
		$smarty->assign('Dthu', 'n');

		$Dthu = 'n';
	}

	if (isset($_REQUEST["Dfri"]) && $_REQUEST["Dfri"] == 'on') {
		$smarty->assign('Dfri', 'y');

		$Dfri = 'y';
	} else {
		$smarty->assign('Dfri', 'n');

		$Dfri = 'n';
	}

	if (isset($_REQUEST["Dsat"]) && $_REQUEST["Dsat"] == 'on') {
		$smarty->assign('Dsat', 'y');

		$Dsat = 'y';
	} else {
		$smarty->assign('Dsat', 'n');

		$Dsat = 'n';
	}

	if (isset($_REQUEST["Dsun"]) && $_REQUEST["Dsun"] == 'on') {
		$smarty->assign('Dsun', 'y');

		$Dsun = 'y';
	} else {
		$smarty->assign('Dsun', 'n');

		$Dsun = 'n';
	}

	$smarty->assign('bannerId', $_REQUEST["bannerId"]);

	if (isset($_REQUEST["create_zone"])) {
		$bannerlib->banner_add_zone($_REQUEST["zoneName"]);
	}

	// If we have an upload then process the upload and setup the data in a field
	// that will be hidden is this is a nightmare?
	$imgname = $_REQUEST["imageName"];
	$imgtype = $_REQUEST["imageType"];

	if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
		$fp = fopen($_FILES['userfile1']['tmp_name'], "rb");

		$data = fread($fp, filesize($_FILES['userfile1']['tmp_name']));
		fclose ($fp);
		$imgtype = $_FILES['userfile1']['type'];
		$imgsize = $_FILES['userfile1']['size'];
		$imgname = $_FILES['userfile1']['name'];
		$smarty->assign('imageData', urlencode($data));
		$smarty->assign('imageName', $imgname);
		$smarty->assign('imageType', $imgtype);
		$_REQUEST["imageData"] = urlencode($data);
		$_REQUEST["imageName"] = $imgname;
		$_REQUEST["imageType"] = $imgtype;
	}

	$smarty->assign('imageData', $_REQUEST["imageData"]);
	$smarty->assign('tempimg', 'n');

	if (strlen($_REQUEST["imageData"]) > 0) {
		$tmpfname = tempnam($prefs['tmpDir'], "TMPIMG"). $imgname;

		$fp = fopen($tmpfname, "w");

		if ($fp) {
			fwrite($fp, urldecode($_REQUEST["imageData"]));

			fclose ($fp);
			$smarty->assign('tempimg', $tmpfname);
			$smarty->assign('hasImage', 'y');
		} else {
			$smarty->assign('hasImage', 'n');
		}
	}

	if (!isset($_REQUEST["create_zone"])) {
		if ($_REQUEST["use"] == "useFlash") {
			$_REQUEST["HTMLData"]=$bannerlib->embed_flash($_REQUEST["movieUrl"],$_REQUEST["movieId"],$_REQUEST["movieInstallUrl"],$_REQUEST["movieWidth"],$_REQUEST["movieHeight"],$_REQUEST["movieVersion"],"","","");
		}
		$bannerId = $bannerlib->replace_banner($_REQUEST["bannerId"], $_REQUEST["client"], $_REQUEST["url"], '',
			'', $_REQUEST["use"], $_REQUEST["imageData"], $_REQUEST["imageType"], $_REQUEST["imageName"], $_REQUEST["HTMLData"],
			$_REQUEST["fixedURLData"], $_REQUEST["textData"], $fromDate, $toDate, $useDates, $Dmon, $Dtue, $Dwed, $Dthu, $Dfri,
			$Dsat, $Dsun, $fromTime, $toTime, $_REQUEST["maxImpressions"], $_REQUEST["zone"]);

		header("location:tiki-list_banners.php");
		
	}
}

$zones = $bannerlib->banner_get_zones();
$smarty->assign_by_ref('zones', $zones);
$clients = $userlib->get_users(0, -1, 'login_desc', '');
$smarty->assign_by_ref('clients', $clients["data"]);

ask_ticket('edit-banner');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-edit_banner.tpl');
$smarty->display("tiki.tpl");

?>
