<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: unified.php 37548 2011-09-22 16:39:17Z nkoth $

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
		'unified_lucene_highlight' => array(
			'name' => tra('Highlight results snippets'),
			'description' => tra('Highlight the result snippet based on the search query. Enabling this option will impact performance, but improve user experience.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'unified_lucene_max_result' => array(
			'name' => tra('Lucene Maximum Results'),
			'description' => tra('Maximum amount of results to expose. Results further than these will need a more refined query to be reached.'),
			'type' => 'text',
			'filter' => 'int',
			'default' => 200,
			'size' => 6,
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
			'hint' => tra('One field per line, field_name__:__5.3'),
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
		'unified_tokenize_version_numbers' => array(
			'name' => tra('Tokenize version numbers'),
			'description' => tra('Tokenize version number strings so that major versions are found when sub-versions are mentionned. For example, searching for 2.7 would return documents containing 2.7.4, but not 1.2.7.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'unified_user_cache' => array(
			'name' => tra('Cache per user and query'),
			'type' => 'text',
			'size' => '4',
			'filter' => 'digits',
			'description' => tra('Time in minutes a user has a same query cached '),
			'hint' => tra('In minutes'),
			'default' => '0',
			'tags' => array('advanced'),
		),
		'unified_forum_deepindexing' => array(
                        'name' => tra('Index forum replies together with root post'),
                        'description' => tra('If enabled, forum replies will be indexed together with the root post as a single document instead of being separately indexed'),
                        'type' => 'flag',
                        'default' => 'y',
                ),
                'unified_cached_formatters' => array(
                        'name' => tra('Search formatters to cache'),
                        'description' => tra('Comma separated list of search formatters to cache the output of'),
                        'type' => 'text',
                        'separator' => ',',
                        'default' => array('trackerrender','categorylist'),
                ),
	);
}

