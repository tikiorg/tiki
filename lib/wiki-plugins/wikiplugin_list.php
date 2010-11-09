<?php

function wikiplugin_list_info()
{
	return array(
		'name' => tra('List'),
		'description' => tra('Pull object lists from the search index based on various search criterias and formatting rules.'),
		'prefs' => array('wikiplugin_list'),
		'body' => tra('List configuration information'),
		'filter' => 'wikicontent',
		'params' => array(
		),
	);
}

function wikiplugin_list($data, $params)
{
	$alternate = null;
	$output = null;

	$query = new Search_Query;

	$matches = WikiParser_PluginMatcher::match($data);
	$argumentParser = new WikiParser_PluginArgumentParser;

	foreach ($matches as $match) {
		$name = $match->getName();

		foreach ($argumentParser->parse($match->getArguments()) as $key => $value) {
			$function = "wpquery_{$name}_{$key}";

			if (function_exists($function)) {
				call_user_func($function, $query, $value);
			}
		}

		if ($name == 'output') {
			$output = $match;
		}

		if ($name == 'alternate') {
			$alternate = $match->getBody();
		}
	}

	$query->filterPermissions(Perms::get()->getGroups());

	global $unifiedsearchlib; require_once 'lib/search/searchlib-unified.php';
	$index = $unifiedsearchlib->getIndex();

	$result = $query->search($index);

	if (count($result)) {
		if ($output) {
			$plugin = new Search_Formatter_Plugin_WikiTemplate($output->getBody());
		} else {
			$plugin = new Search_Formatter_Plugin_WikiTemplate("* {display name=title}\n");
		}
		$formatter = new Search_Formatter($plugin);

		$out = $formatter->format($result);
	} elseif($alternate) {
		$out = $alternate;
	} else {
		$out = '^' . tra('No results for query.') . '^';
	}

	return $out;
}

function wpquery_filter_type($query, $value)
{
	$query->filterType($value);
}

function wpquery_filter_categories($query, $value)
{
	$query->filterCategory($value);
}

function wpquery_filter_deepcategories($query, $value)
{
	$query->filterCategory($value, true);
}

function wpquery_filter_content($query, $value)
{
	$query->filterContent($value);
}

function wpquery_filter_language($query, $value)
{
	$query->filterLanguage($value);
}

function wpquery_sort_mode($query, $value)
{
	$query->setOrder($value);
}

