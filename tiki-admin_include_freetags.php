<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_freetags.php,v 1.4 2006-01-29 04:04:01 amette Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
//smarty is not there - we need setup
require_once('tiki-setup.php');  
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

if (isset($_REQUEST["freetagsset3d"])) {
        check_ticket('admin-inc-freetags');
	if (isset($_REQUEST["freetags_browse_show_cloud"]) && $_REQUEST["freetags_browse_show_cloud"] == "y") {
	    $tikilib->set_preference("freetags_browse_show_cloud", 'y');
	    $smarty->assign("freetags_browse_show_cloud", 'y');
	} else {
	    $tikilib->set_preference("freetags_browse_show_cloud", 'n');
	    $smarty->assign("freetags_browse_show_cloud", 'n');
	}
	if (isset($_REQUEST["freetags_browse_amount_tags_in_cloud"])) {
		$tikilib->set_preference("freetags_browse_amount_tags_in_cloud", $_REQUEST["freetags_browse_amount_tags_in_cloud"]);
		$smarty->assign('freetags_browse_amount_tags_in_cloud', $_REQUEST["freetags_browse_amount_tags_in_cloud"]);
	}

	if (isset($_REQUEST["freetags_feature_3d"]) && $_REQUEST["freetags_feature_3d"] == "on") {
	    $tikilib->set_preference("freetags_feature_3d", 'y');
	    $smarty->assign("freetags_feature_3d", 'y');
	} else {
	    $tikilib->set_preference("freetags_feature_3d", 'n');
	    $smarty->assign("freetags_feature_3d", 'n');
	}

	if (isset($_REQUEST["freetags_3d_width"])) {
		$tikilib->set_preference("freetags_3d_width", $_REQUEST["freetags_3d_width"]);
		$smarty->assign('freetags_3d_width', $_REQUEST["freetags_3d_width"]);
	}

	if (isset($_REQUEST["freetags_3d_height"])) {
		$tikilib->set_preference("freetags_3d_height", $_REQUEST["freetags_3d_height"]);
		$smarty->assign('freetags_3d_height', $_REQUEST["freetags_3d_height"]);
	}

	if (isset($_REQUEST["freetags_3d_navigation_depth"])) {
		$tikilib->set_preference("freetags_3d_navigation_depth", $_REQUEST["freetags_3d_navigation_depth"]);
		$smarty->assign('freetags_3d_navigation_depth', $_REQUEST["freetags_3d_navigation_depth"]);
	}

	if (isset($_REQUEST["freetags_3d_feed_animation_interval"])) {
		$tikilib->set_preference("freetags_3d_feed_animation_interval", $_REQUEST["freetags_3d_feed_animation_interval"]);
		$smarty->assign('freetags_3d_feed_animation_interval', $_REQUEST["freetags_3d_feed_animation_interval"]);
	}

	if (isset($_REQUEST["freetags_3d_existing_page_color"])) {
		$tikilib->set_preference("freetags_3d_existing_page_color", $_REQUEST["freetags_3d_existing_page_color"]);
		$smarty->assign('freetags_3d_existing_page_color', $_REQUEST["freetags_3d_existing_page_color"]);
	}

	if (isset($_REQUEST["freetags_3d_missing_page_color"])) {
		$tikilib->set_preference("freetags_3d_missing_page_color", $_REQUEST["freetags_3d_missing_page_color"]);
		$smarty->assign('freetags_3d_missing_page_color', $_REQUEST["freetags_3d_missing_page_color"]);
	}

	/* new fields */
	if (isset($_REQUEST["freetags_3d_autoload"])) {
		$tikilib->set_preference("freetags_3d_autoload", $_REQUEST["freetags_3d_autoload"]);
		$smarty->assign('freetags_3d_autoload', $_REQUEST["freetags_3d_autoload"]);
	}

	if (isset($_REQUEST["freetags_3d_camera_distance"])) {
		$tikilib->set_preference("freetags_3d_camera_distance", $_REQUEST["freetags_3d_camera_distance"]);
		$smarty->assign('freetags_3d_camera_distance', $_REQUEST["freetags_3d_camera_distance"]);
	}

	if (isset($_REQUEST["freetags_3d_adjust_camera"]) && $_REQUEST["freetags_3d_adjust_camera"] == "on") {
	    $tikilib->set_preference("freetags_3d_adjust_camera", 'true');
	    $smarty->assign("freetags_3d_adjust_camera", 'true');
	} else {
	    $tikilib->set_preference("freetags_3d_adjust_camera", 'false');
	    $smarty->assign("freetags_3d_adjust_camera", 'false');
	}

	if (isset($_REQUEST["freetags_3d_fov"])) {
		$tikilib->set_preference("freetags_3d_fov", $_REQUEST["freetags_3d_fov"]);
		$smarty->assign('freetags_3d_fov', $_REQUEST["freetags_3d_fov"]);
	}

	if (isset($_REQUEST["freetags_3d_node_size"])) {
		$tikilib->set_preference("freetags_3d_node_size", $_REQUEST["freetags_3d_node_size"]);
		$smarty->assign('freetags_3d_node_size', $_REQUEST["freetags_3d_node_size"]);
	}

	if (isset($_REQUEST["freetags_3d_text_size"])) {
		$tikilib->set_preference("freetags_3d_text_size", $_REQUEST["freetags_3d_text_size"]);
		$smarty->assign('freetags_3d_text_size', $_REQUEST["freetags_3d_text_size"]);
	}

	if (isset($_REQUEST["freetags_3d_friction_constant"])) {
		$tikilib->set_preference("freetags_3d_friction_constant", $_REQUEST["freetags_3d_friction_constant"]);
		$smarty->assign('freetags_3d_friction_constant', $_REQUEST["freetags_3d_friction_constant"]);
	}

	if (isset($_REQUEST["freetags_3d_elastic_constant"])) {
		$tikilib->set_preference("freetags_3d_elastic_constant", $_REQUEST["freetags_3d_elastic_constant"]);
		$smarty->assign('freetags_3d_elastic_constant', $_REQUEST["freetags_3d_elastic_constant"]);
	}

	if (isset($_REQUEST["freetags_3d_eletrostatic_constant"])) {
		$tikilib->set_preference("freetags_3d_eletrostatic_constant", $_REQUEST["freetags_3d_eletrostatic_constant"]);
		$smarty->assign('freetags_3d_eletrostatic_constant', $_REQUEST["freetags_3d_eletrostatic_constant"]);
	}

	if (isset($_REQUEST["freetags_3d_spring_size"])) {
		$tikilib->set_preference("freetags_3d_spring_size", $_REQUEST["freetags_3d_spring_size"]);
		$smarty->assign('freetags_3d_spring_size', $_REQUEST["freetags_3d_spring_size"]);
	}

	if (isset($_REQUEST["freetags_3d_node_mass"])) {
		$tikilib->set_preference("freetags_3d_node_mass", $_REQUEST["freetags_3d_node_mass"]);
		$smarty->assign('freetags_3d_node_mass', $_REQUEST["freetags_3d_node_mass"]);
	}

	if (isset($_REQUEST["freetags_3d_node_charge"])) {
		$tikilib->set_preference("freetags_3d_node_charge", $_REQUEST["freetags_3d_node_charge"]);
		$smarty->assign('freetags_3d_node_charge', $_REQUEST["freetags_3d_node_charge"]);
	}
}

ask_ticket('admin-inc-freetags');
?>
