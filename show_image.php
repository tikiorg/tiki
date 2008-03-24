<?php

// $Header: /cvsroot/tikiwiki/tiki/show_image.php,v 1.34.2.1 2007-12-07 05:56:37 mose Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

if (!isset($_REQUEST["nocache"]))
	session_cache_limiter ('private_no_expire');

//include_once ("tiki-setup_base.php");
include_once ("tiki-setup.php");
include_once ("lib/imagegals/imagegallib.php");

// show_image.php
// application to display an image from the database with 
// option to resize the image dynamically creating a thumbnail on the fly.
// you have to check if the user has permission to see this gallery
if ($prefs['feature_galleries'] != 'y') {
   header("HTTP/1.0 404 Not Found");
	die;
}

$id = 0;
if (isset($_REQUEST["name"])) {
	$id=$imagegallib->get_imageid_byname($_REQUEST["name"]);
} elseif (isset($_REQUEST["id"])) {
	$id=$_REQUEST["id"];
}

if (!$id) {
   header("HTTP/1.0 404 Not Found");
	die;
}

$galleryId = $imagegallib->get_gallery_from_image($id);

if ($userlib->object_has_one_permission($galleryId, 'image gallery')) {
	if ($tiki_p_admin != 'y') {
		// Now get all the permissions that are set for this type of permissions 'image gallery'
		$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'image galleries');

		foreach ($perms["data"] as $perm) {
			$permName = $perm["permName"];

			if ($userlib->object_has_permission($user, $galleryId, 'image gallery', $permName)) {
				$$permName = 'y';
			} else {
				$$permName = 'n';
			}
		}
	}
} elseif ($tiki_p_admin != 'y' && $prefs['feature_categories'] == 'y') {
	global $categlib;
	if (!is_object($categlib)) {
		include_once('lib/categories/categlib.php');
	}
	$perms_array = $categlib->get_object_categories_perms($user, 'image gallery', $galleryId);
   	if ($perms_array) {
   		$is_categorized = TRUE;
    	foreach ($perms_array as $perm => $value) {
    		$$perm = $value;
    	}
   	} else {
   		$is_categorized = FALSE;
   	}
	if ($is_categorized && isset($tiki_p_view_categorized) && $tiki_p_view_categorized != 'y') {
        header("HTTP/1.0 404 Not Found");
        die;
	}
}

if ($tiki_p_view_image_gallery != 'y' && $tiki_p_admin_galleries != 'y') {
    header("HTTP/1.0 404 Not Found");
    die;
}

$scalesize = 0;

if (isset($_REQUEST["thumb"])) {
	$itype = 't';
} elseif (isset($_REQUEST["scalesize"])) {
    if (is_numeric($_REQUEST["scalesize"]) && $_REQUEST["scalesize"] > 0) {
    	$itype = 's';
    	$scalesize = $_REQUEST["scalesize"];
    } else {
    	$itype = 'o';
    }
} else {
	$galdef = $imagegallib->get_gallery_default_scale($galleryId);
	if ($galdef =='o') {
    	$itype = 'o';
	} else {
    	$itype = 's';
    	$scalesize = $galdef;
    }
}

if($imagegallib->get_etag($id, $itype, $scalesize)!==false) {

# Client-Side image cache (based on Etag Headers)
# Etag value is based on the md5 hash of the image. It should change everytime the image changes. See http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.19

#if the client sends the HTTP_IF_NONE_MATCH header (because it received the etag for this image the first time he saw it) we check that the received etag is the same as the actual etag (this is, the image haven't changed) and if it's equal, we send the "Not modified" header (304)
  if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $imagegallib->etag){
           header("HTTP/1.0 304 Not Modified");
	   exit();
  }
}

$imagegallib->get_image($id, $itype, $scalesize);
	
if (!isset($imagegallib->image)) {
	// cannot scale image. Get original
	$imagegallib->get_image($id, 'o');
}

// do not count if it is a thumbnail or parameter 'nocount' set
// 'nocount' is set by tiki-browse_image to avoid double counting
if ((!isset($_REQUEST["thumb"])) && (!isset($_REQUEST["nocount"]))) {
	$imagegallib->add_image_hit($id);
}

$type = $imagegallib->filetype;

//echo"<pre>";print_r(get_defined_vars());echo"</pre>";

// close the session for speedup
session_write_close();

header ("Content-type: $type");
header ("Etag: ".$imagegallib->etag);

//line commented out by teedog
//I noticed that the browser sometimes sends erroneous "Range:" headers when calling show_image.php
//which makes images fail to load.  It appears that commenting out the "Content-length:" header
//makes this problem go away.  From what I found through Google, "Content-length:" is mostly optional
//so commenting it out should not cause problems.
// note that the problem was not so mysterious : 912614
// header ("Content-length: ".$imagegallib->filesize);

header ("Content-Disposition: inline; filename=\"" . $imagegallib->filename.'"');
//if($data["path"]) {
//  readfile($prefs['gal_use_dir'].$data["path"].$ter);
//} else {
echo $imagegallib->image;
//}
// ????? echo $data;
?>
