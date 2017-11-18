<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

abstract class Search_Formatter_Plugin_AbstractTableTemplate implements Search_Formatter_Plugin_Interface
{
	protected $fields;

	function __construct($template)
	{
		$this->parseTemplate($template);
	}

	function parseTemplate($template)
	{
		$parser = new WikiParser_PluginArgumentParser;

		$matches = WikiParser_PluginMatcher::match($template);
		foreach ($matches as $match) {
			$name = $match->getName();

			if ($name === 'display') {
				$arguments = $parser->parse($match->getArguments());

				if (isset($arguments['name']) && ! isset($this->fields[$arguments['name']])) {
					$this->fields[$arguments['name']] = $arguments;
				}
			}

			if ($name === 'column') {
				$arguments = $parser->parse($match->getArguments());

				if (isset($arguments['field']) && ! isset($this->fields[$arguments['field']])) {
					$this->fields[$arguments['field']] = $arguments;
				}
			}
		}
	}

	function getFields()
	{
		$fields = [];
		foreach ($this->fields as $field => $arguments) {
			$fields[$field] = isset($arguments['default']) ? $arguments['default'] : null;
		}
		return $fields;
	}
}
