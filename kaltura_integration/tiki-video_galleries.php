<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-vdeo_galleries.php,v 1.58.2.1 2008-03-15 21:11:15 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'video_galleries';
require_once ('tiki-setup.php');

global $videogallib; include_once ("lib/videogals/videogallib.php");

if ($prefs['feature_galleries'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_galleries");

	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);

if (!isset($_REQUEST["galleryId"])) {
	$_REQUEST["galleryId"] = 0;
}

$smarty->assign('galleryId', $_REQUEST["galleryId"]);

$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo["path"] = str_replace("tiki-video_galleries", "tiki-browse_video_gallery", $foo["path"]);
$smarty->assign('url', $tikilib->httpPrefix(). $foo["path"]);

	if (!isset($_REQUEST['maxRows'])) $_REQUEST['maxRows'] = $prefs['maxRowsGalleries'];
	if (!isset($_REQUEST['rowVideos'])) $_REQUEST['rowVideos'] = $prefs['rowVideosGalleries'];
	if (!isset($_REQUEST['thumbSizeX'])) $_REQUEST['thumbSizeX'] = $prefs['thumbSizeXVideoGalleries'];
	if (!isset($_REQUEST['thumbSizeY'])) $_REQUEST['thumbSizeY'] = $prefs['thumbSizeYVideoGalleries'];

if (isset($_REQUEST['edit']) || isset($_REQUEST['preview']) || $_REQUEST["galleryId"] == 0) {
	if (!isset($_REQUEST['description'])) $_REQUEST['description'] = '';
	if (!isset($_REQUEST['maxRows'])) $_REQUEST['maxRows'] = 10;
	if (!isset($_REQUEST['rowVideos'])) $_REQUEST['rowVideos'] = 6;
	if (!isset($_REQUEST['thumbSizeX'])) $_REQUEST['thumbSizeX'] = 80;
	if (!isset($_REQUEST['thumbSizeY'])) $_REQUEST['thumbSizeY'] = 80;
	if (!isset($_REQUEST['sortorder'])) $_REQUEST['sortorder'] = 'created';
	if (!isset($_REQUEST['sortdirection'])) $_REQUEST['sortdirection'] = 'desc';
	if (!isset($_REQUEST['parentgallery'])) $_REQUEST['parentgallery'] = -1;

}


// Init smarty variables to blank values
//$smarty->assign('theme','');
$smarty->assign('name', '');
$smarty->assign('description', '');
$smarty->assign('maxRows', $_REQUEST['maxRows']);
$smarty->assign('rowVideos', $_REQUEST['rowVideos']);
$smarty->assign('thumbSizeX',$_REQUEST['thumbSizeX']);
$smarty->assign('thumbSizeY',$_REQUEST['thumbSizeY']);
$smarty->assign('public', 'n');
$smarty->assign('edited', 'n');
$smarty->assign('visible', 'y');
$smarty->assign('owner', $user);
$smarty->assign('edit_mode', 'n');
$options_sortorder=array(tra('id') => 'videoId',
		tra('Name') => 'name',
		tra('Creation Date') => 'created',
		tra('Owner') => 'user',
		tra('Hits') => 'hits');
$smarty->assign_by_ref('options_sortorder',$options_sortorder);
$smarty->assign('sortorder','videoId');
$smarty->assign('sortdirection','desc');
$smarty->assign('showname','y');
$smarty->assign('showvideoid','n');
$smarty->assign('showcategories','n');
$smarty->assign('showdescription','n');
$smarty->assign('showcreated','n');
$smarty->assign('showuser','n');
$smarty->assign('showhits','y');

$galleries_list=$videogallib->list_galleries(0,-1,'name_desc',$user);

$smarty->assign_by_ref('galleries_list',$galleries_list['data']);
$smarty->assign('parentgallery',-1);

// If we are editing an existing gallery prepare smarty variables
if (isset($_REQUEST["edit_mode"]) && $_REQUEST["edit_mode"]) {
	check_ticket('galleries');

	// Get information about this galleryID and fill smarty variables
	$smarty->assign('edit_mode', 'y');

	$smarty->assign('edited', 'y');

	if ($_REQUEST["galleryId"] > 0) {

		if ($info = $videogallib->get_gallery_info($_REQUEST["galleryId"])) {


		//$gallery_videos = $videogallib->get_videos(0,-1,'name_asc',false,$_REQUEST['galleryId']);

		$smarty->assign_by_ref('name', $info["name"]);
		$smarty->assign_by_ref('description', $info["description"]);
		$smarty->assign_by_ref('maxRows', $info["maxRows"]);
		$smarty->assign_by_ref('rowVideos', $info["rowVideos"]);
		$smarty->assign_by_ref('thumbSizeX', $info["thumbSizeX"]);
		$smarty->assign_by_ref('thumbSizeY', $info["thumbSizeY"]);
		$smarty->assign_by_ref('public', $info["public"]);
		$smarty->assign_by_ref('visible', $info["visible"]);
		$smarty->assign_by_ref('owner', $info["user"]);
		$smarty->assign('sortorder',$info['sortorder']);
		$smarty->assign('sortdirection',$info['sortdirection']);
		$smarty->assign('parentgallery',$info['parentgallery']);
		$smarty->assign('showname',$info['showname']);
		$smarty->assign('showvideoid',$info['showvideoid']);
		$smarty->assign('showcategories',$info['showcategories']);;
		$smarty->assign('showdescription',$info['showdescription']);
		$smarty->assign('showcreated',$info['showcreated']);
		$smarty->assign('showuser',$info['showuser']);
		$smarty->assign('showhits',$info['showhits']);
		}
	}
}

// Process the insertion or modification of a gallery here
if (isset($_REQUEST["edit"])) {
	check_ticket('galleries');
	// Saving information
	// If the user is not gallery admin
	if ($tiki_p_admin_galleries != 'y') {
		if ($tiki_p_create_galleries != 'y') {
			// If you can't create a gallery then you can't edit a gallery because you can't have a gallery
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra("Permission denied you cannot create galleries and so you cant edit them"));

			$smarty->display("error.tpl");
			die;
		}

		// If the user can create a gallery then check if he can edit THIS gallery
		if ($_REQUEST["galleryId"] > 0) {
			$info = $videogallib->get_gallery_info($_REQUEST["galleryId"]);

			if (!$user || $info["user"] != $user) {
				$smarty->assign('errortype', 401);
				$smarty->assign('msg', tra("Permission denied you cannot edit this gallery"));

				$smarty->display("error.tpl");
				die;
			}
		}
	}

	// Everything is ok so we proceed to edit the gallery
	$smarty->assign('edit_mode', 'y');

	$smarty->assign_by_ref('name', $_REQUEST["name"]);
	$smarty->assign_by_ref('owner', $_REQUEST["owner"]);
	$smarty->assign_by_ref('description', $_REQUEST["description"]);
	$smarty->assign_by_ref('maxRows', $_REQUEST["maxRows"]);
	$smarty->assign_by_ref('rowVideos', $_REQUEST["rowVideos"]);
	$smarty->assign_by_ref('thumbSizeX', $_REQUEST["thumbSizeX"]);
	$smarty->assign_by_ref('thumbSizeY', $_REQUEST["thumbSizeY"]);
    $smarty->assign('sortorder',$_REQUEST['sortorder']);
	$smarty->assign('sortdirection',$_REQUEST['sortdirection']);
	$smarty->assign('parentgallery',$_REQUEST['parentgallery']);
	$smarty->assign('defaultscale',$_REQUEST['defaultscale']);
	$auxarray=array('showname','showvideoid','showdescription','showcreated','showuser','showhits','showcategories');
	foreach($auxarray as $key => $item) {
		if(!isset($_REQUEST[$item])) {
			$_REQUEST[$item]='n';
		}
        	$smarty->assign($item,$_REQUEST[$item]);
	}



	if (isset($_REQUEST["visible"]) && $_REQUEST["visible"] == "on") {
		$visible = 'y';
	} else {
		$visible = 'n';
	}

	if (isset($_REQUEST["public"]) && $_REQUEST["public"] == "on") {
		$public = 'y';
	} else {
		$public = 'n';
	}



	$gid = $videogallib->replace_gallery($_REQUEST["galleryId"], $_REQUEST["name"], $_REQUEST["description"],
		'', $_REQUEST["owner"], $_REQUEST["maxRows"], $_REQUEST["rowVideos"], $_REQUEST["thumbSizeX"], $_REQUEST["thumbSizeY"], $public,
		$visible,$_REQUEST['sortorder'],$_REQUEST['sortdirection'],$_REQUEST['parentgallery'],
		$_REQUEST['showname'],$_REQUEST['showvideoid'],$_REQUEST['showdescription'],$_REQUEST['showcreated'],
		$_REQUEST['showuser'],$_REQUEST['showhits'],$_REQUEST['showcategories']);

	$smarty->assign('edit_mode', 'n');
	$smarty->assign('galleryId', '');
	$_REQUEST["galleryId"] = 0;
}

