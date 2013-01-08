<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
		$required = array_keys($this->action->getValues());
		$found = array();

		foreach ($this->definition as $key => $value) {
			if (preg_match('/^(.*)_field$/', $key, $parts)) {
				$key = $parts[1];

				if (in_array($key, $required)) {
					$required[] = $value;
				}
			} elseif (preg_match('/^(.*)_field_coalesce$/', $key, $parts)) {
				$key = $parts[1];

				if (in_array($key, $required)) {
					$required = array_merge($required, $this->splitFields($value));
				}
			} else {
				$found[] = $key;
			}
		}

		return array_diff($required, $found);
	}

	function validate(array $entry)
	{
		if ($entry = $this->prepare($entry)) {
			return $this->action->validate($entry);
		}
	}

	function execute(array $entry)
	{
		if ($entry = $this->prepare($entry)) {
			return $this->action->execute($entry);
		}
	}

	private function prepare($entry)
	{
		$out = array();

		foreach ($this->action->getValues() as $fieldName => $isRequired) {
			$readFrom = array($fieldName);

			if (isset($this->definition[$fieldName])) {
				// Static value
				$out[$fieldName] = $this->definition[$fieldName];
				continue;
			} elseif (isset($this->definition[$fieldName . '_field'])) {
				// Use different field
				$readFrom = array($this->definition[$fieldName . '_field']);
			} elseif (isset($this->definition[$fieldName . '_field_coalesce'])) {
				$readFrom = $this->splitFields($this->definition[$fieldName . '_field_coalesce']);
			}

			foreach ($readFrom as $candidate) {
				if (isset($entry[$candidate])) {
					$out[$fieldName] = $entry[$candidate];
					break;
				}
			}

			if (! isset($out[$fieldName])) {
				if (! $isRequired) {
					$out[$fieldName] = null;
				} else {
					// Missing value, error
					return null;
				}
			}
		}

		return new JitFilter($out);
	}

	private function splitFields($string)
	{
		return array_filter(array_map('trim', explode(',', $string)));
	}
}

