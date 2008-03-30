<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-list_gallery.php,v 1.29.2.1 2007-12-07 05:56:38 mose Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'galleries';
require_once ('tiki-setup.php');
include_once ('lib/categories/categlib.php');
include_once ("lib/imagegals/imagegallib.php");

if ($prefs['feature_galleries'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_galleries");

	$smarty->display("error.tpl");
	die;
}

if (empty($_REQUEST["galleryId"]) && $_REQUEST["galleryId"]!='0') {
	$smarty->assign('msg', tra("No gallery indicated"));

	$smarty->display("error.tpl");
	die;
}

if ($_REQUEST["galleryId"]!='0' && $imagegallib->get_gallery($_REQUEST["galleryId"])===false) {
	$smarty->assign('msg', tra("This gallery does not exist"));
	$smarty->display("error.tpl");
	die;
}
   
$smarty->assign('individual', 'n');

if ($userlib->object_has_one_permission($_REQUEST["galleryId"], 'image gallery')) {
	$smarty->assign('individual', 'y');

	if ($tiki_p_admin != 'y') {
		// Now get all the permissions that are set for this type of permissions 'image gallery'
		$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'image galleries');

		foreach ($perms["data"] as $perm) {
			$permName = $perm["permName"];

			if ($userlib->object_has_permission($user, $_REQUEST["galleryId"], 'image gallery', $permName)) {
				$$permName = 'y';

				$smarty->assign("$permName", 'y');
			} else {
				$$permName = 'n';

				$smarty->assign("$permName", 'n');
			}
		}
	} 
} elseif ($tiki_p_admin != 'y' && $prefs['feature_categories'] == 'y') {
	$perms_array = $categlib->get_object_categories_perms($user, 'image gallery', $_REQUEST['galleryId']);
   	if ($perms_array) {
   		$is_categorized = TRUE;
    	foreach ($perms_array as $perm => $value) {
    		$$perm = $value;
    	}
   	} else {
   		$is_categorized = FALSE;
   	}
	if ($is_categorized && isset($tiki_p_view_categorized) && $tiki_p_view_categorized != 'y') {
		if (!isset($user)){
			$smarty->assign('msg',$smarty->fetch('modules/mod-login_box.tpl'));
			$smarty->assign('errortitle',tra("Please login"));
		} else {
			$smarty->assign('msg',tra("Permission denied you cannot view this page"));
    	}
	    $smarty->display("error.tpl");
		die;
	}
}

if ($tiki_p_admin_galleries == 'y') {
	$tiki_p_view_image_gallery = 'y';

	$smarty->assign("tiki_p_view_image_gallery", 'y');
	$tiki_p_upload_images = 'y';
	$smarty->assign("tiki_p_upload_images", 'y');
	$tiki_p_create_galleries = 'y';
	$smarty->assign("tiki_p_create_galleries", 'y');
}

if ($tiki_p_view_image_gallery != 'y') {
	$smarty->assign('msg', tra("Permission denied you cannot access this gallery"));

	$smarty->display("error.tpl");
	die;
}

/*
if($tiki_p_upload_images != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot upload images"));
  $smarty->display("error.tpl");
  die;  
}
*/
if ($_REQUEST["galleryId"] != 0) {
	// To browse the gallery the user has to be admin, the owner or the gallery has to be public
	$gal_info = $imagegallib->get_gallery($_REQUEST["galleryId"]);
//$smarty->assign_by_ref('theme',$gal_info["theme"]);
//$smarty->assign('use_theme','y');
/*
if($user!='admin' && $user!=$gal_info["user"] && $gal_info["public"]!='y') {
  $smarty->assign('msg',tra("Permission denied you cannot browse this gallery"));
  $smarty->display("error.tpl");
  die;  
}
*/
} else {
	$gal_info["galleryId"] = 0;

	$gal_info["user"] = 'admin';
	$gal_info["name"] = 'System';
	$gal_info["public"] = 'y';
	$gal_info["description"] = 'System Gallery';
	$gal_info["sortdirection"] = 'desc';
	$gal_info["sortorder"] = 'created';
}

$smarty->assign_by_ref('owner', $gal_info["user"]);
$smarty->assign_by_ref('public', $gal_info["public"]);
$smarty->assign_by_ref('galleryId', $_REQUEST["galleryId"]);

$imagegallib->add_gallery_hit($_REQUEST["galleryId"]);

if (isset($_REQUEST["remove"])) {
	// To remove an image the user must be the owner or admin
	if ($tiki_p_admin_galleries != 'y' && (!$user || $user != $gal_info["user"])) {
		$smarty->assign('msg', tra("Permission denied you cannot remove images from this gallery"));
		$smarty->display("error.tpl");
		die;
	}
  $area = 'delgallery';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$imagegallib->remove_image($_REQUEST["remove"]);
  } else {
    key_get($area);
  }
}

if (isset($_REQUEST["rebuild"])) {
	check_ticket('list-gal');
	// To remove an image the user must be the owner or admin
	if ($tiki_p_admin_galleries != 'y' && (!$user || $user != $gal_info["user"])) {
		$smarty->assign('msg', tra("Permission denied you cannot remove images from this gallery"));

		$smarty->display("error.tpl");
		die;
	}

	$imagegallib->rebuild_thumbnails($_REQUEST["rebuild"]);
}

$smarty->assign('system', 'n');

if ($_REQUEST["galleryId"] == 0) {
	$info["thumbSizeX"] = 100;

	$info["thumbSizeY"] = 100;
	$info["galleryId"] = 0;
	$info["user"] = 'admin';
	$info["name"] = 'System';
	$info["public"] = 'y';
	$info["description"] = 'System Gallery';
	$info["sortdirection"] = 'desc';
	$info["sortorder"] = 'created';

	$smarty->assign('system', 'y');
} else {
	$info = $imagegallib->get_gallery($_REQUEST["galleryId"]);
}

if (!isset($info["maxRows"]))
	$info["maxRows"] = 10;

if (!isset($info["rowImages"]))
	$info["rowImages"] = 5;

if ($info["maxRows"] == 0)
	$info["maxRows"] = 10;

if ($info["rowImages"] == 0)
	$info["rowImages"] = 6;

$maxRecords = $info["maxRows"] * $info["rowImages"];
$smarty->assign_by_ref('rowImages', $info["rowImages"]);
$smarty->assign('rowImages2', $info["rowImages"] - 1);
$smarty->assign_by_ref('thx', $info["thumbSizeX"]);
$smarty->assign_by_ref('thy', $info["thumbSizeY"]);
$smarty->assign_by_ref('name', $info["name"]);
$smarty->assign_by_ref('description', $info["description"]);

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = $info['sortorder'].'_'.$info['sortdirection'];
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

$smarty->assign_by_ref('sort_mode', $sort_mode);

// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
// if sortMode is not set then use lastModif_desc
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$images = $imagegallib->get_images($offset, $maxRecords, $sort_mode, $find, $_REQUEST["galleryId"]);
$cant_pages = ceil($images["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($images["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('images', $images["data"]);

$cat_type = 'image gallery';
$cat_objid = $_REQUEST["galleryId"];
include_once ('tiki-section_options.php');

ask_ticket('list-gal');

if ($prefs['feature_actionlog'] == 'y') {
	include_once('lib/logs/logslib.php');
	$logslib->add_action('Viewed', $_REQUEST['galleryId'], 'image gallery');
}

// Display the template
$smarty->assign('mid', 'tiki-list_gallery.tpl');
$smarty->display("tiki.tpl");

?>
