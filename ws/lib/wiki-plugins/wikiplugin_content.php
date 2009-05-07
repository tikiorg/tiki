<?php

function wikiplugin_content_info() {
	return array(
		'name' => tra( 'Dynamic Content' ),
		'documentation' => 'PluginContent',		
		'description' => tra( 'Includes content from the dynamic content system.' ),
		'prefs' => array( 'feature_dynamic_content', 'wikiplugin_content' ),
		'params' => array(
			'id' => array(
				'required' => true,
				'name' => tra('Content ID'),
				'description' => tra('Numeric value.'),
			),
		),
	);
}

function wikiplugin_content( $data, $params) {

	global $tikilib;

	if( $params['id'] )
		return $tikilib->get_actual_content((int) $params['id']);
}

?>
