<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_storedsearch_list()
{
	return array(
		'storedsearch_enabled' => array(
			'name' => tr('Stored Searches'),
			'description' => tr('Allow users to store search queries.'),
			'type' => 'flag',
			'default' => 'n',
		),
	);
}

