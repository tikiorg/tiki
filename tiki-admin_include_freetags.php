<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_freetags.php,v 1.5 2006-12-11 22:36:15 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
//smarty is not there - we need setup
require_once('tiki-setup.php');  
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

if (isset($_REQUEST["freetagsset3d"])) {
	check_ticket('admin-inc-freetags');
	$pref_toggles = array(
	'freetags_browse_show_cloud',
	'freetags_feature_3d',
	);
	foreach ($pref_toggles as $toggle) {
		simple_set_toggle ($toggle);
	}
	$pref_values = array(
	'freetags_browse_amount_tags_in_cloud',
	'freetags_3d_width',
	'freetags_3d_height',
	'freetags_3d_navigation_depth',
	'freetags_3d_feed_animation_interval',
	'freetags_3d_existing_page_color',
	'freetags_3d_missing_page_color',
	'freetags_3d_autoload',
	'freetags_3d_camera_distance',
	'freetags_3d_fov',
	'freetags_3d_node_size',
	'freetags_3d_text_size'
	'freetags_3d_friction_constant',
	'freetags_3d_elastic_constant',
	'freetags_3d_eletrostatic_constant',
	'freetags_3d_spring_size',
	'freetags_3d_node_mass',
	'freetags_3d_node_charge'
	);
	foreach ($pref_values as $value) {
		simple_set_value ($value);
	}
	if (isset($_REQUEST["freetags_3d_adjust_camera"]) && $_REQUEST["freetags_3d_adjust_camera"] == "on") {
	    $tikilib->set_preference("freetags_3d_adjust_camera", 'true');
	    $smarty->assign("freetags_3d_adjust_camera", 'true');
	} else {
	    $tikilib->set_preference("freetags_3d_adjust_camera", 'false');
	    $smarty->assign("freetags_3d_adjust_camera", 'false');
	}
}

ask_ticket('admin-inc-freetags');
?>
