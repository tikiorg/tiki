<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_list_info()
{
	return array(
		'name' => tra('List'),
		'documentation' => 'PluginList',
		'description' => tra('Create lists of Tiki objects based on custom search criteria and formatting'),
		'prefs' => array('wikiplugin_list', 'feature_search'),
		'body' => tra('List configuration information'),
		'filter' => 'wikicontent',
		'profile_reference' => 'search_plugin_content',
		'icon' => 'img/icons/text_list_bullets.png',
		'tags' => array( 'basic' ),
		'params' => array(
			'allow_sort_replace' => array(
				'required' => false,
				'filter' => 'alpha',
				'name' => tra('Allow Sort Replace'),
				'description' => tra('Replace the default sort'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => ''), 
				),
			),
		),
	);
}

function wikiplugin_list($data, $params)
{
	$unifiedsearchlib = TikiLib::lib('unifiedsearch');

	$query = new Search_Query;
	$query->filterIdentifier('y', 'searchable');
	$unifiedsearchlib->initQuery($query);

	$matches = WikiParser_PluginMatcher::match($data);

	$builder = new Search_Query_WikiBuilder($query);
	$builder->enableAggregate();
	$builder->apply($matches);
	$paginationArguments = $builder->getPaginationArguments();

	if (!empty($_REQUEST[$paginationArguments['sort_arg']]) && $params[allow_sort_replace] != "y") {
		$query->setOrder($_REQUEST[$paginationArguments['sort_arg']]);
	}

	if (! $index = $unifiedsearchlib->getIndex()) {
		return '';
	}

	$result = $query->search($index);


	$resultBuilder = new Search_ResultSet_WikiBuilder($result);
	$resultBuilder->setPaginationArguments($paginationArguments);
	$resultBuilder->apply($matches);

	$builder = new Search_Formatter_Builder;
	$builder->setPaginationArguments($paginationArguments);
	$builder->apply($matches);

	$formatter = $builder->getFormatter();
	$out = $formatter->format($result);

	return $out;
}

