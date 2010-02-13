<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/* Displays a remarks box
 * Use:
 * {REMARKSBOX()}Some remarks, will be wiki parsed according to prefs{REMARKSBOX}
 *  (type=>tip|comment|note|warning)	Type (default=tip)
 *  (title=>title text)  				Title text
 *  (highlight=>n|y)  					Add highlight class (default=n)
 *  (icon=>icon_id)  					Optional icon (override defaults, use 'none' for no icon)
 *  (close=>y)  						closable
 *  (width=>'')  						remarksbox width
 * Examples:
 * 
	{REMARKSBOX(title=>Comment,type=>comment)}What's the difference between a comment and a note?{REMARKSBOX}
	{REMARKSBOX(title=>Tip,highlight=y)}Never run for a bus. There'll be another one along soon.{REMARKSBOX}
	{REMARKSBOX(title=>Tip!,highlight=y,icon=>world)}This one is highlighted for the world!{REMARKSBOX}
	{REMARKSBOX(title=>Note,type=>note)}This here is a note{REMARKSBOX}
	{REMARKSBOX(title=>Bicuits!,type=>warning)}Pay attention to this! __Ok!?__{REMARKSBOX}
 */

function wikiplugin_remarksbox_help() {
	return tra('Displays a comment, tip, note or warning box').
		':<br />~np~{REMARKSBOX(type=>tip|comment|note|warning,title=>title text,highlight=n|y,icon=optional icon_id or none, close=y, width=auto )}'.
		tra('remarks text').'{REMARKSBOX}~/np~';
}

function wikiplugin_remarksbox_info() {
	return array(
		'name' => tra('Remarks Box'),
		'documentation' => 'PluginRemarksBox',		
		'description' => tra('Displays a comment, tip, note or warning box'),
		'prefs' => array( 'wikiplugin_remarksbox' ),
		'body' => tra('remarks text'),
		'params' => array(
			'type' => array(
				'required' => true,
				'name' => tra('Type'),
				'description' => 'tip|comment|note|warning',
			),
			'title' => array(
				'required' => true,
				'name' => tra('title'),
				'description' => tra('Label displayed above the remark.'),
			),
			'highlight' => array(
				'required' => false,
				'name' => tra('Highlight'),
				'description' => 'y|n',
			),
			'icon' => array(
				'required' => false,
				'name' => tra('Icon'),
				'description' => tra('Icon ID.'),
			),
			'close' => array(
				'required' => false,
				'name' => tra('Close'),
				'description' => tra('y|n Show close button (default y)'),
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Width (e.g. 100% or 250px - default "")'),
			),
		),
	);
}

function wikiplugin_remarksbox($data, $params) {
	global $smarty;
	require_once('lib/smarty_tiki/block.remarksbox.php');
	
	// there probably is a better way @todo this
	// but for now i'm escaping the html in ~np~s as the parser is adding odd <p> tags
	$ret = '~np~'.smarty_block_remarksbox($params, '~/np~'.tra($data).'~np~', $smarty).'~/np~';
	return $ret;
}
