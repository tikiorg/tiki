<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_url_list()
{
	return array(
		'url_after_validation' => array(
			'name' => tra('URL the user is redirected to after account validation'),
			'hint' => tra('Default').': tiki-information.php?msg='.tra('Account validated successfully.'),
			'type' => 'text',
			'dependencies' => array(
				'allowRegister',
			),
			'default' => '',
		),
		'url_anonymous_page_not_found' => array(
			'name' => tra('The URL that the anonymous user is redirected to when a page is not found'),
			'type' => 'text',
			'default' => '',
		),
		'url_only_ascii' => array(
			'name' => tra('Use Only ASCII in SEFURLs'),
			'description' => tra('Do not use accented characters in short (search engine friendly) URLs'),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'n',
		),
	);
}
