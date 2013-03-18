<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_list.php 44375 2012-12-21 18:39:49Z lphuberdeau $

class Search_Formatter_Builder
{
	private $parser;
	private $paginationArguments;

	private $formatterPlugin;
	private $subFormatters = array();

	function __construct()
	{
		$this->parser = new WikiParser_PluginArgumentParser;
		$this->paginationArguments = array(
			'offset_arg' => 'offset',
			'max' => 50,
		);
	}

	function setPaginationArguments($arguments)
	{
		$this->paginationArguments = $arguments;
	}

	function apply($matches)
	{
		foreach ($matches as $match) {
			$name = $match->getName();

			if ($name == 'output') {
				$this->handleOutput($match);
			}

			if ($name == 'format') {
				$this->handleFormat($match);
			}
		}
	}

	function getFormatter()
	{
		$plugin = $this->formatterPlugin;
		if (! $plugin) {
			$plugin = new Search_Formatter_Plugin_WikiTemplate("* {display name=title format=objectlink}\n");
		}

		$formatter = new Search_Formatter($plugin);

		foreach ($this->subFormatters as $name => $plugin) {
			$formatter->addSubFormatter($name, $plugin);
		}

		return $formatter;
	}

	private function handleFormat($match)
	{
		$arguments = $this->parser->parse($match->getArguments());

		if (isset($arguments['name'])) {
			$plugin = new Search_Formatter_Plugin_WikiTemplate($match->getBody());
			$this->subFormatters[$arguments['name']] = $plugin;
		}
	}

	private function handleOutput($output)
	{
		$arguments = $this->parser->parse($output->getArguments());

		if (isset($arguments['template'])) {
			if ($arguments['template'] == 'table') {
				$arguments['template'] = dirname(__FILE__) . '/../../../../templates/table.tpl';
			} else if (!file_exists($arguments['template'])) {
				TikiLib::lib('errorreport')->report(tr('Missing template "%0"', $arguments['template']));
				return '';
			}
			$abuilder = new Search_Formatter_ArrayBuilder;
			$templateData = $abuilder->getData($output->getBody());

			$plugin = new Search_Formatter_Plugin_SmartyTemplate($arguments['template']);
			$plugin->setData($templateData);
			$plugin->setFields($this->findFields($templateData));
		} elseif (isset($arguments['wiki']) && TikiLib::lib('tiki')->page_exists($arguments['wiki'])) {	
			$wikitpl = "tplwiki:" . $arguments['wiki'];
			$wikicontent = TikiLib::lib('smarty')->fetch($wikitpl);
			$plugin = new Search_Formatter_Plugin_WikiTemplate($wikicontent);
		} else {
			$plugin = new Search_Formatter_Plugin_WikiTemplate($output->getBody());
		}

		if (isset($arguments['pagination'])) {
			$plugin = new WikiPlugin_List_AppendPagination($plugin, $this->paginationArguments);
		}

		$this->formatterPlugin = $plugin;
	}

	private function findFields($data)
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
}

