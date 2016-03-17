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

	$tsret = applyTablesorter($matches, $query);

	$builder = new Search_Query_WikiBuilder($query);
	$builder->enableAggregate();
	if ($tsret['max']) {
		$builder->wpquery_pagination_max($query, $tsret['max']);
	}
	$builder->apply($matches);
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

	$formatter = $builder->getFormatter();

	$result->setTsOn($tsret['tsOn']);
	$out = $formatter->format($result);

	return $out;
}

/**
 * Apply tablesorter is enabled
 *
 * @param WikiParser_PluginMatcher $matches
 * @param Search_Query $query
 * @return array
 */
function applyTablesorter(WikiParser_PluginMatcher $matches, Search_Query $query)
{
	$ret = ['max' => false, 'tsOn' => false];
	$parser = new WikiParser_PluginArgumentParser;
	foreach ($matches as $match) {
		$name = $match->getName();
		if ($name == 'tablesorter') {
			$tsargs = $parser->parse($match->getArguments());
			$ajax = !empty($tsargs['server']) && $tsargs['server'] === 'y';
			$ret['tsOn'] = Table_Check::isEnabled($ajax);
			if (!$ret['tsOn']) {
				TikiLib::lib('errorreport')->report(tra('List plugin: Feature "jQuery Sortable Tables" (tablesorter) is not enabled'));
				return $ret;
			}
			if (isset($tsargs['tsortcolumns'])) {
				$tsc = Table_Check::parseParam($tsargs['tsortcolumns']);
			}
			if (isset($tsargs['tspaginate'])) {
				$tsp = Table_Check::parseParam($tsargs['tspaginate']);
				if (isset($tsp[0]['max']) && $ajax) {
					$ret['max'] = (int) $tsp[0]['max'];
				}
			}
		}
	}

		foreach ($matches as $match) {
		$name = $match->getName();
		if ($name == 'column') {
			$cols[] = $match;
			$args[] = $parser->parse($match->getArguments());
		}
	}

	if (Table_Check::isSort()) {
		foreach ($_GET['sort'] as $key => $dir) {
			$n = '';
			switch ($tsc[$key]['type']) {
				case 'digit':
				case 'currency':
				case 'percent':
				case 'time':
				case strpos($tsc[$key]['type'], 'date') !== false:
					$n = 'n';
					break;
			}
			$query->setOrder($args[$key]['field'] . '_' . $n . Table_Check::$dir[$dir]);
		}
	}

	if (Table_Check::isFilter()) {
		foreach ($_GET['filter'] as $key => $filter) {
			$query->filterContent($filter, $args[$key]['field']);
		}
	}

	return $ret;
}
