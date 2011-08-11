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
	$filter = array();
	$contentfilters = array();

	$matches = WikiParser_PluginMatcher::match($data);
	$argumentParser = new WikiParser_PluginArgumentParser;

	foreach ($matches as $match) {
		$name = $match->getName();
		$arguments = $argumentParser->parse($match->getArguments());

		foreach ($arguments as $key => $value) {
			if ($name == 'filter') {
				if ($key == 'content') {
					$contentfilters[] = array('value' => $value, 'arguments' => $arguments); 
				} else {
					$filter[$key] = $value;	
				}	
			} elseif ($name == 'format' && $key == 'name') {
				wpformat_format_name($subPlugins, $value, $match->getBody());
			} elseif ($name == 'sort' && $key == 'mode') {
				$sort_mode = $value;	
			}
		}
		if ($name == 'output') {
			$output = $match;
		} elseif ($name == 'alternate') {
			$alternate = $match->getBody();
		}
	}

	$query = $unifiedsearchlib->buildQuery($filter);
	foreach ($contentfilters as $cf) {
		wpquery_filter_content($query, $cf['value'], $cf['arguments']);
	} 
	if (isset($_REQUEST['offset'])) {
		$query->setRange($_REQUEST['offset']);
	}

	$query->filterPermissions(Perms::get()->getGroups());

	if (isset($_REQUEST['sort_mode'])) {
		$sort_mode = $_REQUEST['sort_mode'];
	}
	if (isset($sort_mode)) {
		$query->setOrder($sort_mode); 
	}

	$index = $unifiedsearchlib->getIndex();

	$result = $query->search($index);

	if (count($result)) {
		if ($output) {
			$arguments = $argumentParser->parse($output->getArguments());
	
			if (isset($arguments['template'])) {
				if ($arguments['template'] == 'table') {
					$arguments['template'] = dirname(__FILE__) . '/../../templates/table.tpl';
				}
				$builder = new Search_Formatter_ArrayBuilder;

				$plugin = new Search_Formatter_Plugin_SmartyTemplate($arguments['template']);
				$plugin->setData($builder->getData($output->getBody()));
			} elseif (isset($arguments['wiki'])) {
				$wikitpl = "tplwiki:" . $arguments['wiki'];
				$wikicontent = TikiLib::lib('smarty')->fetch($wikitpl);
				$wikicontent = preg_replace('/\{\$([^\}]+)\}/', '{display name=\\1}', $wikicontent); 
				$plugin = new Search_Formatter_Plugin_WikiTemplate($wikicontent);
			} else {
				$plugin = new Search_Formatter_Plugin_WikiTemplate($output->getBody());
			}

			if (isset($arguments['pagination'])) {
				$plugin = new WikiPlugin_List_AppendPagination($plugin);
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
	} elseif($alternate) {
		$out = $alternate;
	} else {
		$out = '^' . tra('No results for query.') . '^';
	}

	return $out;
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

function wpformat_format_name(&$subPlugins, $value, $body)
{
	$subPlugins[$value] = new Search_Formatter_Plugin_WikiTemplate($body);
}

class WikiPlugin_List_AppendPagination implements Search_Formatter_Plugin_Interface
{
	private $parent;

	function __construct(Search_Formatter_Plugin_Interface $parent)
	{
		$this->parent = $parent;
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
		require_once $smarty->_get_plugin_filepath('block', 'pagination_links');
		$pagination = smarty_block_pagination_links(array('cant' => $count, 'offset' => $offset, 'step' => $maxRecords), '', $smarty, false);

		if ($this->getFormat() == Search_Formatter_Plugin_Interface::FORMAT_WIKI) {
			$pagination = "~np~$pagination~/np~";
		}

		return $this->parent->renderEntries($entries, $count, $offset, $maxRecords)
			. $pagination;
	}
}

