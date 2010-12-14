<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = "galleries";
require_once ('tiki-setup.php');
include_once ("lib/imagegals/imagegallib.php");
include_once ('lib/stats/statslib.php');

$access->check_feature('feature_galleries');

if ($prefs['feature_categories'] == 'y') {
	global $categlib;
	if (!is_object($categlib)) {
		include_once ('lib/categories/categlib.php');
	}
}

if (!isset($_REQUEST['imageId'])) {
	$smarty->assign('msg', tra("No image indicated"));
	$smarty->display("error.tpl");
	die;
}
$imageId = $_REQUEST['imageId'];
// always get gallery from image so no user can fake the galleryid
// and get an image that is truly in another (forbidden) gallery
$galleryId = $imagegallib->get_gallery_from_image($imageId);
if (!$galleryId) {
	$smarty->assign('msg', tra("picture not found"));
	$smarty->display("error.tpl");
	die;
}

$tikilib->get_perm_object( $galleryId, 'image gallery' );
$access->check_permission('tiki_p_view_image_gallery');

$gal_info = $imagegallib->get_gallery($galleryId);
$scalesize = 0;
if (isset($_REQUEST["scalesize"])) {
	if (is_numeric($_REQUEST["scalesize"]) && $_REQUEST["scalesize"] > 0) {
		$scalesize = $_REQUEST["scalesize"];
	}
} elseif ($gal_info['defaultscale'] !== 'o') {
	$scalesize = $gal_info['defaultscale'];
}
$arrscales = $imagegallib->get_gallery_scale_info($galleryId);
// adjust scale size to existing ones
if ($scalesize && !$prefs['preset_galleries_info'] == 'y') {
	$testscale = 0;
	foreach( $arrscales as $arrscale ) {
		if ($scalesize <= $arrscale['scale']) {
			$testscale = $arrscale['scale'];
			break;
		}
	}
	$scalesize = $testscale;
}
$smarty->assign_by_ref('scalesize', $scalesize);
$smarty->assign('same_scale', "&amp;scalesize={$scalesize}");
if (!isset($_REQUEST["sort_mode"])) {
	$_REQUEST["sort_mode"] = $gal_info['sortorder'] . '_' . $gal_info['sortdirection'];
}
$sort_mode = $_REQUEST["sort_mode"];
$listImgId = $imagegallib->get_gallery_image($galleryId, 'all', $sort_mode);
$offset = array_search($imageId, $listImgId);
if ($offset) {
	$smarty->assign('previmg', $listImgId[$offset - 1]);
} else {
	$smarty->assign('previmg', '');
}
if (count($listImgId) > $offset) {
	$smarty->assign('nextimg', $listImgId[$offset + 2]);
} else {
	$smarty->assign('nextimg', '');
}
$smarty->assign('firstId', $listImgId[0]);
$smarty->assign('lastId', $listImgId[count($listImgId) - 1]);
$smarty->assign('sort_mode', $_REQUEST["sort_mode"]);
$smarty->assign('galleryId', $galleryId);
$popup = isset($_REQUEST['popup']) && $_REQUEST['popup'] ? 'y' : '';
$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo2 = str_replace("tiki-browse_image", "show_image", $foo["path"]);
$smarty->assign('url_browse', $tikilib->httpPrefix() . $foo["path"]);
$smarty->assign('url_base', $foo["path"] . "?galleryId={$galleryId}&amp;sort_mode={$sort_mode}" . ($popup ? '&amp;popup=y' : '') . '&amp;imageId=');
$smarty->assign('url_show', $tikilib->httpPrefix() . $foo2);
$imagegallib->add_image_hit($imageId);
$info = $imagegallib->get_image_info($imageId);
$maxgal = $gal_info['maxRows'] * $gal_info['rowImages'];
$smarty->assign('offset', $maxgal ? $offset - ($offset % $maxgal) : 0);
//$smarty->assign_by_ref('theme',$gal_info["theme"]);
//$smarty->assign('use_theme','y');
if ($prefs['feature_gal_slideshow'] != 'n') {
	$headerlib->add_jsfile('lib/imagegals/imagegallib.js', 50);
	$listImgId = implode(',', $listImgId);
	$smarty->assign('listImgId', $listImgId);
}
// Everybody can browse images
if (isset($_REQUEST["move_image"])) {
	check_ticket('browse-image');
	if ($tiki_p_admin_galleries != 'y' && (!$user || $user != $gal_info["user"])) {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("You do not have permission to move images from this gallery"));
		$smarty->display("error.tpl");
		die;
	}
	if (isset($_REQUEST["newname"]) and $_REQUEST["newname"] != $info["name"]) {
		if ($imagegallib->edit_image($imageId, $_REQUEST['newname'], $info['description'], $info['lat'], $info['lon'])) {
			$info['name'] = $_REQUEST['newname'];
		}
	}
	$imagegallib->move_image($imageId, $_REQUEST["newgalleryId"]);
	$info['galleryId'] = $_REQUEST["newgalleryId"];
}
$smarty->assign_by_ref('owner', $gal_info["user"]);
$smarty->assign_by_ref('defaultscale', $gal_info["defaultscale"]);
$smarty->assign_by_ref('imageId', $imageId);
$smarty->assign_by_ref('name', $info["name"]);
$smarty->assign_by_ref('title', $info["name"]);
$smarty->assign_by_ref('galleryId', $info["galleryId"]);
$smarty->assign_by_ref('description', $info["description"]);
$smarty->assign_by_ref('lat', $info["lat"]);
$smarty->assign_by_ref('lon', $info["lon"]);
$smarty->assign_by_ref('created', $info["created"]);
$smarty->assign_by_ref('filename', $info["filename"]);
$smarty->assign('xsize', $info["xsize"]);
$smarty->assign('ysize', $info["ysize"]);
$smarty->assign_by_ref('hits', $info["hits"]);
$smarty->assign_by_ref('image_user', $info["user"]);
$smarty->assign_by_ref('gal_info', $gal_info);
$galleries = $imagegallib->list_visible_galleries(0, -1, 'lastModif_desc', $user, '');
$smarty->assign_by_ref('galleries', $galleries["data"]);
// Init vars
$smarty->assign('popup', $popup);
$smarty->assign('popupsize', '');
$winxsize = 0;
$winysize = 0;
// Calculate PopUp Window size for the popup link
$winx = $info['xsize'];
if ($winx < 320) {
	$winx = 320;
}
$winy = $info['ysize'];
if ($winy < 200) {
	$winy = 200;
}
// Give it some more pixels for the links and a little margin
$winx+= 40;
$winy+= 80;
// Now get'em to the template
$smarty->assign('winx', $winx);
$smarty->assign('winy', $winy);
// adjust scale size to existing ones according to image if smaller
$maxsize = max($info['xsize'], $info['ysize']);
$resultscale = $scalesize < $maxsize ? $scalesize : 0;
// build previous and next scale according to current and existing ones
$scaleinfo['nextscale'] = 0;
$scaleinfo['prevscale'] = 0;
$testscale = $resultscale ? $resultscale : $maxsize;
foreach($arrscales as $arrscale) {
	if ($testscale == $arrscale['scale']) {
		continue;
	}
	if ($testscale > $arrscale['scale']) {
		$scaleinfo['prevscale'] = $arrscale['scale'];
		continue;
	}
	if ($maxsize > $arrscale['scale']) {
		$scaleinfo['nextscale'] = $arrscale['scale'];
	}
	break;
}
$scaleinfo['clickscale'] = $scaleinfo['nextscale'] ? $scaleinfo['nextscale'] : ($resultscale ? 0 : ($arrscales ? $arrscales[0]['scale'] : -1));
$smarty->assign('resultscale', $resultscale);
if ($resultscale) {
	$info = $imagegallib->get_image_info($imageId, 's', $resultscale);
	$smarty->assign('xsize_scaled', $info["xsize"]);
	$smarty->assign('ysize_scaled', $info["ysize"]);
}
$smarty->assign_by_ref('scaleinfo', $scaleinfo);
include_once ('tiki-section_options.php');
if ($prefs['feature_theme_control'] == 'y') {
	$cat_type = 'image gallery';
	$cat_objid = $galleryId;
	include ('tiki-tc.php');
}
// now set it if needed
if ($popup) {
	$prefs['feature_top_bar'] = 'n';
	$prefs['feature_left_column'] = 'n';
	$prefs['feature_right_column'] = 'n';
	$prefs['feature_bot_bar'] = 'n';
}
ask_ticket('browse-image');
//add a hit
$statslib->stats_hit($info["name"], "image", $imageId);
if ($prefs['feature_actionlog'] == 'y') {
	$logslib->add_action('Viewed', $galleryId, 'image gallery');
}
// Display the template
if ($popup) {
	$smarty->display("tiki-browse_image.tpl");
} else {
	$smarty->assign('mid', 'tiki-browse_image.tpl');
	$smarty->display("tiki.tpl");
}
