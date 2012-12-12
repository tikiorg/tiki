<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_multidomain_list()
{
	return array(
		'multidomain_active' => array(
			'name' => tra('Multi-domain'),
			'perspective' => false,
			'description' => tra('Allows to map domain names to perspectives and simulate multiple domains hosted on the same instance.'),
			'help' => 'Multi-Domain',
			'type' => 'flag',
			'dependencies' => array(
				'feature_perspective',
			),
			'default' => 'n',
		),
		'multidomain_config' => array(
			'name' => tra('Multi-domain Configuration'),
			'perspective' => false,
			'description' => tra('Comma-separated values mapping the domain name to the perspective ID.'),
			'type' => 'textarea',
			'size' => 10,
			'hint' => tra('One domain per line. Comma separated with perspective ID. Ex.: tiki.org,1'),
			'default' => '',
		),
		'multidomain_switchdomain' => array(
			'name' => tra('Switch domain when switching perspective'),
			'tags' => array('advanced'),	
			'description' => tra('Remember that different domains have different login sessions and even in the case of subdomains you need to have an understanding of session cookies to make it work'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_perspective', 'multidomain_active'
			),
			'default' => 'n',
		),
	);
}
