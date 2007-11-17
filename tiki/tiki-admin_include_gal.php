<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_gal.php,v 1.24.2.2 2007-11-17 08:11:48 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


include_once('lib/imagegals/imagegallib.php');

if (isset($_REQUEST["galset"])) {
	check_ticket('admin-inc-gal');
	simple_set_value ("home_gallery");
}

if (isset($_REQUEST["galfeatures"])) {
	check_ticket('admin-inc-gal');
	simple_set_toggle ("feature_gal_rankings");
	simple_set_toggle ("feature_image_galleries_comments");
	simple_set_toggle ("feature_gal_slideshow");
	simple_set_toggle ("feature_gal_batch");

	// Check for last character being a / or a \
	// My next commit is to create a clas to put this code into
	if (substr($_REQUEST["gal_use_dir"], -1) != "\\" && substr($_REQUEST["gal_use_dir"], -1) != "/" && $_REQUEST["gal_use_dir"] != "") {
		$_REQUEST["gal_use_dir"] .= "/";
	}
	if (substr($_REQUEST["gal_batch_dir"], -1) != "\\" && substr($_REQUEST["gal_batch_dir"], -1) != "/" && $_REQUEST["gal_batch_dir"] != "") {
		$_REQUEST["gal_batch_dir"] .= "/";
	}

	$pref_simple_values = array(
	"gal_use_db",
	"gal_use_lib",
	"gal_use_dir",
	"gal_match_regex",
	"gal_nmatch_regex",
	"gal_batch_dir",
	'feature_image_gallery_mandatory_category',
	'gal_image_mouseover',
	);

	foreach ($pref_simple_values as $svitem) {
		simple_set_value ($svitem);
	}
}

if (isset($_REQUEST["rmvorphimg"])) {
	check_ticket('admin-inc-gal');
	$adminlib->remove_orphan_images();
}

if (isset($_REQUEST['mvimg']) && isset($_REQUEST['move_gallery'])) {
   check_ticket('admin-inc-gal');
   if(($_REQUEST['mvimg']=='to_fs' && $prefs['gal_use_db']=='n') ||
      ($_REQUEST['mvimg']=='to_db' && $prefs['gal_use_db']=='y')) {

     $mvresult=$imagegallib->move_gallery_store($_REQUEST['move_gallery'],$_REQUEST['mvimg']);
     $mvmsg=sprintf(tra('moved %d images, %d errors occured.'),$mvresult['moved_images'],$mvresult['errors']);
     if($mvresult['timeout']) {
	$mvmsg.=' '.tra('a timeout occured. Hit the reload button to move the rest');
     }
     $tikifeedback[]['mes']=$mvmsg;
  }
}

if (isset($_REQUEST['imagegallistprefs'])) {
	check_ticket('admin-inc-gal');

	$pref_toggles = array(
	"gal_list_name",
	"gal_list_description",
	"gal_list_created",
	"gal_list_lastmodif",
	"gal_list_user",
	"gal_list_imgs",
	"gal_list_visits",
	"preset_galleries_thumb"
	);

	foreach ($pref_toggles as $toggle) {
		simple_set_toggle ($toggle);
	}
}

if (isset($_REQUEST["imagegalcomprefs"])) {
	check_ticket('admin-inc-gal');
	simple_set_value ("image_galleries_comments_per_page");
	simple_set_value ("image_galleries_comments_default_order");
}

if (isset($_REQUEST['mvimg']) && isset($_REQUEST['move_gallery'])) {
   check_ticket('admin-inc-gal');
   if(($_REQUEST['mvimg']=='to_fs' && $prefs['gal_use_db']=='n') ||
      ($_REQUEST['mvimg']=='to_db' && $prefs['gal_use_db']=='y')) {

     $mvresult=$imagegallib->move_gallery_store($_REQUEST['move_gallery'],$_REQUEST['mvimg']);
     $mvmsg=sprintf(tra('moved %d images, %d errors occured.'),$mvresult['moved_images'],$mvresult['errors']);
     if($mvresult['timeout']) {
				$mvmsg.=' '.tra('a timeout occured. Hit the reload button to move the rest');
     }
     $tikifeedback[]['mes']=$mvmsg;
  }
}



	if (!isset($_REQUEST['maxRows'])) $_REQUEST['maxRows'] = $prefs['maxRowsGalleries'];
	if (!isset($_REQUEST['rowImages'])) $_REQUEST['rowImages'] =$prefs['rowImagesGalleries'] ;
	if (!isset($_REQUEST['thumbSizeX'])) $_REQUEST['thumbSizeX'] = $prefs['thumbSizeXGalleries'];
	if (!isset($_REQUEST['thumbSizeY'])) $_REQUEST['thumbSizeY'] = $prefs['thumbSizeYGalleries'];
	if (!isset($_REQUEST['scaleSize'])) $_REQUEST['scaleSize'] = $prefs['scaleSizeGalleries'];
        
        $tikilib->set_preference("maxRowsGalleries",  $_REQUEST['maxRows']);
        $tikilib->set_preference("rowImagesGalleries",  $_REQUEST['rowImages']);
        $tikilib->set_preference("thumbSizeXGalleries",  $_REQUEST['thumbSizeX']);
        $tikilib->set_preference("thumbSizeYGalleries",  $_REQUEST['thumbSizeY']);
	$tikilib->set_preference("scaleSizeGalleries",  $_REQUEST['scaleSize']);
        
	$smarty->assign('maxRows', $_REQUEST['maxRows']);
	$smarty->assign('rowImages', $_REQUEST['rowImages']);
	$smarty->assign('thumbSizeX', $_REQUEST['thumbSizeX']);
	$smarty->assign('thumbSizeY',$_REQUEST['thumbSizeY']);
	$smarty->assign('scaleSize', $_REQUEST['scaleSize']);

if($imagegallib->havegd) {
	$gdlib=tra('Detected, Version:').' '.$imagegallib->gdversion;
} else {
	$gdlib=tra('Not detected.');
}
if($imagegallib->haveimagick) {
	$imagicklib=tra('Detected, Version:').' '.tra('Unknown');
} else {
	$imagicklib=tra('Not detected.');
}

$smarty->assign('gdlib',$gdlib);
$smarty->assign('imagicklib',$imagicklib);

if ($prefs['feature_categories'] == 'y') {
	include_once('lib/categories/categlib.php');
	$catree = $categlib->get_all_categories();
	$smarty->assign('catree', $catree);
}

$galleries = $imagegallib->list_visible_galleries(0, -1, 'name_desc', 'admin', '');
$smarty->assign_by_ref('galleries', $galleries["data"]);

$smarty->assign("max_img_upload_size", $imagegallib->max_img_upload_size());

ask_ticket('admin-inc-gal');
?>
