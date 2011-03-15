<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_unified_list()
{
	return array(
		'unified_engine' => array(
			'name' => tra('Unified Search Engine'),
			'description' => tra('Search engine used to index the content of your Tiki. Some engines are more suitable for larger sites, but require additional software on the server.'),
			'type' => 'list',
			'options' => array(
				'lucene' => tra('Lucene (PHP Implementation)'),
			),
		),
		'unified_lucene_location' => array(
			'name' => tra('Lucene Index Location'),
			'description' => tra('Path to the location of the Lucene search index. The index must be on a local filesystem with enough space to contain the volume of the database.'),
			'type' => 'text',
			'size' => 35,
		),
		'unified_incremental_update' => array(
			'name' => tra('Incremental Index Update'),
			'description' => tra('Update the index incrementally as the site content is modified. This may lead to lower performance and accuracy than processing the index on a periodic basis.'),
			'type' => 'flag',
		),
	);
}

