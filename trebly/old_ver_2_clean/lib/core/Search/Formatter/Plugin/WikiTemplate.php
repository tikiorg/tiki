<?php

class Search_Formatter_Plugin_WikiTemplate implements Search_Formatter_Plugin_Interface
{
	private $template;

	function __construct($template)
	{
		$this->template = $template;
	}

	function getFormat()
	{
		return self::FORMAT_WIKI;
	}

	function getFields()
	{
		$matches = WikiParser_PluginMatcher::match($this->template);
		$parser = new WikiParser_PluginArgumentParser;

		$fields = array();
		foreach ($matches as $match) {
			$name = $match->getName();

			if ($name === 'display') {
				$arguments = $parser->parse($match->getArguments());

				if (isset($arguments['name']) && ! isset($fields[$arguments['name']])) {
					$fields[$arguments['name']] = isset($arguments['default']) ? $arguments['default'] : null;
				}
			}
		}

		return $fields;
	}

	function prepareEntry($valueFormatter)
	{
		$matches = WikiParser_PluginMatcher::match($this->template);

		foreach ($matches as $match) {
			$name = $match->getName();

			if ($name === 'display') {
				$match->replaceWith($this->processDisplay($valueFormatter, $match->getBody(), $match->getArguments()));
			}
		}

		return $matches->getText();
	}

	function renderEntries($entries, $count, $offset, $maxRecords)
	{
		return implode('', $entries);
	}

	private function processDisplay($valueFormatter, $body, $arguments)
	{
		$parser = new WikiParser_PluginArgumentParser;
		$arguments = $parser->parse($arguments);

		$name = $arguments['name'];

		if (isset($arguments['format'])) {
			$format = $arguments['format'];
		} else {
			$format = 'plain';
		}

		return $valueFormatter->$format($name);
	}
}

