<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_language_list() {
	return array(
		'language_inclusion_threshold' => array(
			'name' => tra('Language inclusion threshold'),
			'description' => tra('When the number of languages is restricted on the site, and is below this number, all languages will be added to the preferred language list, even if unspecified by the user. However, priority will be given to the specified languages.'),
			'help' => 'Internationalization',
			'type' => 'text',
			'filter' => 'digits',
			'size' => 2,
			'dependencies' => array(
				'available_languages',
			),
		),
	);
}
