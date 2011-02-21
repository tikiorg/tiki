<?php

function wikiplugin_list_info()
{
	return array(
		'name' => tra('List'),
		'documentation' => 'PluginList',
		'description' => tra('Pull object lists from the search index based on various search criteria and formatting rules'),
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
	$alternate = null;
	$output = null;
	$subPlugins = array();

	$query = new Search_Query;

	if (isset($_REQUEST['offset'])) {
		$query->setRange($_REQUEST['offset']);
	}

	$matches = WikiParser_PluginMatcher::match($data);
	$argumentParser = new WikiParser_PluginArgumentParser;

	foreach ($matches as $match) {
		$name = $match->getName();

		foreach ($argumentParser->parse($match->getArguments()) as $key => $value) {
			$function = "wpquery_{$name}_{$key}";

			if (function_exists($function)) {
				$function($query, $value);
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
	}

	$query->filterPermissions(Perms::get()->getGroups());

	if (isset($_REQUEST['sort_mode'])) {
		$query->setOrder($_REQUEST['sort_mode']);
	}

	global $unifiedsearchlib; require_once 'lib/search/searchlib-unified.php';
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

