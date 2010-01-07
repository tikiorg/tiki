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
				'required' => false,
				'name' => tra('Content ID'),
				'description' => tra('Dynamic content ID. The value can be obtained in the listing.'),
				'filter' => 'digits',
			),
			'label' => array(
				'required' => false,
				'name' => tra('Content Label'),
				'description' => tra('Label of the dynamic content to display.'),
				'filter' => 'description',
			),
		),
	);
}

function wikiplugin_content( $data, $params, $offset, $parseOptions) {

	global $dcslib; require_once 'lib/dcs/dcslib.php';

	$lang = null;
	if( isset( $parseOptions['language'] ) ) {
		$lang = $parseOptions['language'];
	}

	if( $params['id'] ) {
		return $dcslib->get_actual_content((int) $params['id'], $lang);
	} elseif( $params['label'] ) {
		return $dcslib->get_actual_content_by_label( $params['label'], $lang);
	}
}
