<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_gal.php,v 1.4 2003-12-28 20:12:51 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
include_once('lib/imagegals/imagegallib.php');

if (isset($_REQUEST["galset"]) && isset($_REQUEST["homeGallery"])) {
	check_ticket('admin-inc-gal');
	$tikilib->set_preference("home_gallery", $_REQUEST["homeGallery"]);

	$smarty->assign('home_gallery', $_REQUEST["homeGallery"]);
}

if (isset($_REQUEST["galfeatures"])) {
	check_ticket('admin-inc-gal');
	if (isset($_REQUEST["feature_gal_rankings"]) && $_REQUEST["feature_gal_rankings"] == "on") {
		$tikilib->set_preference("feature_gal_rankings", 'y');

		$smarty->assign("feature_gal_rankings", 'y');
	} else {
		$tikilib->set_preference("feature_gal_rankings", 'n');

		$smarty->assign("feature_gal_rankings", 'n');
	}

	$tikilib->set_preference("gal_use_db", $_REQUEST["gal_use_db"]);
	$smarty->assign('gal_use_db', $_REQUEST["gal_use_db"]);
	$tikilib->set_preference("gal_use_lib", $_REQUEST["gal_use_lib"]);
	$smarty->assign('gal_use_lib', $_REQUEST["gal_use_lib"]);
	$tikilib->set_preference("gal_use_dir", $_REQUEST["gal_use_dir"]);
	$smarty->assign('gal_use_dir', $_REQUEST["gal_use_dir"]);

	$tikilib->set_preference("gal_match_regex", $_REQUEST["gal_match_regex"]);
	$smarty->assign('gal_match_regex', $_REQUEST["gal_match_regex"]);
	$tikilib->set_preference("gal_nmatch_regex", $_REQUEST["gal_nmatch_regex"]);
	$smarty->assign('gal_nmatch_regex', $_REQUEST["gal_nmatch_regex"]);

	if (isset($_REQUEST["feature_image_galleries_comments"]) && $_REQUEST["feature_image_galleries_comments"] == "on") {
		$tikilib->set_preference("feature_image_galleries_comments", 'y');

		$smarty->assign("feature_image_galleries_comments", 'y');
	} else {
		$tikilib->set_preference("feature_image_galleries_comments", 'n');

		$smarty->assign("feature_image_galleries_comments", 'n');
	}
}

if (isset($_REQUEST["rmvorphimg"])) {
	check_ticket('admin-inc-gal');
	$adminlib->remove_orphan_images();
}

if (isset($_REQUEST['imagegallistprefs'])) {
	check_ticket('admin-inc-gal');
	if (isset($_REQUEST["gal_list_name"]) && $_REQUEST["gal_list_name"] == "on") {
		$tikilib->set_preference("gal_list_name", 'y');

		$smarty->assign("gal_list_name", 'y');
	} else {
		$tikilib->set_preference("gal_list_name", 'n');

		$smarty->assign("gal_list_name", 'n');
	}

	if (isset($_REQUEST["gal_list_description"]) && $_REQUEST["gal_list_description"] == "on") {
		$tikilib->set_preference("gal_list_description", 'y');

		$smarty->assign("gal_list_description", 'y');
	} else {
		$tikilib->set_preference("gal_list_description", 'n');

		$smarty->assign("gal_list_description", 'n');
	}

	if (isset($_REQUEST["gal_list_created"]) && $_REQUEST["gal_list_created"] == "on") {
		$tikilib->set_preference("gal_list_created", 'y');

		$smarty->assign("gal_list_created", 'y');
	} else {
		$tikilib->set_preference("gal_list_created", 'n');

		$smarty->assign("gal_list_created", 'n');
	}

	if (isset($_REQUEST["gal_list_lastmodif"]) && $_REQUEST["gal_list_lastmodif"] == "on") {
		$tikilib->set_preference("gal_list_lastmodif", 'y');

		$smarty->assign("gal_list_lastmodif", 'y');
	} else {
		$tikilib->set_preference("gal_list_lastmodif", 'n');

		$smarty->assign("gal_list_lastmodif", 'n');
	}

	if (isset($_REQUEST["gal_list_user"]) && $_REQUEST["gal_list_user"] == "on") {
		$tikilib->set_preference("gal_list_user", 'y');

		$smarty->assign("gal_list_user", 'y');
	} else {
		$tikilib->set_preference("gal_list_user", 'n');

		$smarty->assign("gal_list_user", 'n');
	}

	if (isset($_REQUEST["gal_list_imgs"]) && $_REQUEST["gal_list_imgs"] == "on") {
		$tikilib->set_preference("gal_list_imgs", 'y');

		$smarty->assign("gal_list_imgs", 'y');
	} else {
		$tikilib->set_preference("gal_list_imgs", 'n');

		$smarty->assign("gal_list_imgs", 'n');
	}

	if (isset($_REQUEST["gal_list_visits"]) && $_REQUEST["gal_list_visits"] == "on") {
		$tikilib->set_preference("gal_list_visits", 'y');

		$smarty->assign("gal_list_visits", 'y');
	} else {
		$tikilib->set_preference("gal_list_visits", 'n');

		$smarty->assign("gal_list_visits", 'n');
	}
}

if (isset($_REQUEST["imagegalcomprefs"])) {
	check_ticket('admin-inc-gal');
	if (isset($_REQUEST["image_galleries_comments_per_page"])) {
		$tikilib->set_preference("image_galleries_comments_per_page", $_REQUEST["image_galleries_comments_per_page"]);

		$smarty->assign('image_galleries_comments_per_page', $_REQUEST["image_galleries_comments_per_page"]);
	}

	if (isset($_REQUEST["image_galleries_comments_default_ordering"])) {
		$tikilib->set_preference(
			"image_galleries_comments_default_ordering", $_REQUEST["image_galleries_comments_default_ordering"]);

		$smarty->assign('image_galleries_comments_default_ordering', $_REQUEST["image_galleries_comments_default_ordering"]);
	}
}

$galleries = $tikilib->list_visible_galleries(0, -1, 'name_desc', 'admin', '');
$smarty->assign_by_ref('galleries', $galleries["data"]);

$smarty->assign("gal_match_regex", $tikilib->get_preference("gal_match_regex", ''));
ask_ticket('admin-inc-gal');
?>
