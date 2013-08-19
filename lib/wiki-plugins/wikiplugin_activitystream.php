<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_activitystream_info()
{
	return array(
		'name' => tra('Activity Stream'),
		'documentation' => 'PluginActivityStream',
		'description' => tra('Generates a feed or activity stream based on the recorded events in the system.'),
		'prefs' => array('wikiplugin_activitystream', 'activity_custom_events'),
		'default' => 'y',
		'body' => tra('List configuration information'),
		'filter' => 'wikicontent',
		'profile_reference' => 'search_plugin_content',
		'icon' => 'img/icons/text_list_bullets.png',
		'tags' => array('advanced'),
		'params' => array(
		),
	);
}

function wikiplugin_activitystream($data, $params)
{
	$unifiedsearchlib = TikiLib::lib('unifiedsearch');

	$alternate = null;
	$output = null;

	$query = new Search_Query;
	$unifiedsearchlib->initQuery($query);
	$query->filterType('activity');

	$matches = WikiParser_PluginMatcher::match($data);

	$builder = new Search_Query_WikiBuilder($query);
	$builder->enableAggregate();
	$builder->apply($matches);

	$query->setOrder('modification_date_desc');

	if (! $index = $unifiedsearchlib->getIndex()) {
		return '';
	}

	$result = $query->search($index);

	$paginationArguments = $builder->getPaginationArguments();

	$resultBuilder = new Search_ResultSet_WikiBuilder($result);
	$resultBuilder->setPaginationArguments($paginationArguments);
	$resultBuilder->apply($matches);

	try {
		$plugin = new Search_Formatter_Plugin_SmartyTemplate('templates/activity/activitystream.tpl');
		$plugin->setFields(array('like_list' => true));
		$formatter = new Search_Formatter($plugin);
		$formatter->setDataSource($unifiedsearchlib->getDataSource());
		$out = $formatter->format($result);

		return $out;
	} catch (SmartyException $e) {
		return WikiParser_PluginOutput::userError($e->getMessage());
	}
}

