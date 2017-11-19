<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_sitelogo_list()
{
	return [
		'sitelogo_src' => [
			'name' => tra('Logo source (image path)'),
			'type' => 'text',
			'description' => 'This can be a conventional path to the image file, or the syntax for an image in a Tiki gallery.',
			'default' => 'img/tiki/Tiki_WCG.png',
			'tags' => ['basic'],
		],
		'sitelogo_bgcolor' => [
			'name' => tra('Logo background color'),
			'description' => tr('A legacy option to add a background color to the div that contains the logo image.'),
			'hint' => tra('Examples:') . ' ' . '1) silver - 2) #fff',
			'type' => 'text',
			'size' => '15',
			'default' => 'transparent',
			'tags' => ['basic'],
		],
		'sitelogo_title' => [
			'name' => tra('Logo title (on mouseover)'),
			'description' => tr('This appears as tool tip text. The site logo is also a link to the site index (top page).'),
			'type' => 'text',
			'size' => '50',
			'default' => 'Tiki powered site',
			'tags' => ['basic'],
		],
		'sitelogo_alt' => [
			'name' => tra('HTML "alt" tag description'),
			'type' => 'text',
			'size' => '50',
			'description' => tr('Normally a description of the image, such as “Example.com logo”.'),
			'default' => 'Site Logo',
			'tags' => ['basic'],
			'hint' => tr('Used by text browsers, screen readers, etc.'),
		],
		'sitelogo_icon' => [
			'name' => tra('Site icon'),
			'description' => tr('This is to be used in narrow (top to bottom) page headers. In some layouts, the site logo is scaled down to fit.'),
			'hint' => tra('Recommended image height: 32 pixel'),
			'type' => 'text',
			'default' => 'img/tiki/tikilogo_icon.png',
			'tags' => ['basic'],
		],
	];
}
