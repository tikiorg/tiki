<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Formatter_Plugin_WikiTemplate implements Search_Formatter_Plugin_Interface
{
	private $template;
	private $format;

	function __construct($template)
	{
		$this->template = WikiParser_PluginMatcher::match($template);
		$this->format = self::FORMAT_WIKI;
	}

	function setRaw($isRaw)
	{
		$this->format = $isRaw ? self::FORMAT_HTML : self::FORMAT_WIKI;
	}

	function getFormat()
	{
		return $this->format;
	}

	function getFields()
	{
		$parser = new WikiParser_PluginArgumentParser;

		$fields = array();
		foreach ($this->template as $match) {
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
		$matches = clone $this->template;

		foreach ($matches as $match) {
			$name = $match->getName();

			if ($name === 'display') {
				$match->replaceWith((string) $this->processDisplay($valueFormatter, $match->getBody(), $match->getArguments()));
			}
		}

		return $matches->getText();
	}

	function renderEntries(Search_ResultSet $entries)
	{
		$out = '';
		foreach ($entries as $entry) {
			$out .= $entry;
		}
		return $out;
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

		unset($arguments['format']);
		unset($arguments['name']);
		return $valueFormatter->$format($name, $arguments);
	}
}

