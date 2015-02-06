<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Action_ActionStep implements Search_Action_Step
{
	private $action;
	private $definition;

	function __construct(Search_Action_Action $action, array $definition)
	{
		$this->action = $action;
		$this->definition = $definition;
	}

	function getFields()
	{
		$initial = array();
		foreach (array_keys($this->action->getValues()) as $keyName) {
			$initial[] = rtrim($keyName, '+');
		}

		$required = array();
		$found = array();

		foreach ($this->definition as $key => $value) {
			if (preg_match('/^(.*)_field$/', $key, $parts)) {
				$key = $parts[1];

				if (in_array($key, $initial)) {
					$required[] = $value;
					$initial = array_diff($initial, array($key));
				}
			} elseif (preg_match('/^(.*)_field_(coalesce|multiple)$/', $key, $parts)) {
				$key = $parts[1];

				if (in_array($key, $initial)) {
					$required = array_merge($required, $this->splitFields($value));
					$initial = array_diff($initial, array($key));
				}
			} else {
				$found[] = $key;
			}
		}

		return array_diff(array_merge($initial, $required), $found);
	}

	function validate(array $entry)
	{
		if ($entry = $this->prepare($entry)) {
			return $this->action->validate($entry);
		}

		return false;
	}

	function execute(array $entry)
	{
		if ($entry = $this->prepare($entry)) {
			return $this->action->execute($entry);
		}

		return false;
	}

	private function prepare($entry)
	{
		$out = array();

		foreach ($this->action->getValues() as $fieldName => $isRequired) {
			$initialName = $fieldName;
			$fieldName = rtrim($fieldName, '+');
			$requiresArray = $initialName != $fieldName;

			$values = array();

			if (isset($this->definition[$fieldName])) {
				// Static value
				$values = array($this->definition[$fieldName]);
			} elseif (isset($this->definition[$fieldName . '_field'])) {
				// Use different field
				$values = $this->readValues($entry, array($this->definition[$fieldName . '_field']));
			} elseif (isset($this->definition[$fieldName . '_field_coalesce'])) {
				$readFrom = $this->splitFields($this->definition[$fieldName . '_field_coalesce']);
				$values = $this->readValues($entry, $readFrom);
				$values = array_slice($values, 0, 1);
			} elseif (isset($this->definition[$fieldName . '_field_multiple'])) {
				$readFrom = $this->splitFields($this->definition[$fieldName . '_field_multiple']);
				$values = $this->readValues($entry, $readFrom);
			} else {
				$values = $this->readValues($entry, array($fieldName));
			}

			if (empty($values) && $isRequired) {
				return null;
			} elseif ($requiresArray) {
				$out[$fieldName] = $values;
			} else {
				$out[$fieldName] = empty($values) ? null : reset($values);
			}
		}

		return new JitFilter($out);
	}

	private function readValues($entry, $readFrom)
	{
		$values = array();

		foreach ($readFrom as $candidate) {
			if (isset($entry[$candidate])) {
				$values[] = $entry[$candidate];
			}
		}

		return $values;
	}

	private function splitFields($string)
	{
		return array_filter(array_map('trim', explode(',', $string)));
	}
}

