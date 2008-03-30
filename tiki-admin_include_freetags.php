<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_include_freetags.php,v 1.9.2.5 2008-02-18 14:03:29 lphuberdeau Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
//smarty is not there - we need setup
require_once('tiki-setup.php');  
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

if (isset($_REQUEST["freetagsfeatures"])) {
	check_ticket('admin-inc-freetags');
	
	simple_set_toggle('freetags_browse_show_cloud');
	simple_set_toggle('freetags_lowercase_only');
	simple_set_toggle('freetags_preload_random_search');
	simple_set_toggle('freetags_multilingual');

	simple_set_value('freetags_browse_amount_tags_in_cloud');
	simple_set_value('freetags_browse_amount_tags_suggestion');
	simple_set_value('freetags_normalized_valid_chars');
	
	simple_set_value('freetags_cloud_colors');
}

if (isset($_REQUEST["cleanup"])) {
	check_ticket('admin-inc-freetags');
	global $freetaglib;
	if (!is_object($freetaglib)) {
		include_once('lib/freetag/freetaglib.php');
	}			
	$freetaglib->cleanup_tags();
}

if (isset($_REQUEST["morelikethisoptions"])) {
	check_ticket('admin-inc-freetags');
		
	simple_set_value('morelikethis_algorithm');
	simple_set_value('morelikethis_basic_mincommon');
}

if (isset($_REQUEST["freetagsset3d"])) {
	check_ticket('admin-inc-freetags');
	$pref_toggles = array(
	'freetags_feature_3d',
	'freetags_feature_3d',
	);
	foreach ($pref_toggles as $toggle) {
		simple_set_toggle ($toggle);
	}
	
  $pref_values = array(
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
	'freetags_3d_text_size',
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
	} else {
	    $tikilib->set_preference("freetags_3d_adjust_camera", 'false');
	}
}

ask_ticket('admin-inc-freetags');
?>
