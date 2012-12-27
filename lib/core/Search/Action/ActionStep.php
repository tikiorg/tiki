<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
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
			$readFrom = $fieldName;

			if (isset($this->definition[$fieldName])) {
				// Static value
				$out[$fieldName] = $this->definition[$fieldName];
				continue;
			} elseif (isset($this->definition[$fieldName . '_field'])) {
				// Use different field
				$readFrom = $this->definition[$fieldName . '_field'];
			}

			if (isset($entry[$readFrom])) {
				$out[$fieldName] = $entry[$readFrom];
			} elseif (! $isRequired) {
				$out[$fieldName] = null;
			} else {
				// Missing value, error
				return null;
			}
		}

		return new JitFilter($out);
	}
}

