<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-galleries.php,v 1.15 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ("lib/imagegals/imagegallib.php");

// Now check permissions to access this page
/*
if($tiki_p_view != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot view pages like this page"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}
*/
if ($feature_galleries != 'y') {
	$smarty->assign('msg', tra("This feature is disabled"));

	$smarty->display("styles/$style_base/error.tpl");
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

// This check should be done before checking individual permissions
if ($tiki_p_view_image_gallery != 'y') {
	$smarty->assign('msg', tra("Permission denied you can not view this section"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

// Individual permissions are checked because we may be trying to edit the gallery

// Check here for indivdual permissions the objectType is 'image galleries' and the id is galleryId
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
}

$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo["path"] = str_replace("tiki-galleries", "tiki-browse_gallery", $foo["path"]);
$smarty->assign('url', httpPrefix(). $foo["path"]);

// Init smarty variables to blank values
//$smarty->assign('theme','');
$smarty->assign('name', '');
$smarty->assign('description', '');
$smarty->assign('maxRows', 10);
$smarty->assign('rowImages', 6);
$smarty->assign('thumbSizeX', 80);
$smarty->assign('thumbSizeY', 80);
$smarty->assign('public', 'n');
$smarty->assign('edited', 'n');
$smarty->assign('visible', 'y');
$smarty->assign('edit_mode', 'n');

// If we are editing an existing gallery prepare smarty variables
if (isset($_REQUEST["edit_mode"]) && $_REQUEST["edit_mode"]) {
	// Get information about this galleryID and fill smarty variables
	$smarty->assign('edit_mode', 'y');

	$smarty->assign('edited', 'y');

	if ($_REQUEST["galleryId"] > 0) {
		$info = $imagegallib->get_gallery_info($_REQUEST["galleryId"]);

		$scaleinfo = $imagegallib->get_gallery_scale_info($_REQUEST["galleryId"]);
		//$smarty->assign_by_ref('theme',$info["theme"]);
		$smarty->assign_by_ref('name', $info["name"]);
		$smarty->assign_by_ref('description', $info["description"]);
		$smarty->assign_by_ref('maxRows', $info["maxRows"]);
		$smarty->assign_by_ref('rowImages', $info["rowImages"]);
		$smarty->assign_by_ref('thumbSizeX', $info["thumbSizeX"]);
		$smarty->assign_by_ref('thumbSizeY', $info["thumbSizeY"]);
		$smarty->assign_by_ref('public', $info["public"]);
		$smarty->assign_by_ref('visible', $info["visible"]);
		$smarty->assign_by_ref('scaleinfo', $scaleinfo);
	}
}

// Process the insertion or modification of a gallery here
if (isset($_REQUEST["edit"])) {
	// Saving information
	// If the user is not gallery admin
	if ($tiki_p_admin_galleries != 'y') {
		if ($tiki_p_create_galleries != 'y') {
			// If you can't create a gallery then you can't edit a gallery because you can't have a gallery
			$smarty->assign('msg', tra("Permission denied you cannot create galleries and so you cant edit them"));

			$smarty->display("styles/$style_base/error.tpl");
			die;
		}

		// If the user can create a gallery then check if he can edit THIS gallery
		if ($_REQUEST["galleryId"] > 0) {
			$info = $tikilib->get_gallery_info($_REQUEST["galleryId"]);

			if (!$user || $info["user"] != $user) {
				$smarty->assign('msg', tra("Permission denied you cannot edit this gallery"));

				$smarty->display("styles/$style_base/error.tpl");
				die;
			}
		}
	}

	// Everything is ok so we proceed to edit the gallery
	$smarty->assign('edit_mode', 'y');
	//$smarty->assign_by_ref('theme',$_REQUEST["theme"]);
	$smarty->assign_by_ref('name', $_REQUEST["name"]);
	$smarty->assign_by_ref('description', $_REQUEST["description"]);
	$smarty->assign_by_ref('maxRows', $_REQUEST["maxRows"]);
	$smarty->assign_by_ref('rowImages', $_REQUEST["rowImages"]);
	$smarty->assign_by_ref('thumbSizeX', $_REQUEST["thumbSizeX"]);
	$smarty->assign_by_ref('thumbSizeY', $_REQUEST["thumbSizeY"]);

	if (isset($_REQUEST["visible"]) && $_REQUEST["visible"] == "on") {
		$smarty->assign('visible', 'y');

		$visible = 'y';
	} else {
		$visible = 'n';
	}

	if (!isset($_REQUEST["visible"])) {
		$visible = 'y';
	}

	$smarty->assign_by_ref('visible', $visible);

	if (isset($_REQUEST["public"]) && $_REQUEST["public"] == "on") {
		$smarty->assign('public', 'y');

		$public = 'y';
	} else {
		$public = 'n';
	}

	$smarty->assign_by_ref('public', $public);
	$gid = $imagegallib->replace_gallery($_REQUEST["galleryId"], $_REQUEST["name"], $_REQUEST["description"],
		'', $user, $_REQUEST["maxRows"], $_REQUEST["rowImages"], $_REQUEST["thumbSizeX"], $_REQUEST["thumbSizeY"], $public,
		$visible);

	#add scales
	if (isset($_REQUEST["scaleSizeX"])
		&& is_numeric($_REQUEST["scaleSizeX"]) && isset($_REQUEST["scaleSizeY"]) && is_numeric($_REQUEST["scaleSizeY"])) {
		$imagegallib->add_gallery_scale($gid, $_REQUEST["scaleSizeX"], $_REQUEST["scaleSizeY"]);
	}

	#remove scales
	$scaleinfo = $imagegallib->get_gallery_scale_info($_REQUEST["galleryId"]);

	# loop though scales to determine if a scale has to be removed
	while (list($num, $sci) = each($scaleinfo)) {
		$sizestr = $sci["xsize"] . "x" . $sci["ysize"];

		if (isset($_REQUEST[$sizestr]) && $_REQUEST[$sizestr] == 'on') {
			$imagegallib->remove_gallery_scale($_REQUEST["galleryId"], $sci["xsize"], $sci["ysize"]);
		}
	}

	$cat_type = 'image gallery';
	$cat_objid = $gid;
	$cat_desc = substr($_REQUEST["description"], 0, 200);
	$cat_name = $_REQUEST["name"];
	$cat_href = "tiki-browse_gallery.php?galleryId=" . $cat_objid;
	include_once ("categorize.php");

	$smarty->assign('edit_mode', 'n');
}

if (isset($_REQUEST["removegal"])) {
	if ($tiki_p_admin_galleries != 'y') {
		$info = $tikilib->get_gallery_info($_REQUEST["removegal"]);

		if (!$user || $info["user"] != $user) {
			$smarty->assign('msg', tra("Permission denied you cannot remove this gallery"));

			$smarty->display("styles/$style_base/error.tpl");
			die;
		}
	}

	$imagegallib->remove_gallery($_REQUEST["removegal"]);
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'name_desc';
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

// Get the list of libraries available for this user (or public galleries)
// GET ALL GALLERIES SINCE ALL GALLERIES ARE BROWSEABLE
$galleries = $tikilib->list_galleries($offset, $maxRecords, $sort_mode, 'admin', $find);

for ($i = 0; $i < count($galleries["data"]); $i++) {
	if ($userlib->object_has_one_permission($galleries["data"][$i]["galleryId"], 'image gallery')) {
		$galleries["data"][$i]["individual"] = 'y';

		if ($userlib->object_has_permission($user, $galleries["data"][$i]["galleryId"], 'image gallery',
			'tiki_p_view_image_gallery')) {
			$galleries["data"][$i]["individual_tiki_p_view_image_gallery"] = 'y';
		} else {
			$galleries["data"][$i]["individual_tiki_p_view_image_gallery"] = 'n';
		}

		if ($userlib->object_has_permission($user, $galleries["data"][$i]["galleryId"], 'image gallery', 'tiki_p_upload_images')) {
			$galleries["data"][$i]["individual_tiki_p_upload_images"] = 'y';
		} else {
			$galleries["data"][$i]["individual_tiki_p_upload_images"] = 'n';
		}

		if ($userlib->object_has_permission($user, $galleries["data"][$i]["galleryId"], 'image gallery', 'tiki_p_create_galleries'))
			{
			$galleries["data"][$i]["individual_tiki_p_create_galleries"] = 'y';
		} else {
			$galleries["data"][$i]["individual_tiki_p_create_galleries"] = 'n';
		}

		if ($tiki_p_admin == 'y' || $userlib->object_has_permission($user, $galleries["data"][$i]["galleryId"], 'image gallery',
			'tiki_p_admin_galleries')) {
			$galleries["data"][$i]["individual_tiki_p_create_galleries"] = 'y';

			$galleries["data"][$i]["individual_tiki_p_upload_images"] = 'y';
			$galleries["data"][$i]["individual_tiki_p_view_image_gallery"] = 'y';
		}
	} else {
		$galleries["data"][$i]["individual"] = 'n';
	}
}

// If there're more records then assign next_offset
$cant_pages = ceil($galleries["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($galleries["cant"] > ($offset + $maxRecords)) {
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

$smarty->assign_by_ref('galleries', $galleries["data"]);
//print_r($galleries["data"]);
$cat_type = 'image gallery';
$cat_objid = $_REQUEST["galleryId"];
include_once ("categorize_list.php");

$section = 'galleries';
include_once ('tiki-section_options.php');

// Display the template
$smarty->assign('mid', 'tiki-galleries.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>