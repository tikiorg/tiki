<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
 * Google Docs plugin. Creates an iframe and loads the Google Doc within the frame.
 *
 * MatWho 13/09/08
 */

function wikiplugin_googledoc_help() {
	return tra("googledoc").":~np~{GOOGLEDOC(type=sheet|doc|pres|spreadsheet|document|presentation, key=XXXXX name=xxx, size=small|medium|large, width=100, height=100, align=top|middle|bottom|left|right, frameborder=1|0, marginheight=0, marginwidth=0, scrolling=yes|no|auto, editLink=top|bottom|both)}{GOOGLEDOC}~/np~";
}

function wikiplugin_googledoc_info() {
	return array(
		'name' => tra('Google Doc'),
		'documentation' => 'PluginGoogleDoc',
		'description' => tra('Display a Google document'),
		'prefs' => array( 'wikiplugin_googledoc' ),
		'body' => tra('Leave this empty.'),
//		'validate' => 'all',
		'icon' => 'pics/icons/google.png',
		'params' => array(
			'type' => array(
				'safe' => true,
				'required' => true,
				'name' => tra('Type'),
				'description' => tra('Type of Google document'),
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Document'), 'value' => 'document'), 
					array('text' => tra('Presentation'), 'value' => 'presentation'), 
					array('text' => tra('Spreadsheet'), 'value' => 'speadsheet')
				)
			),
			'key' => array(
					'safe' => true,
					'required' => true,
					'name' => tra('key'),
					'description' => tra('Google doc key - for example: pXsHENf1bGGY92X1iEeJJI'),
					'default' => ''
				),
			'name' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Name'),
				'description' => tra('Name of iframe. Default is "Frame" + the key')
			),
			'size' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Size'),
				'description' => tra('Size of frame. Use instead of width and height. The sizes will fit the Google presentations sizes exactly.'),
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Small'), 'value' => 'small'), 
					array('text' => tra('Medium'), 'value' => 'medium'), 
					array('text' => tra('Large'), 'value' => 'large')
				)
			),
			'width' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Width in pixels or %'),
				'filter' => 'digits',
				'default' => 800
			),
			'height' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Height in pixels or %'),
				'filter' => 'digits',
				'default' => 400
			),
			'align' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Alignment'),
				'description' => 'top|middle|bottom|left|right',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Top'), 'value' => 'top'), 
					array('text' => tra('Middle'), 'value' => 'middle'), 
					array('text' => tra('Bottom'), 'value' => 'bottom'), 
					array('text' => tra('Left'), 'value' => 'left'), 
					array('text' => tra('Right'), 'value' => 'right') 
				)
			),
			'frameborder' => array(
				'safe' => true,
				'required' => false,
				'name' => 'Frame Border',
				'description' => tra('Choose whether to show a border around the iframe'),
				'default' => 0,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0)
				)
			),
			'marginheight' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Margin Height'),
				'description' => tra('Margin height in pixels'),
				'default' => ''
			),
			'marginwidth' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Margin Width'),
				'description' => tra('Margin width in pixels'),
				'default' => ''
			),
			'scrolling' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Scrolling'),
				'description' => tra('Choose whether to add a scroll bar'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'yes'), 
					array('text' => tra('No'), 'value' => 'no'),
					array('text' => tra('Auto'), 'value' => 'auto')
				)
			),
			'editLink' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Edit Link'),
				'description' => tra('Choose whether to show an edit link and set its location'),
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Top'), 'value' => 'top'), 
					array('text' => tra('Bottom'), 'value' => 'bottom'),
					array('text' => tra('Both'), 'value' => 'both')
				)
			)
		)
	);
}

function wikiplugin_googledoc($data, $params) {

	extract ($params, EXTR_SKIP);
	
	if (empty($type)) {
		return tra('Required parameter "type" missing');
	}
	if (empty($key)) {
		return tra('Required parameter "key" missing');
	}

    if ($type =="sheet" or $type=="spreadsheet") {
		$srcUrl="\"http://spreadsheets.google.com/pub?key=$key &output=html&widget=true\"";
		$editHtml=" <P><A HREF=$srcUrl Target=\"$frameName\">Edit this Google Document</A></P>";
	}
	if ($type =="doc" or $type=="document") {
		$srcUrl="\"http://docs.google.com/View?docid=$key\"";
		$editHtml="";
	}
	if ($type =="pres" or $type=="presentation") {
		$srcUrl="\"http://docs.google.com/EmbedSlideshow?docid=$key\"";
		$editHtml="";
	}
	
	$ret = "";
	
	if (isset($name)) {
		$frameName=$name;
	} else {
		$frameName="Frame".$key;
	}
	if ($editLink== 'both' or $editLink== 'top') {
		$ret .= $editHtml;
	}

	$ret .= '<iframe ';
	$ret .= " name=\"$frameName\"";
	
	if($size == 'small') { $width= 410; $height= 342;}
	if($size == 'medium'){ $width= 555; $height= 451;}
	if($size == 'large') { $width= 700; $height= 559;}
	
	if (isset($width)) {
		$ret .= " width=\"$width\"";
	} else {
		$ret .=  " width=\"800\"";
	}
	if (isset($height)) {
		$ret .= " height=\"$height\"";
	} else {
		$ret .= " height=\"400\"";
	}

	if (isset($align)) {
		$ret .= " align=\"$align\"";
	}
	if (isset($frameborder)) {
		$ret .= " frameborder=\"$frameborder\"";
	} else {
		$ret .= " frameborder=0";
	}
	if (isset($marginheight)) {
		$ret .= " marginheight=\"$marginheight\"";
	}
	if (isset($marginwidth)) {
		$ret .= " marginwidth=\"$marginwidth\"";
	}
	if (isset($scrolling)) {
		$ret .= " scrolling=\"$scrolling\"";
	}
	if (isset($key)) {
		$ret .= " src=$srcUrl></iframe>";
	}
	if ($editLink== 'both' or $editLink== 'bottom') {
		$ret .= $editHtml;
	}

	$ret .= "";
	return $ret;
}
