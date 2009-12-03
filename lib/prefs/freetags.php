<?php

function prefs_freetags_list() {
	return array (
			'freetags_multilingual' => array(
			'name' => tra('Multilingual tags'),
			'description' => tra('Permits translation management of tags'),
			'help' => 'Tags',
			'type' => 'flag',
			'dependencies' => array(
				'feature_multilingual',
				'feature_freetags',
			),
		),
		'freetags_browse_show_cloud' => array(
			'name' => tra('Show tag cloud'),
			'type' => 'flag',
		),
		'freetags_browse_amount_tags_in_cloud' => array(
			'name' => tra('Maximum number of tags in cloud'),
			'type' => 'text',
			'size' => '5',
			'filter' => 'digits',
		),
		'freetags_show_middle' => array(
			'name' => tra('Show freetags in middle column'),
			'type' => 'flag',
		),
		'freetags_preload_random_search' => array(
			'name' => tra('Preload freetag random tag'),
			'type' => 'flag',
		),
		'freetags_browse_amount_tags_suggestion' => array(
			'name' => tra('Number of Tags to show in Tag Suggestions'),
			'type' => 'text',
			'size' => '4',
			'filter' => 'digits',
		),
		'freetags_normalized_valid_chars' => array(
			'name' => tra('Valid characters pattern'),
			'type' => 'text',
			'size' => '30',
		),
		'freetags_lowercase_only' => array(
			'name' => tra('Lowercase tags only'),
			'type' => 'flag',
		),
	
	
		// Used in templates/tiki-admin-include-freetags.tpl
		'freetags_cloud_colors' => array(
			'name' => tra('Random tag cloud colors'),
			'type' => '',
		),
	
		// Used in templates/tiki-admin-include-freetags.tpl
		'freetags_feature_3d' => array(
			'name' => tra('Enable freetags 3D browser'),
			'type' => '',
		),
	
		// Used in templates/tiki-admin-include-freetags.tpl
		'freetags_3d_width' => array(
			'name' => tra('Browser width'),
			'type' => '',
		),
	
		// Used in templates/tiki-admin-include-freetags.tpl
		'freetags_3d_height' => array(
			'name' => tra('Browser height'),
			'type' => '',
		),
	
		// Used in templates/tiki-admin-include-freetags.tpl
		'freetags_3d_navigation_depth' => array(
			'name' => tra('Navigation depth'),
			'type' => '',
		),
	
		// Used in templates/tiki-admin-include-freetags.tpl
		'freetags_3d_feed_animation_interval' => array(
			'name' => tra('Feed animation interval (milisecs)'),
			'type' => '',
		),
	
		// Used in templates/tiki-admin-include-freetags.tpl
		'freetags_3d_existing_page_color' => array(
			'name' => tra('Node color'),
			'type' => '',
		),
	
		// Used in templates/tiki-admin-include-freetags.tpl
		'freetags_3d_missing_page_color' => array(
			'name' => '',
			'type' => '',
		),
	
		// Used in templates/tiki-admin-include-freetags.tpl
		'freetags_3d_autoload' => array(
			'name' => '',
			'type' => '',
		),
	
		// Used in templates/tiki-admin-include-freetags.tpl
		'freetags_3d_camera_distance' => array(
			'name' => tra('Camera distance'),
			'type' => '',
		),
	
		// Used in templates/tiki-admin-include-freetags.tpl
		'freetags_3d_elastic_constant' => array(
			'name' => tra('Elastic constant'),
			'type' => '',
		),
	
		// Used in templates/tiki-admin-include-freetags.tpl
		'freetags_3d_eletrostatic_constant' => array(
			'name' => tra('Eletrostatic constant'),
			'type' => '',
		),
	
		// Used in templates/tiki-admin-include-freetags.tpl
		'freetags_3d_fov' => array(
			'name' => tra('Field of view'),
			'type' => '',
		),
	
		// Used in templates/tiki-admin-include-freetags.tpl
		'freetags_3d_friction_constant' => array(
			'name' => tra('Friction constant'),
			'type' => '',
		),
	
		// Used in templates/tiki-admin-include-freetags.tpl
		'freetags_3d_node_charge' => array(
			'name' => tra('Node charge'),
			'type' => '',
		),
	
		// Used in templates/tiki-admin-include-freetags.tpl
		'freetags_3d_node_mass' => array(
			'name' => tra('Node mass'),
			'type' => '',
		),
	
		// Used in templates/tiki-admin-include-freetags.tpl
		'freetags_3d_node_size' => array(
			'name' => tra('Node size'),
			'type' => '',
		),
	
		// Used in templates/tiki-admin-include-freetags.tpl
		'freetags_3d_spring_size' => array(
			'name' => tra('Spring (connection) size'),
			'type' => '',
		),
	
		// Used in templates/tiki-admin-include-freetags.tpl
		'freetags_3d_text_size' => array(
			'name' => tra('Text size'),
			'type' => '',
		),
	
		// Used in templates/tiki-admin-include-freetags.tpl
		'freetags_3d_adjust_camera' => array(
			'name' => tra('Camera distance adjusted relative to nearest node'),
			'type' => '',
		),
	);
}
