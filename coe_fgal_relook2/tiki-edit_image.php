<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');

include_once ("lib/imagegals/imagegallib.php");

if ($prefs['feature_categories'] == 'y') {
	global $categlib;
	if (!is_object($categlib)) {
		include_once('lib/categories/categlib.php');
	}
}

$access->check_feature('feature_galleries');

// Sanity anyone?
if (!$_REQUEST['edit'] or !$_REQUEST['galleryId']) {
	$smarty->assign('msg', tra("Invalid request to edit an image"));

	$smarty->display("error.tpl");
	die;
}

$tikilib->get_perm_object( $_REQUEST['galleryId'], 'image gallery' );

$access->check_permission('tiki_p_upload_images');

$imageId=$_REQUEST['edit'];
$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1 = str_replace("tiki-edit_image", "tiki-browse_image", $foo["path"]);
$foo2 = str_replace("tiki-edit_image", "show_image", $foo["path"]);
$smarty->assign('url_browse', $tikilib->httpPrefix(). $foo1);
$smarty->assign('url_show', $tikilib->httpPrefix(). $foo2);

$gal_info = $imagegallib->get_gallery($_REQUEST["galleryId"]);

if (!isset($_REQUEST['sort_mode'])) {
	$sort_mode = $gal_info['sortorder'].'_'.$gal_info['sortdirection'];
} else $sort_mode = $_REQUEST['sort_mode'];
$smarty->assign('sort_mode', $sort_mode);

if (isset($_REQUEST["editimage"]) || isset($_REQUEST["editimage_andgonext"])) {
	check_ticket('edit-image');

	$access->check_permission('tiki_p_upload_images');

	if ($gal_info["thumbSizeX"] == 0)
		$gal_info["thumbSizeX"] = 80;

	if ($gal_info["thumbSizeY"] == 0)
		$gal_info["thumbSizeY"] = 80;

	// Check the user to be admin or owner or the gallery is public
	if ($tiki_p_admin_galleries != 'y' && (!$user || $user != $gal_info["user"]) && $gal_info["public"] != 'y') {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("You have permission to edit images but not in this gallery"));

		$smarty->display("error.tpl");
		die;
	}

	$error_msg = '';

	// Avoid warnings
	if ($prefs['feature_maps'] != 'y') {
	    $_REQUEST['lat'] = '';
	    $_REQUEST['lon'] = '';
	}

	if (!empty($_FILES['userfile']) && !empty($_FILES['userfile']['name'])) {
	  if ((!empty($prefs['gal_match_regex']) && !preg_match('/'.$prefs['gal_match_regex'].'/', $_FILES['userfile']['name'], $reqs))
		  || (!empty($prefs['gal_nmatch_regex']) && preg_match('/'.$prefs['gal_nmatch_regex'].'/', $_FILES['userfile']['name'], $reqs))) {
			$smarty->assign('msg', tra('Invalid imagename (using filters for filenames)'));
			$smarty->display('error.tpl');
			die;
		}
	}

	if ($imagegallib->edit_image($imageId, $_REQUEST['name'], $_REQUEST['description'],$_REQUEST['lat'],$_REQUEST['lon'], $_FILES['userfile'])) {
		$smarty->assign('show', 'y');
		$cat_type = 'image';
		$cat_objid = $imageId;
		$cat_desc = $_REQUEST['description'];
		$cat_lat = $_REQUEST['lat'];
		$cat_lon = $_REQUEST['lon'];
		$cat_name = $_REQUEST['name'];
		$cat_href = "tiki-browse_image.php?imageId=".$cat_objid;
		include_once("categorize.php");

		if (isset($_REQUEST["editimage_andgonext"])) {
		        $prevnext = $imagegallib->get_prev_and_next_image($sort_mode, NULL, $imageId, $_REQUEST["galleryId"]);
		        if ($prevnext['next']) $imageId=$prevnext['next'];
		}

	} else {
		$smarty->assign('msg', tra("Failed to edit the image"));

		$smarty->display("error.tpl");
		die;
	}
}

$info = $imagegallib->get_image($imageId);
$smarty->assign_by_ref('imageId', $imageId);
$smarty->assign_by_ref('galleryId', $info['galleryId']);
$smarty->assign_by_ref('name', $info['name']);
$smarty->assign_by_ref('description', $info['description']);
$smarty->assign_by_ref('lat', $info['lat']);
$smarty->assign_by_ref('lon', $info['lon']);
$smarty->assign_by_ref('filename', $info['filename']);
$smarty->assign_by_ref('gal_info', $gal_info);

$cat_type = 'image';
$cat_objid = $imageId;
include_once ("categorize_list.php");

ask_ticket('edit-image');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-edit_image.tpl');
$smarty->display("tiki.tpl");
