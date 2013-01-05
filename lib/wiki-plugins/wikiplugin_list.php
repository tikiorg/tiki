<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
		'icon' => 'img/icons/text_list_bullets.png',
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

	$query = new Search_Query;
	$query->setWeightCalculator($unifiedsearchlib->getWeightCalculator());

	$matches = WikiParser_PluginMatcher::match($data);
	$argumentParser = new WikiParser_PluginArgumentParser;

	$builder = new Search_Query_WikiBuilder($query);
	$builder->apply($matches);

	foreach ($matches as $match) {
		$name = $match->getName();
		if ($name == 'output') {
			$output = $match;
		}

		if ($name == 'alternate') {
			$alternate = $match->getBody();
		}
	}

	if (! Perms::get()->admin) {
		$query->filterPermissions(Perms::get()->getGroups());
	}

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
				} else if (!file_exists($arguments['template'])) {
					TikiLib::lib('errorreport')->report(tr('Missing template "%0"', $arguments['template']));
					return '';
				}
				$abuilder = new Search_Formatter_ArrayBuilder;
				$templateData = $abuilder->getData($output->getBody());

				$plugin = new Search_Formatter_Plugin_SmartyTemplate($arguments['template']);
				$plugin->setData($templateData);
				$plugin->setFields(wp_list_findfields($templateData));
			} elseif (isset($arguments['wiki']) && TikiLib::lib('tiki')->page_exists($arguments['wiki'])) {	
				$wikitpl = "tplwiki:" . $arguments['wiki'];
				$wikicontent = TikiLib::lib('smarty')->fetch($wikitpl);
				$plugin = new Search_Formatter_Plugin_WikiTemplate($wikicontent);
			} else {
				$plugin = new Search_Formatter_Plugin_WikiTemplate($output->getBody());
			}

			if (isset($arguments['pagination'])) {
				$paginationArguments = $builder->getPaginationArguments();
				$plugin = new WikiPlugin_List_AppendPagination($plugin, $paginationArguments);
			}
		} else {
			$plugin = new Search_Formatter_Plugin_WikiTemplate("* {display name=title format=objectlink}\n");
		}

		$formatter = new Search_Formatter($plugin);
		$formatter->setDataSource($unifiedsearchlib->getDataSource());
		$builder = new Search_Formatter_Builder($formatter);
		$builder->apply($matches);

		$out = $formatter->format($result);
	} elseif (!empty($alternate)) {
		$out = $alternate;
	} else {
		$out = '^' . tra('No results for query.') . '^';
	}

	return $out;
}

class WikiPlugin_List_AppendPagination implements Search_Formatter_Plugin_Interface
{
	private $parent;
	private $arguments;

	function __construct(Search_Formatter_Plugin_Interface $parent, array $arguments = array())
	{ 
		$this->parent = $parent;
		$this->arguments = $arguments;
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

	function renderEntries(Search_ResultSet $entries)
	{
		global $smarty;
		$smarty->loadPlugin('smarty_block_pagination_links');
		$arguments = $this->arguments;
		$arguments['resultset'] = $entries;
		$pagination = smarty_block_pagination_links($arguments, '', $smarty, $tmp = false);

		if ($this->getFormat() == Search_Formatter_Plugin_Interface::FORMAT_WIKI) {
			$pagination = "~np~$pagination~/np~";
		}
		return $this->parent->renderEntries($entries) . $pagination;
	}
}

function wp_list_findfields($data)
{
	$data = TikiLib::array_flat($data);

	// Heuristic based: only lowecase letters, digits and underscore
	$fields = array();
	foreach ($data as $candidate) {
		if (preg_match("/^[a-z0-9_]+$/", $candidate) || substr($candidate, 0, strlen('tracker_field_')) === 'tracker_field_') {
			$fields[] = $candidate;
		}
	}

	return $fields;
}

