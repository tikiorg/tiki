<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_default_list()
{
	return array(
		'default_mail_charset' => array(
			'name' => tra('Default character set for sending mail'),
			'description' => tra('Default character set for sending mail'),
			'type' => 'list',
			'options' => array(
				'utf-8' => tra('utf-8'),
				'iso-8859-1' => tra('iso-8859-1'),
			),
			'default' =>'utf-8',
		),
		'default_map' => array(
			'name' => tra('default mapfile'),
            'description' => tra(''),
			'type' => 'text',
			'size' => '50',
			'default' => '',
		),
		'default_wiki_diff_style' => array(
			'name' => tra('Default diff style'),
            'description' => tra(''),
			'type' => 'list',
			'options' => array(
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
			),
			'default' => 'sidediff',
		),
		'default_rows_textarea_wiki' => array(
			'name' => tra('Wiki'),
            'description' => tra(''),
			'type' => 'text',
			'size' => '3',
			'shorthint' => tra('rows'),
			'filter' => 'digits',
			'default' => '20',
		),
		'default_rows_textarea_comment' => array(
			'name' => tra('Default number of rows for comment box'),
            'description' => tra(''),
			'type' => 'text',
			'size' => '3',
			'shorthint' => tra('rows'),
			'filter' => 'digits',
			'default' => '6',
		),
		'default_rows_textarea_forum' => array(
			'name' => tra('Forum'),
            'description' => tra(''),
			'type' => 'text',
			'size' => '3',
			'shorthint' => tra('rows'),
			'filter' => 'digits',
			'default' => '20',
		),
		'default_rows_textarea_forumthread' => array(
			'name' => tra('Forum reply'),
            'description' => tra(''),
			'type' => 'text',
			'size' => '3',
			'shorthint' => tra('rows'),
			'filter' => 'digits',
			'default' => '10',
		),
	);
}
