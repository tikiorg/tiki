<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: ArrayTemplate.php 60099 2016-11-01 20:09:05Z kroky6 $

class Search_Formatter_Plugin_ArrayTemplate implements Search_Formatter_Plugin_Interface
{
	private $template;
	private $format;
	private $fieldPermNames;

	function __construct($template)
	{
		$this->template = WikiParser_PluginMatcher::match($template);
		$this->format = self::FORMAT_JSON;
		$this->fieldPermNames = array();
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

			if ($name === 'column' ) {
				$arguments = $parser->parse($match->getArguments());

				if (isset($arguments['field']) && ! isset($fields[$arguments['field']])) {
					$fields[$arguments['field']] = null;
				}
			}
		}

		return $fields;
	}

	function setFieldPermNames($fields) {
		$this->fieldPermNames = array_map(function($f){ return 'tracker_field_'.$f['permName']; }, $fields);
	}

	function prepareEntry($valueFormatter)
	{
		$matches = clone $this->template;

		$entry = array();
		foreach ($matches as $match) {
			$name = $match->getName();

			if ($name === 'display') {
				$entry = array_merge($entry, $this->processDisplay($valueFormatter, $match->getBody(), $match->getArguments()));
			}

			if ($name === 'column') {
				$entry = array_merge($entry, $this->processColumn($valueFormatter, $match->getBody(), $match->getArguments()));
			}
		}

		return $entry;
	}

	function renderEntries(Search_ResultSet $entries)
	{
		$result = array();
		foreach ($entries as $entry) {
			$result[] = $entry;
		}
		return $result;
	}

	private function processDisplay($valueFormatter, $body, $arguments)
	{
		$parser = new WikiParser_PluginArgumentParser;
		$arguments = $parser->parse($arguments);

		$name = $arguments['name'];

		if( !$this->canViewField($name) ) {
			return array();
		}

		if (isset($arguments['format'])) {
			$format = $arguments['format'];
		} else {
			$format = 'plain';
		}

		unset($arguments['format']);
		unset($arguments['name']);
		return array( str_replace('tracker_field_', '', $name) => $valueFormatter->$format($name, $arguments) );
	}

	private function processColumn($valueFormatter, $body, $arguments)
	{
		$parser = new WikiParser_PluginArgumentParser;
		$arguments = $parser->parse($arguments);

		$field = $arguments['field'];

		if( !$this->canViewField($field) ) {
			return array();
		}

		return array( str_replace('tracker_field_', '', $field) => $valueFormatter->plain($field) );
	}

	private function canViewField($field) {
		return in_array($field, $this->fieldPermNames);
	}
}

