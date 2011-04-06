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
			'default' => 'lucene',
		),
		'unified_lucene_location' => array(
			'name' => tra('Lucene Index Location'),
			'description' => tra('Path to the location of the Lucene search index. The index must be on a local filesystem with enough space to contain the volume of the database.'),
			'type' => 'text',
			'size' => 35,
			'default' => 'temp/unified-index',
		),
		'unified_incremental_update' => array(
			'name' => tra('Incremental Index Update'),
			'description' => tra('Update the index incrementally as the site content is modified. This may lead to lower performance and accuracy than processing the index on a periodic basis.'),
			'type' => 'flag',
			'default' => 'y',
		),
		'unified_field_weight' => array(
			'name' => tra('Field Weights'),
			'description' => tra('Allows to set the field weights that apply when ranking the pages for search listing. The weight only applies when the field is in the query. To nullify the value of a field, use an insignificant amount, not 0, which may lead to unexpected behaviors, such as stripping results.'),
			'hint' => tra('One field per line, \'\'field_name\'\'__:__\'\'5.3\'\''),
			'type' => 'textarea',
			'size' => 5,
			'filter' => 'text',
			'default' => "title:2.5\nallowed_groups:0.0001\ncategories:0.0001\ndeep_categories:0.0001",
		),
		'unified_default_content' => array(
			'name' => tra('Default content fields'),
			'description' => tra('All of the content is aggregated in the contents field. For custom weighting to apply, the fields must be included in the query. This option allows to include other fields in the default content search.'),
			'type' => 'text',
			'separator' => ',',
			'filter' => 'word',
			'default' => array('contents', 'title'),
		),
	);
}

