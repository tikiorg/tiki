<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
  
if (!isset($_REQUEST["nocache"]))
	session_cache_limiter('private_no_expire');

//include_once ("tiki-setup_base.php");
include_once ("tiki-setup.php");
$imagegallib = TikiLib::lib('imagegal');

// show_image.php
// application to display an image from the database with 
// option to resize the image dynamically creating a thumbnail on the fly.
// If a gallery ID is specified gallery's representative image is displayed.
// you have to check if the user has permission to see this gallery
if ($prefs['feature_galleries'] != 'y') {
   header("HTTP/1.0 404 Not Found");
	die;
}

$id = 0;
if (isset($_REQUEST["name"])) {
	if (!empty($_REQUEST['galleryId'])) {
		$id=$imagegallib->get_imageid_byname($_REQUEST['name'], $_REQUEST['galleryId']);
	} else {
		$id = $imagegallib->get_imageid_byname($_REQUEST['name']);
	}
} elseif (isset($_REQUEST["id"])) {
	$id=$_REQUEST["id"];
} elseif (isset($_REQUEST["galleryId"])) {
	$id=$imagegallib->get_gallery_image($_REQUEST["galleryId"], 'default');
}

if (!$id) {
   header("HTTP/1.0 404 Not Found");
	die;
}

$galleryId = $imagegallib->get_gallery_from_image($id);
$galperms = Perms::get(array( 'type' => 'image gallery', 'object' => $galleryId ));

if ( ! $galperms->view_image_gallery ) {
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

if ($imagegallib->get_etag($id, $itype, $scalesize)!==false) {

# Client-Side image cache (based on Etag Headers)
# Etag value is based on the md5 hash of the image. It should change everytime the image changes. See http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.19

#if the client sends the HTTP_IF_NONE_MATCH header(because it received the etag for this image the first time he saw it) we check that the received etag is the same as the actual etag (this is, the image haven't changed) and if it's equal, we send the "Not modified" header(304)
  if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $imagegallib->etag) {
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

// close the session for speedup
session_write_close();

header("Content-type: $type");
header("Etag: ".$imagegallib->etag);

//line commented out by teedog
//I noticed that the browser sometimes sends erroneous "Range:" headers when calling show_image.php
//which makes images fail to load.  It appears that commenting out the "Content-length:" header
//makes this problem go away.  From what I found through Google, "Content-length:" is mostly optional
//so commenting it out should not cause problems.
// note that the problem was not so mysterious : 912614
// header("Content-length: ".$imagegallib->filesize);

header("Content-Disposition: inline; filename=\"" . $imagegallib->filename.'"');
//if ($data["path"]) {
//  readfile($prefs['gal_use_dir'].$data["path"].$ter);
//} else {
echo $imagegallib->image;
//}
