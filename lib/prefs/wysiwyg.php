<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_wysiwyg_list()
{
	
	return array(
		'wysiwyg_optional' => array(
			'name' => tra('Wysiwyg Editor is optional'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_wysiwyg',
			),
			'default' => 'y',
		),
		'wysiwyg_default' => array(
			'name' => tra('Wysiwyg Editor is displayed by default'),
			'type' => 'flag',
			'dependencies' => array(
				'wysiwyg_optional',
			),
			'default' => 'y',
		),
		'wysiwyg_memo' => array(
			'name' => tra('Reopen with the same editor'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_wysiwyg',
			),
			'default' => 'y',
		),
		'wysiwyg_wiki_parsed' => array(
			'name' => tra('Content is parsed like wiki page'),
			'description' => tra('This allows a mixture of wiki and HTML. All wiki syntax is parsed.'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_wysiwyg',
			),
			'default' => 'y',
		),
		'wysiwyg_wiki_semi_parsed' => array(
			'name' => tra('Content is partially wiki parsed'),
			'description' => tra('This also allows a mixture of wiki and HTML. Only some wiki syntax is parsed, such as plugins (not inline character styles etc).'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_wysiwyg',
			),
			'default' => 'n',
			'warning' => tra('Neglected. This feature can have unpredicable results and may be removed in future versions.'),
			'tags' => array('experimental'),
		),
		'wysiwyg_toolbar_skin' => array(
			'name' => tra('Wysiwyg editor skin'),
			'type' => 'list',
			'options' => array(
				'moono' => tra('Moono (Default)'),
				'kama' => tra('Kama'),
			),
			'default' => 'moono',
		),
		'wysiwyg_htmltowiki' => array(
			'name' => tra('Use Wiki syntax in WYSIWYG'),
			'description' => tra('Allow to keep the wiki syntax with the WYSIWYG editor. Sometimes also known as "Visual Wiki".'),
			'hint' => tra('Using wiki syntax in wysiwyg mode will limit toolbar to Wiki tools'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_wysiwyg',
			),
			'default' => 'y',
		),
		'wysiwyg_fonts' => array(
			'name' => tra('Font names'),
			'description' => tra('List of font names separated by;'),
			'type' => 'textarea',
			'size' => '3',
			'default' => 'sans serif;serif;monospace;Arial;Century Gothic;Comic Sans MS;Courier New;Tahoma;Times New Roman;Verdana',
		),
		'wysiwyg_inline_editing' => array(
			'name' => tra('Inline Wysiwyg editor'),
			'description' => tra('Seemless inline editing. Uses CKEditor4.'),
			'help' => 'Inline Wysiwyg',
			'type' => 'flag',
			'default' => 'n',
			'dependencies' => array(
				'feature_wysiwyg',
			),
			'tags' => array('experimental'),
			'warning' => tra('experimental'),
		),
	);
}
