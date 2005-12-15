<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_freetags.php,v 1.1 2005-12-15 21:41:44 amette Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
//smarty is not there - we need setup
require_once('tiki-setup.php');  
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

if (isset($_REQUEST["wikiset3d"])) {
        check_ticket('admin-inc-freetags');
	if (isset($_REQUEST["wiki_feature_3d"]) && $_REQUEST["wiki_feature_3d"] == "on") {
	    $tikilib->set_preference("wiki_feature_3d", 'y');
	    $smarty->assign("wiki_feature_3d", 'y');
	} else {
	    $tikilib->set_preference("wiki_feature_3d", 'n');
	    $smarty->assign("wiki_feature_3d", 'n');
	}

	if (isset($_REQUEST["wiki_3d_width"])) {
		$tikilib->set_preference("wiki_3d_width", $_REQUEST["wiki_3d_width"]);
		$smarty->assign('wiki_3d_width', $_REQUEST["wiki_3d_width"]);
	}

	if (isset($_REQUEST["wiki_3d_height"])) {
		$tikilib->set_preference("wiki_3d_height", $_REQUEST["wiki_3d_height"]);
		$smarty->assign('wiki_3d_height', $_REQUEST["wiki_3d_height"]);
	}

	if (isset($_REQUEST["wiki_3d_navigation_depth"])) {
		$tikilib->set_preference("wiki_3d_navigation_depth", $_REQUEST["wiki_3d_navigation_depth"]);
		$smarty->assign('wiki_3d_navigation_depth', $_REQUEST["wiki_3d_navigation_depth"]);
	}

	if (isset($_REQUEST["wiki_3d_feed_animation_interval"])) {
		$tikilib->set_preference("wiki_3d_feed_animation_interval", $_REQUEST["wiki_3d_feed_animation_interval"]);
		$smarty->assign('wiki_3d_feed_animation_interval', $_REQUEST["wiki_3d_feed_animation_interval"]);
	}

	if (isset($_REQUEST["wiki_3d_existing_page_color"])) {
		$tikilib->set_preference("wiki_3d_existing_page_color", $_REQUEST["wiki_3d_existing_page_color"]);
		$smarty->assign('wiki_3d_existing_page_color', $_REQUEST["wiki_3d_existing_page_color"]);
	}

	if (isset($_REQUEST["wiki_3d_missing_page_color"])) {
		$tikilib->set_preference("wiki_3d_missing_page_color", $_REQUEST["wiki_3d_missing_page_color"]);
		$smarty->assign('wiki_3d_missing_page_color', $_REQUEST["wiki_3d_missing_page_color"]);
	}

	/* new fields */
	if (isset($_REQUEST["wiki_3d_autoload"])) {
		$tikilib->set_preference("wiki_3d_autoload", $_REQUEST["wiki_3d_autoload"]);
		$smarty->assign('wiki_3d_autoload', $_REQUEST["wiki_3d_autoload"]);
	}

	if (isset($_REQUEST["wiki_3d_camera_distance"])) {
		$tikilib->set_preference("wiki_3d_camera_distance", $_REQUEST["wiki_3d_camera_distance"]);
		$smarty->assign('wiki_3d_camera_distance', $_REQUEST["wiki_3d_camera_distance"]);
	}

	if (isset($_REQUEST["wiki_3d_adjust_camera"])) {
		$tikilib->set_preference("wiki_3d_adjust_camera", $_REQUEST["wiki_3d_adjust_camera"]);
		$smarty->assign('wiki_3d_adjust_camera', $_REQUEST["wiki_3d_adjust_camera"]);
	}

	if (isset($_REQUEST["wiki_3d_fov"])) {
		$tikilib->set_preference("wiki_3d_fov", $_REQUEST["wiki_3d_fov"]);
		$smarty->assign('wiki_3d_fov', $_REQUEST["wiki_3d_fov"]);
	}

	if (isset($_REQUEST["wiki_3d_node_size"])) {
		$tikilib->set_preference("wiki_3d_node_size", $_REQUEST["wiki_3d_node_size"]);
		$smarty->assign('wiki_3d_node_size', $_REQUEST["wiki_3d_node_size"]);
	}

	if (isset($_REQUEST["wiki_3d_text_size"])) {
		$tikilib->set_preference("wiki_3d_text_size", $_REQUEST["wiki_3d_text_size"]);
		$smarty->assign('wiki_3d_text_size', $_REQUEST["wiki_3d_text_size"]);
	}

	if (isset($_REQUEST["wiki_3d_friction_constant"])) {
		$tikilib->set_preference("wiki_3d_friction_constant", $_REQUEST["wiki_3d_friction_constant"]);
		$smarty->assign('wiki_3d_friction_constant', $_REQUEST["wiki_3d_friction_constant"]);
	}

	if (isset($_REQUEST["wiki_3d_elastic_constant"])) {
		$tikilib->set_preference("wiki_3d_elastic_constant", $_REQUEST["wiki_3d_elastic_constant"]);
		$smarty->assign('wiki_3d_elastic_constant', $_REQUEST["wiki_3d_elastic_constant"]);
	}

	if (isset($_REQUEST["wiki_3d_eletrostatic_constant"])) {
		$tikilib->set_preference("wiki_3d_eletrostatic_constant", $_REQUEST["wiki_3d_eletrostatic_constant"]);
		$smarty->assign('wiki_3d_eletrostatic_constant', $_REQUEST["wiki_3d_eletrostatic_constant"]);
	}

	if (isset($_REQUEST["wiki_3d_spring_size"])) {
		$tikilib->set_preference("wiki_3d_spring_size", $_REQUEST["wiki_3d_spring_size"]);
		$smarty->assign('wiki_3d_spring_size', $_REQUEST["wiki_3d_spring_size"]);
	}

	if (isset($_REQUEST["wiki_3d_node_mass"])) {
		$tikilib->set_preference("wiki_3d_node_mass", $_REQUEST["wiki_3d_node_mass"]);
		$smarty->assign('wiki_3d_node_mass', $_REQUEST["wiki_3d_node_mass"]);
	}

	if (isset($_REQUEST["wiki_3d_node_charge"])) {
		$tikilib->set_preference("wiki_3d_node_charge", $_REQUEST["wiki_3d_node_charge"]);
		$smarty->assign('wiki_3d_node_charge', $_REQUEST["wiki_3d_node_charge"]);
	}
}

ask_ticket('admin-inc-freetags');
?>
