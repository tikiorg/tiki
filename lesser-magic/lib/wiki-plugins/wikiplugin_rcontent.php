<?php

function wikiplugin_rcontent_info() {
	return array(
		'name' => tra( 'Random Dynamic Content' ),
		'documentation' => 'PluginRcontent',			
		'description' => tra( 'Includes random content from the dynamic content system.' ),
		'prefs' => array( 'feature_dynamic_content', 'wikiplugin_rcontent' ),
		'params' => array(
			'id' => array(
				'required' => true,
				'name' => tra('Content ID'),
				'description' => tra('Numeric value.'),
			),
		),
	);
}

function wikiplugin_rcontent( $data, $params) {

	global $tikilib;

	if( $params['id'] )
		return $tikilib->get_random_content((int) $params['id']);
}
