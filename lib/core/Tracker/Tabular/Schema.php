<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tracker\Tabular;

class Schema
{
	private $columns = [];
	private $primaryKey;
	private $schemas = [];

	function __construct(\Tracker_Definition $definition)
	{
		$this->definition = $definition;
	}

	function getDefinition()
	{
		return $this->definition;
	}

	function addColumn($permName, $mode)
	{
		if (isset($this->schemas[$permName])) {
			$partial = $this->schemas[$permName];
		} elseif ($partial = $this->getSystemSchema($permName)) {
			$this->schemas[$permName] = $partial;
		} else {
			$partial = $this->getFieldSchema($permName, $mode);
			$this->schemas[$permName] = $partial;
		}

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

	function addNew($permName, $mode)
	{
		$column = new Schema\Column($permName, $mode);
		$this->columns[] = $column;
		return $column;
	}

	function getColumns()
	{
		return $this->columns;
	}

	private function getFieldSchema($permName)
	{
		$field = $this->definition->getFieldFromPermName($permName);
		$factory = $this->definition->getFieldFactory();

		if (! $field) {
			throw new Exception\FieldNotFound($permName);
		}

		$handler = $factory->getHandler($field);

		if (! $handler instanceof \Tracker_Field_Exportable) {
			throw new Exception\ModeNotSupported($permName, 'any mode');
		}

		return $handler->getTabularSchema();
	}

	private function getSystemSchema($name)
	{
		switch ($name) {
		case 'itemId':
			$schema = new self($this->definition);
			$schema->addNew($name, 'id')
				->setLabel(tr('Item ID'))
				->addQuerySource('itemId', 'object_id')
				->setRenderTransform(function ($value, $extra) {
					return $extra['itemId'];
				})
				->setParseIntoTransform(function (& $info, $value) {
					$info['itemId'] = (int) $value;
				})
				;
			return $schema;
		case 'status':
			$types = \TikiLib::lib('trk')->status_types();
			$invert = array_flip(array_map(function ($s) {
				return $s['name'];
			}, $types));

			$schema = new self($this->definition);
			$schema->addNew($name, 'system')
				->setLabel(tr('Status'))
				->addQuerySource('status', 'tracker_status')
				->setRenderTransform(function ($value, $extra) {
					return $extra['status'];
				})
				->setParseIntoTransform(function (& $info, $value) {
					$info['status'] = $value;
				})
				;
			$schema->addNew($name, 'name')
				->setLabel(tr('Status'))
				->addQuerySource('status', 'tracker_status')
				->setRenderTransform(function ($value, $extra) use ($types) {
					return $types[$extra['status']]['name'];
				})
				->setParseIntoTransform(function (& $info, $value) use ($invert) {
					$info['status'] = $invert[$value];
				})
				;
			return $schema;
		}
	}
}
