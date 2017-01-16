<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: ArrayTemplate.php 60099 2016-11-01 20:09:05Z kroky6 $

class Search_Formatter_Plugin_ArrayTemplate implements Search_Formatter_Plugin_Interface
{
	private $fields;
	private $format;
	private $fieldPermNames;

	function __construct($template)
	{
		$this->format = self::FORMAT_ARRAY;
		$this->fieldPermNames = array();
		$this->parseTemplate($template);
	}

	function parseTemplate($template)
	{
		$parser = new WikiParser_PluginArgumentParser;

		$matches = WikiParser_PluginMatcher::match($template);		
		foreach( $matches as $match ) {
			$name = $match->getName();
			
			if ($name === 'display') {
				$arguments = $parser->parse($match->getArguments());
				
				if (isset($arguments['name']) && ! isset($this->fields[$arguments['name']])) {
					$this->fields[$arguments['name']] = $arguments;
				}
			}
			
			if ($name === 'column' ) {
				$arguments = $parser->parse($match->getArguments());

				if (isset($arguments['field']) && ! isset($this->fields[$arguments['field']])) {
					$this->fields[$arguments['field']] = $arguments;
				}
			}
		}
	}

	function getFormat()
	{
		return $this->format;
	}

	function getFields()
	{
		$fields = array();
		foreach( $this->fields as $field => $arguments ) {
			$fields[$field] = isset($arguments['default']) ? $arguments['default'] : null;
		}
		return $fields;
	}

	function setFieldPermNames($fields) {
		$this->fieldPermNames = array_map(function($f){
			if( in_array($f['permName'], array('creation_date', 'modification_date', 'tracker_status')) ) {
				return $f['permName'];
			} else {
				return 'tracker_field_'.$f['permName'];
			}
		}, $fields);
	}

	function prepareEntry($valueFormatter)
	{
		$entry = array();
		$searchRow = $valueFormatter->getPlainValues();
		foreach ($this->fields as $field => $arguments) {
			if( !$this->canViewField($field) ) {
				continue;
			}
			if( isset($arguments['format']) ) {
				$format = $arguments['format'];
			} else {
				$format = 'plain';
			}
			unset($arguments['format']);
			unset($arguments['name']);
			unset($arguments['field']);
			if( isset($searchRow[$field.'_text']) ) {
				$searchField = $field.'_text';
			} else {
				$searchField = $field;
			}
			$entry[str_replace('tracker_field_', '', $field)] = str_replace(
				array('~np~', '~/np~'),
				'',
				trim($valueFormatter->$format($searchField, $arguments))
			);
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

	private function canViewField($field) {
		return in_array($field, $this->fieldPermNames);
	}
}

