<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_wysiwyg_info() {
	return array(
		'name' => 'WYSIWYG',
		'documentation' => 'PluginWYSIWYG',
		'description' => tra('Permits to have a WYSIWYG section for part of a page.'),
		'prefs' => array('wikiplugin_wysiwyg'),
		'params' => array(),
		'tags' => array( 'experimental' ),	
		'filter' => 'purifier',
		'body' => tra('Content'),
	);
} // wikiplugin_wysiwyg_info()


function wikiplugin_wysiwyg($data, $params) {
	global $tikilib;

	$html = $tikilib->parse_data( $data, array('is_html' => true));
	return $html;
	
} // wikiplugin_wysiwyg()
