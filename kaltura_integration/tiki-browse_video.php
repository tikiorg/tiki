<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-browse_video.php,v 1.46.2.4 2008-03-06 19:45:42 sampaioprimo Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = "video_galleries";
require_once('tiki-setup.php');

include_once ("lib/kaltura/includes.php");
include_once ("lib/videogals/videogallib.php");
include_once ('lib/stats/statslib.php');

if ($prefs['feature_categories'] == 'y') {
	global $categlib;
	if (!is_object($categlib)) {
		include_once('lib/categories/categlib.php');
	}
}

if ($prefs['feature_galleries'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_galleries");
	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST['videoId'])) {
	$smarty->assign('msg', tra("No video indicated"));
	$smarty->display("error.tpl");
	die;
}
$videoId = $_REQUEST['videoId'];

// always get gallery from video so no user can fake the galleryid
// and get an video that is truly in another (forbidden) gallery

$galleryId = $videogallib->get_gallery_from_video($videoId);

if (!$galleryId) {
	$smarty->assign('msg', tra("Video not found"));
	$smarty->display("error.tpl");
	die;
}

$smarty->assign('individual', 'n');

if ($userlib->object_has_one_permission($galleryId, 'video gallery')) {
	$smarty->assign('individual', 'y');

	if ($tiki_p_admin != 'y') {
		// Now get all the permissions that are set for this type of permissions 'video gallery'
		$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'video galleries');

		foreach ($perms["data"] as $perm) {
			$permName = $perm["permName"];

			if ($userlib->object_has_permission($user, $galleryId, 'video gallery', $permName)) {
				$$permName = 'y';

				$smarty->assign("$permName", 'y');
			} else {
				$$permName = 'n';

				$smarty->assign("$permName", 'n');
			}
		}
	}
} elseif ($tiki_p_admin != 'y' && $prefs['feature_categories'] == 'y') {
	$perms_array = $categlib->get_object_categories_perms($user, 'video gallery', $galleryId);
   	if ($perms_array) {
   		$is_categorized = TRUE;
    	foreach ($perms_array as $perm => $value) {
    		$$perm = $value;
    	}
   	} else {
   		$is_categorized = FALSE;
   	}
	if ($is_categorized && isset($tiki_p_view_categorized) && $tiki_p_view_categorized != 'y') {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg',tra("Permission denied you cannot view this page"));
		$smarty->display("error.tpl");
		die;
	}
}

if ($tiki_p_admin_galleries == 'y') {
	$tiki_p_view_video_gallery = 'y';
	$smarty->assign("tiki_p_view_video_gallery", 'y');
	$tiki_p_upload_videos = 'y';
	$smarty->assign("tiki_p_upload_videos", 'y');
	$tiki_p_create_galleries = 'y';
	$smarty->assign("tiki_p_create_galleries", 'y');
}

if ($tiki_p_view_video_gallery != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied you can not view this section"));
	$smarty->display("error.tpl");
	die;
}



$gal_info = $videogallib->get_gallery($galleryId);

if (!isset($_REQUEST["sort_mode"])) {
	$_REQUEST["sort_mode"] = $gal_info['sortorder'].'_'.$gal_info['sortdirection'];
}

$sort_mode = $_REQUEST["sort_mode"];

$listVidId = $videogallib->get_gallery_videos($galleryId,$sort_mode);

$offset = array_search($videoId, $listVidId);

if ($offset) {
	$smarty->assign('previmg', $listVidId[$offset-1]);
} else {
	$smarty->assign('previmg', '');
}

if (count($listVidId) > $offset) {
	$smarty->assign('nextimg', $listVidId[$offset+1]);
} else {
	$smarty->assign('nextimg', '');
}

$smarty->assign('firstId', $listVidId[0]);
$smarty->assign('lastId', $listVidId[count($listVidId)-1]);

$smarty->assign('sort_mode', $_REQUEST["sort_mode"]);
$smarty->assign('galleryId', $galleryId);


$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo2 = str_replace("tiki-browse_video", "show_video", $foo["path"]);
$smarty->assign('url_browse', $tikilib->httpPrefix(). $foo["path"]);
$smarty->assign('url_base', $foo["path"] .
         "?galleryId={$galleryId}&amp;sort_mode={$sort_mode}" .
         ($popup ? '&amp;popup=y' : '') . '&amp;videoId=');
$smarty->assign('url_show', $tikilib->httpPrefix(). $foo2);

$info = $videogallib->get_video_info($videoId);

		$conf = kaltura_init_config();

		$kuser = new KalturaSessionUser();
		$kuser->userId = "123";
		$cl = new KalturaClient($conf);
		$kres =$cl->start($user, $conf->secret);

$res= $cl->getEntry ( $kuser , $info[entryId],1);

$info = $res['result']['entry'];

//print_r($info);
$smarty->assign_by_ref('entryId', $info['id']);
$maxgal = $gal_info['maxRows'] * $gal_info['rowImages'];
$smarty->assign('offset', $maxgal ? $offset - ($offset % $maxgal) : 0);

//$smarty->assign_by_ref('theme',$gal_info["theme"]);
//$smarty->assign('use_theme','y');

// Everybody can browse videos
if (isset($_REQUEST["move_video"])) {
	check_ticket('browse-video');
	if ($tiki_p_admin_galleries != 'y' && (!$user || $user != $gal_info["user"])) {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("Permission denied you cannot move videos from this gallery"));

		$smarty->display("error.tpl");
		die;
	}

	if (isset($_REQUEST["newname"]) and $_REQUEST["newname"] != $info["name"]) {
		if ($videogallib->edit_video($videoId, $_REQUEST['newname'], $info['description'])) {
			$info['name'] = $_REQUEST['newname'];
		}
	}
	$videogallib->move_video($videoId, $_REQUEST["newgalleryId"]);
	$info['galleryId'] = $_REQUEST["newgalleryId"];
}

$smarty->assign_by_ref('owner', $gal_info["user"]);
$smarty->assign_by_ref('videoId', $videoId);
$smarty->assign_by_ref('name', $info["name"]);
$smarty->assign_by_ref('title', $info["name"]);
$smarty->assign_by_ref('galleryId', $info["galleryId"]);
$smarty->assign_by_ref('description', $info["kshow"]["description"]);
$smarty->assign_by_ref('created', $info["createdAtAsInt"]);
$smarty->assign_by_ref('hits', $info["views"]);
$smarty->assign_by_ref('video_user', $info["userScreenName"]);
$smarty->assign_by_ref('gal_info', $gal_info);

$galleries = $videogallib->list_visible_galleries(0, -1, 'lastModif_desc', $user, '');

$smarty->assign_by_ref('galleries', $galleries["data"]);

include_once('tiki-section_options.php');

if ($prefs['feature_theme_control'] == 'y') {
	$cat_type = 'video gallery';

	$cat_objid = $galleryId;
	include('tiki-tc.php');
}

ask_ticket('browse-video');

//add a hit
$statslib->stats_hit($info["name"],"video",$videoId);
if ($prefs['feature_actionlog'] == 'y') {
	include_once('lib/logs/logslib.php');
	$logslib->add_action('Viewed', $galleryId, 'video gallery');
}

// Display the template

	$smarty->assign('mid', 'tiki-browse_video.tpl');
	$smarty->display("tiki.tpl");

?>
