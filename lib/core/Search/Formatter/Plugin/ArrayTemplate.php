<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Formatter_Plugin_ArrayTemplate extends Search_Formatter_Plugin_AbstractTableTemplate
{
	private $fieldPermNames;
	private $nonTrackerFields;

	function __construct($template)
	{
		$this->fieldPermNames = [];
		$this->nonTrackerFields = ['object_id', 'object_type', 'creation_date', 'modification_date', 'tracker_status'];
		parent::__construct($template);
	}

	function getFormat()
	{
		return self::FORMAT_ARRAY;
	}

	function setFieldPermNames($fields)
	{
		$this->fieldPermNames = array_map(function ($f) {
			if (in_array($f['permName'], $this->nonTrackerFields)) {
				return $f['permName'];
			} else {
				return 'tracker_field_' . $f['permName'];
			}
		}, $fields);
	}

	function prepareEntry($valueFormatter)
	{
		$entry = [];
		$searchRow = $valueFormatter->getPlainValues();
		foreach ($this->fields as $field => $arguments) {
			if (! $this->canViewField($field)) {
				continue;
			}
			if (isset($arguments['format'])) {
				$format = $arguments['format'];
			} else {
				$format = 'plain';
			}
			unset($arguments['format']);
			unset($arguments['name']);
			unset($arguments['field']);
			if (isset($searchRow[$field . '_text'])) {
				$searchField = $field . '_text';
			} else {
				$searchField = $field;
			}
			$entry[str_replace('tracker_field_', '', $field)] = str_replace(
				['~np~', '~/np~'],
				'',
				trim($valueFormatter->$format($searchField, $arguments))
			);
		}
		return $entry;
	}

	function renderEntries(Search_ResultSet $entries)
	{
		$result = [];
		foreach ($entries as $entry) {
			$result[] = $entry;
		}
		return $result;
	}

	private function canViewField($field)
	{
		return in_array($field, $this->fieldPermNames);
	}
}
