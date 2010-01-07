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

function wikiplugin_rcontent( $data, $params, $offset, $parseOptions) {

	global $dcslib; include_once('lib/dcs/dcslib.php');

	$lang = null;
	if( isset( $parseOptions['language'] ) ) {
		$lang = $parseOptions['language'];
	}

	if( $params['id'] )
		return $dcslib->get_random_content((int) $params['id'], $lang);
}
