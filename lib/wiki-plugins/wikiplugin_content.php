<?php

function wikiplugin_content_info() {
	return array(
		'name' => tra( 'Dynamic Content' ),
		'documentation' => 'PluginContent',		
		'description' => tra( 'Includes content from the dynamic content system.' ),
		'prefs' => array( 'feature_dynamic_content', 'wikiplugin_content' ),
		'filter' => 'text',
		'params' => array(
			'id' => array(
				'required' => true,
				'name' => tra('Content ID'),
				'description' => tra('Numeric value.'),
				'filter' => 'digits',
			),
		),
	);
}

function wikiplugin_content( $data, $params) {

	global $dcslib; require_once 'lib/dcs/dcslib.php';

	if( $params['id'] )
		return $dcslib->get_actual_content((int) $params['id']);
}