if (isset($_REQUEST["removegal"])) {
	if ($tiki_p_admin_galleries != 'y') {
		$info = $videogallib->get_gallery_info($_REQUEST["removegal"]);

		if (!$user || $info["user"] != $user) {
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra("Permission denied you cannot remove this gallery"));

			$smarty->display("error.tpl");
			die;
		}
	}
  $area = 'delgal';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$videogallib->remove_gallery($_REQUEST["removegal"]);
  } else {
    key_get($area);
  }
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'name_asc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

$smarty->assign_by_ref('sort_mode', $sort_mode);

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

// Get the list of libraries available for this user (or public galleries)
global $videogallib;
if (!is_object($videogallib)) {
	require_once('lib/videogals/videogallib.php');
}

$galleries = $videogallib->list_galleries($offset, $maxRecords, $sort_mode, 'admin', $find);

$smarty->assign('filter', '');
if (!empty($_REQUEST['filter']))
	$smarty->assign('filter', $_REQUEST["filter"]);


$temp_max = count($galleries["data"]);
for ($i = 0; $i < $temp_max; $i++) {

	// check if top gallery (has no parents)
	$info = $videogallib->get_gallery_info($galleries["data"][$i]["galleryId"]);
	if ($info['parentgallery'] == -1) {
		$galleries["data"][$i]["topgal"] = 'y';
	} else {
		$galleries["data"][$i]["topgal"] = 'n';
	}

	// check if has subgalleries (parent of any children)
	$maxVideos = 1;
	$subgals = $videogallib->get_subgalleries($offset, $maxVideos, $sort_mode, '', $galleries["data"][$i]["galleryId"]);
	if (count($subgals['data']) > 0) {
		$galleries["data"][$i]["parentgal"] = 'y';
	} else {
		$galleries["data"][$i]["parentgal"] = 'n';
	}

}

$smarty->assign_by_ref('galleries', $galleries["data"]);
$smarty->assign_by_ref('cant', $galleries["cant"]);

$defaultRows = 5;
include_once("textareasize.php");

include_once ('tiki-section_options.php');
ask_ticket('galleries');

// Display the template
$smarty->assign('mid', 'tiki-video_galleries.tpl');
$smarty->display("tiki.tpl");
?>
