<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/* Tiki-Wiki plugin example 
 *
 * This is an example plugin to let you know how to create
 * a plugin. Plugins are called using the syntax
 * {NAME(params)}content{NAME}
 * Name must be in uppercase!
 * params is in the form: name=>value,name2=>value2 (don't use quotes!)
 * If the plugin doesn't use params use {NAME()}content{NAME}
 *
 * The function will receive the plugin content in $data and the params
 * in the asociative array $params (using extract to pull the arguments
 * as in the example is a good practice)
 * The function returns some text that will replace the content in the
 * wiki page.
 */
function wikiplugin_slideshow_help() {
	return tra("SLIDESHOW").':<br />~np~{SLIDESHOW(backgroundurl=>x, simple=>n, height=>h)}'.tra("Slideshow Notes").'{SLIDESHOW}~/np~';
}

function wikiplugin_slideshow_info() {
	return array(
		'name' => tra('SLIDESHOW'),
		'documentation' => 'Slideshow',
		'description' => tra('Configure a slideshow'),
		'prefs' => array( 'feature_slideshow' ),
		'body' => tra('Slideshow notes notes'),
		'icon' => 'pics/icons2/i_controlpanel_off.gif',
		'params' => array(
			'backgroundurl' => array(
				'required' => false,
				'name' => tra('Background Url Location'),
				'description' => tra('Internal URL of the background image to use in your slideshow'),
				'filter' => 'url',
				'accepted' => 'Valid url',
				'default' => '',
				'since' => '7.0'
			),
			'class' => array(
				'required' => false,
				'name' => tra('CSS Class'),
				'description' => tra('Apply custom CSS class to the containing div.'),
				'filter' => 'text',
				'accepted' => 'Any valid CSS class',
				'default' => '',
				'since' => '7.0',
			),
		),
	);
}

function wikiplugin_slideshow($data, $params) {
	global $dbTiki, $tiki_p_admin, $prefs, $user, $page, $tikilib, $smarty;
	extract ($params,EXTR_SKIP);
	$class = (isset($class)) ? " $class"  : '';

	$ret = '<div id="" class="tiki_slideshow' . $class . '" style="overflow:hidden;' . $style . '"></div>';
	
	return '~np~' . $ret . '~/np~';
}
