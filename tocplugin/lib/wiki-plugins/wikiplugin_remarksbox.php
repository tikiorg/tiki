<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_remarksbox_info()
{
	return array(
		'name' => tra('Remarks Box'),
		'documentation' => 'PluginRemarksBox',
		'description' => tra('Display a comment, tip, note or warning box'),
		'prefs' => array( 'wikiplugin_remarksbox' ),
		'body' => tra('remarks text'),
		'iconname' => 'comment',
		'introduced' => 2,
		'tags' => array( 'basic' ),
		'params' => array(
			'type' => array(
				'required' => true,
				'name' => tra('Type'),
				'description' => tra('Select type of remarksbox, which determines what icon and style will be displayed'),
				'since' => '2.0',
				'default' => 'tip',
				'filter' => 'word',
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
				'since' => '2.0',
				'filter' => 'text',
				'default' => '',
			),
			'highlight' => array(
				'required' => false,
				'name' => tra('Highlight'),
				'description' => tra('Use the highlight class for formatting (not used by default).') ,
				'since' => '2.0',
				'filter' => 'alpha',
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
				'since' => '2.0',
				'filter' => 'url',
				'default' => '',
			),
			'close' => array(
				'required' => false,
				'name' => tra('Close'),
				'description' => tra('Show a close button (not shown by default).'),
				'since' => '4.0',
				'filter' => 'alpha',
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
				'description' => tr('Width (e.g. %0100%%1 or %0250px%1 - default "")', '<code>', '</code>'),
				'since' => '4.1',
				'filter' => 'text',
				'default' => ''
			),
			'store_cookie' => array(
				'name' => tr('Remember Dismiss'),
				'description' => tr('Set whether to remember if the alert is dismissed (not remembered by default).
					Requires %0id%1 and %0version%1 parameters to be set.', '<code>', '</code>'),
				'since' => '14.0',
				'required' => false,
				'filter' => 'text',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'id' => array(
				'name' => tr('ID'),
				'description' => tr('Sets an HTML id for the account.'),
				'since' => '14.0',
				'required' => false,
				'filter' => 'text'
			),
			'version' => array(
				'name' => tr('Version'),
				'description' => tr('Sets a version for the alert. If new version, the alert should show up again even
					if it was previously dismissed using the %0store_cookie%1 parameter', '<code>', '</code>'),
				'since' => '14.0',
				'required' => false,
				'filter' => 'text'
			),
		)
	);
}

function wikiplugin_remarksbox($data, $params)
{
	$smarty = TikiLib::lib('smarty');
	require_once('lib/smarty_tiki/block.remarksbox.php');
	
	// there probably is a better way @todo this
	// but for now i'm escaping the html in ~np~s as the parser is adding odd <p> tags
	$repeat = false;
	$ret = '~np~'.smarty_block_remarksbox($params, '~/np~'.tra($data).'~np~', $smarty, $repeat).'~/np~';
	return $ret;
}
