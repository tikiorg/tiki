<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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
		'prefs' => array('wikiplugin_list'),
		'body' => tra('List configuration information'),
		'filter' => 'wikicontent',
		'icon' => 'pics/icons/text_list_bullets.png',
		'tags' => array( 'basic' ),
		'params' => array(
		),
	);
}

function wikiplugin_list($data, $params)
{
	$unifiedsearchlib = TikiLib::lib('unifiedsearch');

	$alternate = null;
	$output = null;
	$subPlugins = array();

	$query = new Search_Query;
	$query->setWeightCalculator($unifiedsearchlib->getWeightCalculator());

	if (isset($_REQUEST['offset'])) {
		if (isset($_REQUEST['maxRecords'])) {
			$query->setRange($_REQUEST['offset'], $_REQUEST['maxRecords']);
		} else {
			$query->setRange($_REQUEST['offset']);
		}
	}

	$matches = WikiParser_PluginMatcher::match($data);
	$argumentParser = new WikiParser_PluginArgumentParser;

	$onclick = '';
	$offset_jsvar = '';

	foreach ($matches as $match) {
		$name = $match->getName();
		$arguments = $argumentParser->parse($match->getArguments());

		foreach ($arguments as $key => $value) {
			$function = "wpquery_{$name}_{$key}";

			if (function_exists($function)) {
				$function($query, $value, $arguments);
			}

			$function = "wpformat_{$name}_{$key}";

			if (function_exists($function)) {
				$function($subPlugins, $value, $match->getBody());
			}
		}

		if ($name == 'output') {
			$output = $match;
		}

		if ($name == 'alternate') {
			$alternate = $match->getBody();
		}

		if ($name == 'pagination' && isset($arguments['onclick'])) {
			$onclick = $arguments['onclick'];
		}
		if ($name == 'pagination' && isset($arguments['offset_jsvar'])) {
			$offset_jsvar = $arguments['offset_jsvar'];
		}
	}

	$query->filterPermissions(Perms::get()->getGroups());

	if (!empty($_REQUEST['sort_mode'])) {
		$query->setOrder($_REQUEST['sort_mode']);
	}

	$index = $unifiedsearchlib->getIndex();

	$result = $query->search($index);

	if (count($result)) {
		if (!empty($output)) {
			$arguments = $argumentParser->parse($output->getArguments());
	
			if (isset($arguments['template'])) {
				if ($arguments['template'] == 'table') {
					$arguments['template'] = dirname(__FILE__) . '/../../templates/table.tpl';
				}
				$builder = new Search_Formatter_ArrayBuilder;

				$plugin = new Search_Formatter_Plugin_SmartyTemplate($arguments['template']);
				$plugin->setData($builder->getData($output->getBody()));
			} elseif (isset($arguments['wiki']) && TikiLib::lib('tiki')->page_exists($arguments['wiki'])) {	
				$wikitpl = "tplwiki:" . $arguments['wiki'];
				$wikicontent = TikiLib::lib('smarty')->fetch($wikitpl);
				$plugin = new Search_Formatter_Plugin_WikiTemplate($wikicontent);
			} else {
				$plugin = new Search_Formatter_Plugin_WikiTemplate($output->getBody());
			}

			if (isset($arguments['pagination'])) {
				$plugin = new WikiPlugin_List_AppendPagination($plugin, $onclick, $offset_jsvar);
			}
		} else {
			$plugin = new Search_Formatter_Plugin_WikiTemplate("* {display name=title format=objectlink}\n");
		}

		$formatter = new Search_Formatter($plugin);
		$formatter->setDataSource($unifiedsearchlib->getDataSource());

		foreach ($subPlugins as $key => $plugin) {
			$formatter->addSubFormatter($key, $plugin);
		}

		$out = $formatter->format($result);
	} elseif(!empty($alternate)) {
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
	wpquery_filter_relation($query, $value, array(
		'qualifier' => 'tiki.user.favorite.invert',
		'objecttype' => 'user',
	));
}

function wpquery_filter_range($query, $value, array $arguments)
{
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

function wpquery_sort_mode($query, $value)
{
	$query->setOrder($value);
}

function wpformat_format_name(&$subPlugins, $value, $body)
{
	$subPlugins[$value] = new Search_Formatter_Plugin_WikiTemplate($body);
}

class WikiPlugin_List_AppendPagination implements Search_Formatter_Plugin_Interface
{
	private $parent;

	function __construct(Search_Formatter_Plugin_Interface $parent, $onclick, $offset_jsvar)
	{ 
		$this->parent = $parent;
		$this->offset_jsvar = $offset_jsvar;
		$this->onclick = $onclick;
	}

	function getFields()
	{
		return $this->parent->getFields();
	}

	function getFormat()
	{
		return $this->parent->getFormat();
	}

	function prepareEntry($entry)
	{
		return $this->parent->prepareEntry($entry);
	}

	function renderEntries($entries, $count, $offset, $maxRecords)
	{
		global $smarty;
		$smarty->loadPlugin('smarty_block_pagination_links');
		$pagination = smarty_block_pagination_links(array('_onclick' => $this->onclick, 'offset_jsvar' => $this->offset_jsvar, 'cant' => $count, 'offset' => $offset, 'step' => $maxRecords), '', $smarty, false);

		if ($this->getFormat() == Search_Formatter_Plugin_Interface::FORMAT_WIKI) {
			$pagination = "~np~$pagination~/np~";
			return $this->parent->renderEntries($entries, $count, $offset, $maxRecords) . $pagination;
		} else { 
			return $this->parent->renderEntries($entries, $count, $offset, $maxRecords, $pagination); 
		}
	}
}

