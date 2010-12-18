<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_rcontent_info() {
	return array(
		'name' => tra( 'Random Dynamic Content' ),
		'documentation' => 'PluginRcontent',
		'description' => tra( 'Display pre-programmed changing content' ),
		'prefs' => array( 'feature_dynamic_content', 'wikiplugin_rcontent' ),
		'params' => array(
			'id' => array(
				'required' => true,
				'name' => tra('Content ID'),
				'description' => tra('Numeric value representing the content ID'),
				'default' => '',
			)
		)
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
