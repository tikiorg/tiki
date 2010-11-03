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
	}

	$query->filterPermissions(Perms::get()->getGroups());

	global $unifiedsearchlib; require_once 'lib/search/searchlib-unified.php';
	$index = $unifiedsearchlib->getIndex();

	$result = $query->search($index);
	
	$out = '';
	foreach ($result as $row) {
		$out .= "* {$row['object_type']} - {$row['object_id']}\n";
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

function wpquery_filter_content($query, $value)
{
	$query->filterContent($value);
}

function wpquery_sort_mode($query, $value)
{
	$query->setOrder($value);
}

