<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_remarksbox_info() {
	return array(
		'name' => tra('Remarks Box'),
		'documentation' => 'PluginRemarksBox',
		'description' => tra('Displays a comment, tip, note or warning box'),
		'prefs' => array( 'wikiplugin_remarksbox' ),
		'body' => tra('remarks text'),
		'icon' => 'pics/icons/comment_add.png',
		'params' => array(
			'type' => array(
				'required' => true,
				'name' => tra('Type'),
				'description' => tra('Select type of remarksbox, which determines what icon and style will be displayed'),
				'default' => 'tip',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Comment'), 'value' => 'comment'), 
					array('text' => tra('Confirm'), 'value' => 'confirm'),
					array('text' => tra('Errors'), 'value' => 'errors'),
					array('text' => tra('Information'), 'value' => 'information'),
					array('text' => tra('Note'), 'value' => 'note'),
					array('text' => tra('Tip'), 'value' => 'tip'),
					array('text' => tra('Warning'), 'value' => 'warning')
				)
			),
			'title' => array(
				'required' => true,
				'name' => tra('Title'),
				'description' => tra('Label displayed above the remark.'),
				'default' => '',
			),
			'highlight' => array(
				'required' => false,
				'name' => tra('Highlight'),
				'description' => tra('Use the highlight class for formatting (not used by default).') ,
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'icon' => array(
				'required' => false,
				'name' => tra('Custom Icon'),
				'description' => tra('Enter a Tiki icon file name (with or without extension) or path to display a custom icon'),
				'default' => '',
			),
			'close' => array(
				'required' => false,
				'name' => tra('Close'),
				'description' => tra('Show a close button (not shown by default).'),
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Width (e.g. 100% or 250px - default "")'),
				'default' => ''
			)
		)
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
