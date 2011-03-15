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
		'description' => tra('Configure a slideshow.  Extends the existing wiki page slideshow with notes & styles.'),
		'prefs' => array( 'feature_slideshow' ),
		'body' => tra('Slideshow notes - Separate with "/////"'),
		'icon' => 'pics/icons/images.png',
		'params' => array(
			'backgroundurl' => array(
				'required' => false,
				'name' => tra('Background Url Location'),
				'description' => tra('URL of the background image to use in your slideshow, overrides backgroundcolor'),
				'filter' => 'url',
				'accepted' => tra('Valid url'),
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
				'accepted' => tra('Any valid CSS class'),
				'default' => '',
				'since' => '7.0',
			),
			'headerfontcolor' => array(
				'required' => false,
				'name' => tra('Header font color'),
				'description' => tra('Apply a color to the headers of your slideshow'),
				'filter' => 'text',
				'accepted' => tra('Any html color'),
				'default' => '#56D0FF',
				'since' => '7.0',
			),
			'slidefontcolor' => array(
				'required' => false,
				'name' => tra('Slide font color'),
				'description' => tra('Apply a color to the slides of your slideshow'),
				'filter' => 'text',
				'accepted' => tra('Any html color'),
				'default' => '#EEFAFF',
				'since' => '7.0',
			),
			'listitemhighlightcolor' => array(
				'required' => false,
				'name' => tra('Line Item highlight color'),
				'description' => tra('Apply a color to the line item when mouse over'),
				'filter' => 'text',
				'accepted' => tra('Any html color'),
				'default' => '',
				'since' => '7.0',
			),
			'slideseconds' => array(
				'required' => false,
				'name' => tra('Slide Seconds'),
				'description' => tra('How many seconds a slide will be open while playing'),
				'filter' => 'digits',
				'accepted' => tra('Second count'),
				'default' => '15',
				'since' => '7.0'
			),
			'textside' => array(
				'required' => false,
				'name' => tra('Text Side'),
				'description' => tra('The side on which you would like to display text if there are images that are resized or present'),
				'filter' => 'text',
				'default' => tra('Left'),
				'since' => '7.0',
				'options' => array(
					array('text' => tra('Left'), 'value' => 'left'), 
					array('text' => tra('Right'), 'value' => 'right'), 
				),
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
	$slideduration = (isset($slideseconds) ? $slideseconds : 15) * 1000;
	$listitemhighlightcolor = (isset($listitemhighlightcolor) ? $listitemhighlightcolor : '');
	$textside = (isset($textside) ? $textside : 'left');
	
	$notes = explode("/////", ($data ? $data : ""));
	$notesHtml = '';
	foreach ( $notes as $note ) {
		$notesHtml .= '<span class="s5-note">'.$note.'</span>';
	}
	global $headerlib;
	
	$headerlib->add_js("
		window.s5Settings = {
			slideClass: '$class',
			backgroundImage: '$backgroundurl',
			backgroundColor: '$backgroundcolor',
			headerFontColor: '$headerfontcolor',
			slideFontColor: '$slidefontcolor',
			slideDuration: $slideduration,
			listItemHighlightColor: '$listitemhighlightcolor',
			textSide: '$textside'
		};
	");
	
	return "~np~<div id='' class='tiki_slideshow'>$notesHtml</div>~/np~";
}
