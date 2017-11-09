<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_wysiwyg_list()
{
	
	return array(
		'wysiwyg_optional' => array(
			'name' => tra('Full WYSIWYG editor is optional'),
			'type' => 'flag',
			'description' => tra('If WYSIWYG is optional, the wiki text editor is also available. Otherwise only the WYSIWYG editor is used.').' '.tra('Switching between HTML and wiki formats can cause problems for some pages.'),
			'dependencies' => array(
				'feature_wysiwyg',
			),
			'default' => 'y',
		),
		'wysiwyg_default' => array(
			'name' => tra('Full WYSIWYG editor is displayed by default'),
			'description' => tra('If both the WYSIWYG editor and the text editor are available, the WYSIWYG editor is used by default, for example, when creating new pages'),
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
		
		// TODO: wysiwyg_wiki_semi_parsed depends on wysiwyg_wiki_parsed. These 2 booleans should be replaced by a select with 3 options, but we would need to indicate that one of the options is experimental. Chealer
		'wysiwyg_wiki_parsed' => array(
			'name' => tra("Support Tiki's \"wiki syntax\""),
			'description' => tra('This allows a mixture of wiki syntax and HTML in the code of a text field in WYSIWYG mode.'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_wysiwyg',
			),
			'default' => 'y',
		),
		'wysiwyg_wiki_semi_parsed' => array(
			'name' => tra('Limited wiki parsing'),
			'description' => tra('If this preference is enabled, the set of features from Tiki\'s markup language allowed in parsed text fields is limited. Only some wiki syntax is parsed, such as plugins (not inline character styles, etc). If this preference is disabled, all markup features allowed in "wiki mode" are available.'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_wysiwyg',
			),
			'default' => 'n',
			'warning' => tra('Neglected. This feature can have unpredicable results and may be removed in future versions.'),
			'tags' => array('experimental'),
		),
		
		'wysiwyg_htmltowiki' => array(
			'name' => tra('Use Wiki syntax in WYSIWYG'),
			'description' => tra('Allow keeping wiki syntax with the WYSIWYG editor. Sometimes referred to as a "visual wiki".'),
			'hint' => tra('Using wiki syntax in WYSIWYG mode will limit toolbar to wiki tools'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_wysiwyg',
			),
			'default' => 'y',
		),
		'wysiwyg_toolbar_skin' => array(
			'name' => tra('Full WYSIWYG editor skin'),
			'type' => 'list',
			'options' => array(
				'moono' => tra('Moono (Default)'),
				'kama' => tra('Kama'),
				'bootstrapck' => tra('Bootstrap CK'),
				'minimalist' => tra('Minimalist'),
				'office2013' => tra('Office 2013'),
			),
			'default' => 'moono',
		),
		'wysiwyg_fonts' => array(
			'name' => tra('Typefaces'),
			'description' => tra('List of font names separated by semi-colons (";")'),
			'type' => 'textarea',
			'size' => '3',
			'default' => 'sans serif;serif;monospace;Arial;Century Gothic;Comic Sans MS;Courier New;Tahoma;Times New Roman;Verdana',
		),
		'wysiwyg_inline_editing' => array(
			'name' => tra('Inline WYSIWYG editor'),
			'description' => tra('Seamless inline editing. Uses CKEditor 4. Inline editing enables editing pages without a context switch. The editor is embedded in the wiki page. When used on pages in wiki format, a conversion from HTML to wiki format is required'),
			'help' => 'Wiki Inline Editing', 
			'type' => 'flag',
			'default' => 'n',
			'dependencies' => array(
				'feature_wysiwyg',
			),
			'tags' => array('experimental'),
		),
		'wysiwyg_extra_plugins' => array(
			'name' => tra('Extra plugins'),
			'hint' => tra('List of plugin names (separated by,)'),
			'description' => tra('In Tiki, CKEditor uses the "standard" package in which some plugins are disabled by default that are available in the "full" package.<br>See http://ckeditor.com/presets for a comparison of which plugins are enabled as standard.'),
			'type' => 'textarea',
			'size' => '1',
			'default' => 'bidi,colorbutton,divarea,find,font,justify,pagebreak,showblocks,smiley',
		),
	);
}
