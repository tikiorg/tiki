<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Query_WikiBuilder
{
	private $query;

	function __construct(Search_Query $query)
	{
		$this->query = $query;
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
	}

	function wpquery_list_max($query, $value)
	{
		if (!empty($_REQUEST['offset'])) {
			$start = $_REQUEST['offset'];
		} else {
			$start = 0;
		}
		$query->setRange($start, $value);	
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
				$value = $modes[array_rand($modes)];
			} else {
				return;
			}
		}
		$query->setOrder($value);
	}
}

