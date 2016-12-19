<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_sitead_list()
{
	return array(
		'sitead_publish' => array(
			'name' => tra('Publish'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_sitead',
			),
			'default' => 'n',
		),	
	);
}
