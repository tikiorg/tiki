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
		'name' => tra('googledoc'),
		'documentation' => 'PluginGoogleDoc',
		'description' => tra("Displays a Google document"),
		'prefs' => array( 'wikiplugin_googledoc' ),
		'body' => tra('Leave this empty.'),
//		'validate' => 'all',
		'params' => array(
			'type' => array(
				'safe' => true,
				'required' => true,
				'name' => tra('type'),
				'description' => tra('Type of Google document'),
			),
			'key' => array(
					'safe' => true,
					'required' => true,
					'name' => tra('key'),
					'description' => tra('Google doc key - for example: pXsHENf1bGGY92X1iEeJJI'),
				),
			'name' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Name'),
				'description' => tra('Name of iframe'),
			),
			'size' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Size'),
				'description' => tra('Size of frame, use instead of width and height, they will fit the Google presentations sizes exactly. It can be small|medium|large.'),
			),
			'width' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Pixels or %'),
			),
			'height' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Pixels or %'),
			),
			'align' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Alignment'),
				'description' => 'top|middle|bottom|left|right',
			),
			'frameborder' => array(
				'safe' => true,
				'required' => false,
				'name' => 'frameborder',
				'description' => '1|0',
			),
			'marginheight' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Margin Height'),
				'description' => tra('Pixels'),
			),
			'marginwidth' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Margin Width'),
				'description' => tra('Pixels'),
			),
			'scrolling' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Scrolling'),
				'description' => 'yes|no|auto',
			),
			'editLink' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('editLink'),
				'description' => 'top|bottom|both',
			),
		),
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
