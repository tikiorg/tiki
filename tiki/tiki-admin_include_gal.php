<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_gal.php,v 1.10 2004-06-14 19:30:11 redflo Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
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

	// Check for last character being a / or a \
	if (substr($_REQUEST["gal_use_dir"], -1) != "\\" && substr($_REQUEST["gal_use_dir"], -1) != "/" && $_REQUEST["gal_use_dir"] != "") {
		$_REQUEST["gal_use_dir"] .= "/";
	}

	$pref_simple_values = array(
	"gal_use_db",
	"gal_use_lib",
	"gal_use_dir",
	"gal_match_regex",
	"gal_nmatch_regex"
	);

	foreach ($pref_simple_values as $svitem) {
		simple_set_value ($svitem);
	}
}

if (isset($_REQUEST["rmvorphimg"])) {
	check_ticket('admin-inc-gal');
	$adminlib->remove_orphan_images();
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
	"gal_list_visits"
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

if($imagegallib->havegd) {
	$gdlib=tra('Detected, Version:').' '.$imagegallib->gdversion;
} else {
	$gdlib=tra('Not detected. Use at own risk!');
}
if($imagegallib->haveimagick) {
	$imagicklib=tra('Detected, Version:').' '.tra('Unknown');
} else {
	$imagicklib=tra('Not detected. Use at own risk!').' Imagick '.tra('can be downloaded from:').' <a href=\'http://pecl.php.net/\'>http://pecl.php.net/</a>';
}

$smarty->assign('gdlib',$gdlib);
$smarty->assign('imagicklib',$imagicklib);

$galleries = $tikilib->list_visible_galleries(0, -1, 'name_desc', 'admin', '');
$smarty->assign_by_ref('galleries', $galleries["data"]);

$smarty->assign("gal_match_regex", $tikilib->get_preference("gal_match_regex", ''));
ask_ticket('admin-inc-gal');
?>
