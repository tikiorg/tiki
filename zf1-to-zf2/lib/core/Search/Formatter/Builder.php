<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Formatter_Builder
{
	private $parser;
	private $paginationArguments;

	private $formatterPlugin;
	private $subFormatters = array();
	private $alternateOutput;
	private $id;
	private $count;
	private $tsOn;

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

	function setFormatterPlugin(Search_Formatter_Plugin_Interface $plugin)
	{
		$this->formatterPlugin = $plugin;
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

			if ($name == 'alternate') {
				$this->handleAlternate($match);
			}

			if ($name == 'tablesorter') {
				$this->handleTablesorter($match);
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

		if ($this->alternateOutput > '') {
			$formatter->setAlternateOutput($this->alternateOutput);
		} else {
			$formatter->setAlternateOutput('^' . tra('No results for query.') . '^');
		}

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
			$plugin->setRaw(! empty($arguments['mode']) && $arguments['mode'] == 'raw');
			$this->subFormatters[$arguments['name']] = $plugin;
		}
	}

	private function handleAlternate($match)
	{
		$this->alternateOutput = $match->getBody();
	}

	private function handleOutput($output)
	{
        $smarty = TikiLib::lib('smarty');
		$arguments = $this->parser->parse($output->getArguments());

		if (isset($arguments['template'])) {
			if ($arguments['template'] == 'table') {
				$arguments['template'] = dirname(__FILE__) . '/../../../../templates/search/list/table.tpl';
				$arguments['pagination'] = true;
			} elseif ($arguments['template'] == 'medialist') {
				$arguments['template'] = dirname(__FILE__) . '/../../../../templates/search/list/medialist.tpl';
			} elseif ($arguments['template'] == 'carousel') {
				$arguments['template'] = dirname(__FILE__) . '/../../../../templates/search/list/carousel.tpl';
			} elseif ($arguments['template'] == 'count') {
				$arguments['template'] = dirname(__FILE__) . '/../../../../templates/search/list/count.tpl';
			} elseif (!file_exists($arguments['template'])) {
                $temp = $smarty->get_filename($arguments['template']);
                if (empty($temp)){ //if get_filename cannot find template, return error
                    TikiLib::lib('errorreport')->report(tr('Missing template "%0"', $arguments['template']));
                    return '';
                }
                $arguments['template'] = $temp;
			}
			$abuilder = new Search_Formatter_ArrayBuilder;
			$outputData = $abuilder->getData($output->getBody());
			foreach ($this->paginationArguments as $k => $v) {
				$outputData[$k] = $this->paginationArguments[$k];
			}
			$templateData = file_get_contents($arguments['template']);

			$plugin = new Search_Formatter_Plugin_SmartyTemplate($arguments['template']);
			$plugin->setData($outputData);
			$plugin->setFields($this->findFields($outputData, $templateData));
		} elseif (isset($arguments['wiki']) && TikiLib::lib('tiki')->page_exists($arguments['wiki'])) {
			$wikitpl = "tplwiki:" . $arguments['wiki'];
			$wikicontent = $smarty->fetch($wikitpl);
			$plugin = new Search_Formatter_Plugin_WikiTemplate($wikicontent);
		} else {
			$plugin = new Search_Formatter_Plugin_WikiTemplate($output->getBody());
		}

		if (isset($arguments['pagination'])) {

			$plugin = new Search_Formatter_AppendPagination($plugin, $this->paginationArguments);
		}

		$this->formatterPlugin = $plugin;
	}

	private function handleTablesorter($match)
	{
		$args = $this->parser->parse($match->getArguments());
		if (!$this->tsOn) {
			return false;
		}
		if (!Table_Check::isAjaxCall()) {
			$ts = new Table_Plugin;
			$ts->setSettings(
				$this->id,
				isset($args['server']) ? $args['server'] : 'n',
				isset($args['sortable']) ? $args['sortable'] : 'y',
				isset($args['sortList']) ? $args['sortList'] : null,
				isset($args['tsortcolumns']) ? $args['tsortcolumns'] : null,
				isset($args['tsfilters']) ? $args['tsfilters'] : null,
				isset($args['tsfilteroptions']) ? $args['tsfilteroptions'] : null,
				isset($args['tspaginate']) ? $args['tspaginate'] : null,
				isset($args['tscolselect']) ? $args['tscolselect'] : null,
				$GLOBALS['requestUri'],
				$this->count
			);
			if (is_array($ts->settings)) {
				$ts->settings['ajax']['offset'] = 'offset';
				Table_Factory::build('PluginWithAjax', $ts->settings);
			}
		}
	}

	private function findFields($outputData, $templateData)
	{
		$outputData = TikiLib::array_flat($outputData);

		// Heuristic based: only lowercase letters, digits and underscore
		$fields = array();
		foreach ($outputData as $candidate) {
			if (preg_match("/^[a-z0-9_]+$/", $candidate) || substr($candidate, 0, strlen('tracker_field_')) === 'tracker_field_') {
				$fields[] = $candidate;
			}
		}

		preg_match_all('/\$(result|row|res)\.([a-z0-9_]+)[\|\}\w]+/', $templateData, $matches);
		$fields = array_merge($fields, $matches[2]);	

		$fields = array_fill_keys(array_unique($fields), null);

		return $fields;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function setCount($count)
	{
		$this->count = $count;
	}

	public function setTsOn($tsOn)
	{
		$this->tsOn = $tsOn;
	}
}

