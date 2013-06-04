<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Query_WikiBuilder
{
	private $query;
	private $paginationArguments;

	function __construct(Search_Query $query)
	{
		$this->query = $query;
		$this->paginationArguments = array(
			'offset_arg' => 'offset',
			'max' => 50,
		);
	}

	function apply(WikiParser_PluginMatcher $matches)
	{
		$argumentParser = new WikiParser_PluginArgumentParser;

		foreach ($matches as $match) {
			$name = $match->getName();
			$arguments = $argumentParser->parse($match->getArguments());

			foreach ($arguments as $key => $value) {
				$function = "wpquery_{$name}_{$key}";

				if (method_exists($this, $function)) {
					call_user_func(array($this, $function), $this->query, $value, $arguments);
				}
			}
		}

		$offsetArg = $this->paginationArguments['offset_arg'];
		$maxRecords = $this->paginationArguments['max'];
		if (isset($_REQUEST[$offsetArg])) {
			$this->query->setRange($_REQUEST[$offsetArg], $maxRecords);
		} else {
			$this->query->setRange(0, $maxRecords);
		}
	}

	function getPaginationArguments()
	{
		return $this->paginationArguments;
	}

	function wpquery_list_max($query, $value)
	{
		$this->paginationArguments['max'] = max(1, (int) $value);
	}

	function wpquery_filter_type($query, $value)
	{
		$value = explode(',', $value);
		$query->filterType($value);
	}

	function wpquery_filter_categories($query, $value)
	{
		$query->filterCategory($value);
	}

	function wpquery_filter_contributors($query, $value)
	{
		$query->filterContributors($value);
	}

	function wpquery_filter_deepcategories($query, $value)
	{
		$query->filterCategory($value, true);
	}

	function wpquery_filter_content($query, $value, array $arguments)
	{
		if (isset($arguments['field'])) {
			$fields = explode(',', $arguments['field']);
		} else {
			$fields = TikiLib::lib('tiki')->get_preference('unified_default_content', array('contents'), true);
		}

		$query->filterContent($value, $fields);
	}

	function wpquery_filter_language($query, $value)
	{
		$query->filterLanguage($value);
	}

	function wpquery_filter_relation($query, $value, $arguments)
	{
		if (! isset($arguments['qualifier'], $arguments['objecttype'])) {
			TikiLib::lib('errorreport')->report(tr('Missing objectype or qualifier for relation filter.'));
		}

		$token = (string) new Search_Query_Relation($arguments['qualifier'], $arguments['objecttype'], $value);
		$query->filterRelation($token);
	}

	function wpquery_filter_favorite($query, $value)
	{
		wpquery_filter_relation($query, $value, array('qualifier' => 'tiki.user.favorite.invert', 'objecttype' => 'user'));
	}

	function wpquery_filter_range($query, $value, array $arguments)
	{
		if ($arguments['from'] == 'now') {
			$arguments['from'] = TikiLib::lib('tiki')->now;
		}
		if ($arguments['to'] == 'now') {
			$arguments['to'] = TikiLib::lib('tiki')->now;
		}
		if (! isset($arguments['from']) && isset($arguments['to'], $arguments['gap'])) {
			$arguments['from'] = $arguments['to'] - $arguments['gap'];
		}
		if (! isset($arguments['to']) && isset($arguments['from'], $arguments['gap'])) {
			$arguments['to'] = $arguments['from'] + $arguments['gap'];
		}
		if (! isset($arguments['from'], $arguments['to'])) {
			TikiLib::lib('errorreport')->report(tr('Missing from or to for range filter.'));
		} 
		$query->filterRange($arguments['from'], $arguments['to'], $value); 
	}

	function wpquery_filter_textrange($query, $value, array $arguments)
	{
		if (! isset($arguments['from'], $arguments['to'])) {
			TikiLib::lib('errorreport')->report(tr('Missing from or to for range filter.'));
		}
		$query->filterTextRange($arguments['from'], $arguments['to'], $value);
	}

	function wpquery_sort_mode($query, $value, array $arguments)
	{
		if ($value == 'randommode') {
			if ( !empty($arguments['modes']) ) {
				$modes = explode(',', $arguments['modes']);
				$value = trim($modes[array_rand($modes)]);
				// append a direction if not already supplied
				$last = substr($value, strrpos($value, '_'));
				$directions = array('_asc', '_desc', '_nasc', '_ndesc');
				if (!in_array($last, $directions)) {
					$direction = $directions[array_rand($directions)];
					if (stripos($value, 'date')) {
						$value .= $direction;
					} else {
						$value .= str_replace('n', '', $direction);
					}
				}
			} else {
				return;
			}
		}
		$query->setOrder($value);
	}

	function wpquery_pagination_onclick($query, $value)
	{
		$this->paginationArguments['_onclick'] = $value;
	}

	function wpquery_pagination_offset_jsvar($query, $value)
	{
		$this->paginationArguments['offset_jsvar'] = $value;
	}

	function wpquery_pagination_offset_arg($query, $value)
	{
		$this->paginationArguments['offset_arg'] = $value;
	}

	function wpquery_pagination_max($query, $value)
	{
		$this->paginationArguments['max'] = (int) $value;
	}
}

