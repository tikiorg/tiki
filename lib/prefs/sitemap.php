<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_sitemap_list()
{
	return array(
		'sitemap_enable' => array(
			'name' => tra('Support for generate the xml for sitemap protocol'),
			'type' => 'flag',
			'default' => 'n',
			'tags' => array('advanced'),
		),
	);
}
