<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_ajax_list()
{

	return [
		'ajax_autosave' => [
			'name' => tra('Ajax auto-save'),
			'description' => tra('Saves your edits as you write, enabling work to be recovered after an "interruption". Also enables a "live" preview and is required for WYSIWYG plugin processing.'),
			'help' => 'Lost+Edit+Protection',
			'type' => 'flag',
			'dependencies' => [
				'feature_ajax',
				'feature_warn_on_edit',
			],
			'default' => 'y',
		],

		'ajax_inline_edit' => [
			'name' => tr('Inline editing'),
			'description' => tr('Enable inline editing of certain values. Currently limited to tracker item fields.'),
			'type' => 'flag',
			'default' => 'n',
		],
		'ajax_inline_edit_trackerlist' => [
			'name' => tr('Tracker list inline editing'),
			'description' => tr('Enable inline editing on all fields listed in the tracker list page.'),
			'type' => 'flag',
			'default' => 'y',
			'dependencies' => ['ajax_inline_edit'],
		],
	];
}
