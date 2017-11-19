<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_federated_list()
{
	return [
		'federated_enabled' => [
			'name' => tr('Federated search'),
			'description' => tr('Search through alternate site indices.'),
			'type' => 'flag',
			'default' => 'n',
			'hint' => tr('Elasticsearch is required'),
			'dependencies' => ['feature_search'],
		],
		'federated_elastic_url' => [
			'name' => tra('Elasticsearch tribe node URL'),
			'description' => tra('URL of the tribe client node accessing multiple clusters.'),
			'type' => 'text',
			'filter' => 'url',
			'default' => '',
			'size' => 40,
		],
	];
}
