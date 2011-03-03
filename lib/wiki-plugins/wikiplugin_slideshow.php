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
	return tra("SLIDESHOW").':<br />~np~{SLIDESHOW(backgroundurl=>x, class=>c)}'.tra("Slideshow Notes").'{SLIDESHOW}~/np~';
}

function wikiplugin_slideshow_info() {
	return array(
		'name' => tra('SLIDESHOW'),
		'documentation' => 'Slideshow',
		'description' => tra('Configure a slideshow'),
		'prefs' => array( 'feature_slideshow' ),
		'body' => tra('Slideshow notes notes'),
		'icon' => 'pics/icons/images.gif',
		'params' => array(
			'backgroundurl' => array(
				'required' => false,
				'name' => tra('Background Url Location'),
				'description' => tra('URL of the background image to use in your slideshow, overrides backgroundcolor'),
				'filter' => 'url',
				'accepted' => 'Valid url',
				'default' => '',
				'since' => '7.0'
			),
			'backgroundcolor' => array(
				'required' => false,
				'name' => tra('Background Color'),
				'description' => tra('Background color to use in your slideshow, default '),
				'default' => '#0087BB',
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
			'headerfontcolor' => array(
				'required' => false,
				'name' => tra('Header font color'),
				'description' => tra('Apply a color to the headers of your slideshow'),
				'filter' => 'text',
				'accepted' => 'Any html color',
				'default' => '#56D0FF',
				'since' => '7.0',
			),
			'slidefontcolor' => array(
				'required' => false,
				'name' => tra('Header font color'),
				'description' => tra('Apply a color to the slides of your slideshow'),
				'filter' => 'text',
				'accepted' => 'Any html color',
				'default' => '#EEFAFF',
				'since' => '7.0',
			),
		),
	);
}

function wikiplugin_slideshow($data, $params) {
	global $dbTiki, $tiki_p_admin, $prefs, $user, $page, $tikilib, $smarty;
	extract ($params,EXTR_SKIP);
	
	$backgroundcolor = (isset($backgroundcolor) ? $backgroundcolor : '#0087BB');
	$backgroundurl = (isset($backgroundurl) ? $backgroundurl : '');
	$class = (isset($class) ? " $class"  : '');
	$headerfontcolor = (isset($headerfontcolor) ? $headerfontcolor : '#56D0FF');
	$slidefontcolor = (isset($slidefontcolor) ? $slidefontcolor : '#EEFAFF');
	
	$notes = explode("/////", ($data ? $data : ""));
	$notesHtml = '';
	foreach ( $notes as $note ) {
		$notesHtml .= '<span class="note">'.$note.'</span>';
	}
	
	$ret = "<div id='' class='tiki_slideshow $class'
		style='display: none;
			background-image: url(\"$backgroundurl\"); 
			background-color: $backgroundcolor;'
			
		headerFontColor='$headerfontcolor'
		slideFontColor='$slidefontcolor'
	>$notesHtml</div>";
	
	return '~np~' . $ret . '~/np~';
}
