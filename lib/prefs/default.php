<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_default_list()
{

	$calendarlib = TikiLib::lib('calendar');
	$cals = $calendarlib->list_calendars();
	if (array_key_exists('data', $cals)) {
		$cals = array_column($cals['data'], 'name', 'calendarId');
	} else {
		$cals = [];
	}


	return [
		'default_mail_charset' => [
			'name' => tra('Default character set for sending mail'),
			'description' => tra('Specify the character encoding used by Tiki when sending mail notifications.'),
			'type' => 'list',
			'options' => [
				'utf-8' => tra('utf-8'),
				'iso-8859-1' => tra('iso-8859-1'),
			],
			'default' => 'utf-8',
		],
		'default_map' => [
			'name' => tra('default mapfile'),
			'type' => 'text',
			'size' => '50',
			'default' => '',
		],
		'default_wiki_diff_style' => [
			'name' => tra('Default diff style'),
			'type' => 'list',
			'options' => [
				'old' => tra('Only with last version'),
				'htmldiff' => tra('HTML diff'),
				'sidediff' => tra('Side-by-side diff'),
				'sidediff-char' => tra('Side-by-side diff by characters'),
				'inlinediff' => tra('Inline diff'),
				'inlinediff-char' => tra('Inline diff by characters'),
				'sidediff-full' => tra('Full side-by-side diff'),
				'sidediff-full-char' => tra('Full side-by-side diff by characters'),
				'inlinediff-full' => tra('Full inline diff'),
				'inlinediff-full-char' => tra('Full inline diff by characters'),
				'unidiff' => tra('Unified diff'),
				'sideview' => tra('Side-by-side view'),
			],
			'default' => 'sidediff',
		],
		'default_rows_textarea_wiki' => [
			'name' => tra('Wiki'),
			'type' => 'text',
			'size' => '3',
			'units' => tra('rows'),
			'filter' => 'digits',
			'default' => '20',
		],
		'default_rows_textarea_comment' => [
			'name' => tra('Comment box'),
			'type' => 'text',
			'description' => tr('Size (height) of the comment text area.'),
			'size' => '3',
			'units' => tra('rows'),
			'filter' => 'digits',
			'default' => '6',
		],
		'default_rows_textarea_forum' => [
			'name' => tra('Forum'),
			'type' => 'text',
			'size' => '3',
			'units' => tra('rows'),
			'filter' => 'digits',
			'default' => '20',
		],
		'default_rows_textarea_forumthread' => [
			'name' => tra('Forum reply'),
			'type' => 'text',
			'size' => '3',
			'units' => tra('rows'),
			'filter' => 'digits',
			'default' => '10',
		],
		'default_calendars' => [
			'name' => tra('Select default calendars to display'),
			'type' => 'multicheckbox',
			'options' => $cals,
			'default' => [],
		],
	];
}
