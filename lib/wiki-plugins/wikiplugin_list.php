<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_list_info()
{
	return array(
		'name' => tra('List'),
		'documentation' => 'PluginList',
		'description' => tra('Search for, list, and filter all types of items and display custom formatted results'),
		'prefs' => array('wikiplugin_list', 'feature_search'),
		'body' => tra('List configuration information'),
		'filter' => 'wikicontent',
		'profile_reference' => 'search_plugin_content',
		'iconname' => 'list',
		'introduced' => 7,
		'tags' => array( 'basic' ),
		'params' => array(
			'searchable_only' => array(
				'required' => false,
				'name' => tra('Searchable Only Results'),
				'description' => tra('Only include results marked as searchable in the index.'),
				'filter' => 'digits',
				'default' => '1',
			),
		),
	);
}

function wikiplugin_list($data, $params)
{
	global $prefs;

	static $i;
	$i++;

	$unifiedsearchlib = TikiLib::lib('unifiedsearch');

	$query = new Search_Query;
	if (!isset($params['searchable_only']) || $params['searchable_only'] == 1) {
		$query->filterIdentifier('y', 'searchable');
	}
	$unifiedsearchlib->initQuery($query);

	$matches = WikiParser_PluginMatcher::match($data);

	$builder = new Search_Query_WikiBuilder($query);
	$builder->enableAggregate();
	$builder->apply($matches);
	$tsret = $builder->applyTablesorter($matches);
	if (!empty($tsret['max']) || !empty($_GET['numrows'])) {
		$max = !empty($_GET['numrows']) ? $_GET['numrows'] : $tsret['max'];
		$builder->wpquery_pagination_max($query, $max);
	}
	$paginationArguments = $builder->getPaginationArguments();

	if (!empty($_REQUEST[$paginationArguments['sort_arg']])) {
		$query->setOrder($_REQUEST[$paginationArguments['sort_arg']]);
	}

	if (! $index = $unifiedsearchlib->getIndex()) {
		return '';
	}

	$result = $query->search($index);
	$result->setId('wplist-' . $i);


	$resultBuilder = new Search_ResultSet_WikiBuilder($result);
	$resultBuilder->setPaginationArguments($paginationArguments);
	$resultBuilder->apply($matches);

	$builder = new Search_Formatter_Builder;
	$builder->setPaginationArguments($paginationArguments);
	$builder->setId('wplist-' . $i);
	$builder->setCount($result->count());
	$builder->setTsOn($tsret['tsOn']);
	$builder->apply($matches);

	$result->setTsSettings($builder->getTsSettings());

	$formatter = $builder->getFormatter();

	$result->setTsOn($tsret['tsOn']);
	$out = $formatter->format($result);

	return $out;
}
