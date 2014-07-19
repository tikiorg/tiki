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
			'name' => tra('Full Wysiwyg Editor is optional'),
			'type' => 'flag',
			'description' => tra('If wysiwyg is optional, the wiki text editor is also available. Otherwise only the Wysiwyg editor is used.').' '.tra('Switching between html and wiki formats can cause problems for some pages.'),
			'dependencies' => array(
				'feature_wysiwyg',
			),
			'default' => 'y',
		),
		'wysiwyg_default' => array(
			'name' => tra('Full Wysiwyg Editor is displayed by default'),
			'description' => tra('If both the Wysiwyg editor and the text editor is available, the Wysiwyg editor is used by default, e.g when creating new pages'),
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
			'name' => tra('Full Wysiwyg editor skin'),
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
			'description' => tra('Seamless inline editing. Uses CKEditor4. Inline editing lets the user edit pages without a context switch. The editor is embedded in the wiki page. When used on pages in wiki format, a conversion from HTML to Wiki format is required'),
			'help' => 'Wiki Inline Editing', 
			'type' => 'flag',
			'default' => 'n',
			'dependencies' => array(
				'feature_wysiwyg',
			),
			'tags' => array('experimental'),
			'warning' => tra('experimental'),
		),
		'wysiwyg_extra_plugins' => array(
			'name' => tra('Extra Plugins'),
			'hint' => tra('List of plugin names (separated by,)'),
			'description' => tra('As of Tiki 13 ckeditor uses the "standard" package which has some plugins disabled by default that were available in the "full" package.<br>See http://ckeditor.com/presets for a comparison of which plugins are enabled as standard.'),
			'type' => 'textarea',
			'size' => '1',
			'default' => 'bidi,colorbutton,find,font,justify,pagebreak,showblocks,smiley',
		),
	);
}
