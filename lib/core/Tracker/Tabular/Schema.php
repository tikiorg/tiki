<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tracker\Tabular;

class Schema
{
	private $columns = [];
	private $primaryKey;

	function __construct(\Tracker_Definition $definition)
	{
		$this->definition = $definition;
	}

	function getDefinition()
	{
		return $this->definition;
	}

	function addColumn($permName, $label, $mode)
	{
		$field = $this->definition->getFieldFromPermName($permName);
		$factory = $this->definition->getFieldFactory();

		if (! $field) {
			throw new Exception\FieldNotFound($permName);
		}

		$handler = $factory->getHandler($field);

		if (! $handler instanceof \Tracker_Field_Exportable) {
			throw new Exception\ModeNotSupported($mode);
		}

		$partial = $handler->getTabularSchema();
		$this->columns[] = $partial->lookupMode($permName, $mode);
	}

	function setPrimaryKey($field)
	{
		foreach ($this->columns as $column) {
			if ($column->getField() == $field) {
				$this->primaryKey = $column;
				return;
			}
		}

		throw new Exception\FieldNotFound($field);
	}

	function getPrimaryKey()
	{
		return $this->primaryKey;
	}

	private function lookupMode($permName, $mode)
	{
		foreach ($this->columns as $column) {
			if ($column->getField() == $permName && $column->getMode() == $mode) {
				return $column;
			}
		}

		throw new Exception\ModeNotSupported($permName, $mode);
	}

	function addNew($mode)
	{
		$column = new Schema\Column($mode);
		$this->columns[] = $column;
		return $column;
	}

	function getColumns()
	{
		return $this->columns;
	}
}
