<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tracker\Filter;

class Collection
{
	private $definition;
	private $filters = [];
	private $collections = [];

	function __construct(\Tracker_Definition $definition)
	{
		$this->definition = $definition;
	}

	function addNew($permName, $mode)
	{
		$column = new Filter($permName, $mode);
		$this->filters[] = $column;
		return $column;
	}

	function getFilters()
	{
		return $this->filters;
	}
	
	function applyConditions(\JitFilter $input, \Search_Query $query)
	{
		foreach ($this->filters as $filter) {
			$filter->applyCondition($input, $query);
		}
	}

	function applyInput(\JitFilter $input)
	{
		foreach ($this->filters as $filter) {
			$filter->applyInput($input);
		}
	}

	function loadFilterDescriptor($descriptor)
	{
		foreach ($descriptor as $column) {
			$col = $this->addFilter($column['field'], $column['mode']);

			if ($column['label']) {
				$col->setLabel($column['label']);
			}
		}
	}

	private function addFilter($permName, $mode)
	{
		if (isset($this->collections[$permName])) {
			$partial = $this->collections[$permName];
		} else {
			$partial = $this->getFieldCollection($permName, $mode);
			$this->collections[$permName] = $partial;
		}

		$filter = $partial->lookupMode($permName, $mode);
		$this->filters[] = $filter;

		return $filter;
	}

	private function lookupMode($permName, $mode)
	{
		foreach ($this->filters as $filter) {
			if ($filter->getField() == $permName && $filter->getMode() == $mode) {
				return $filter;
			}
		}

		throw new Exception\ModeNotSupported($permName, $mode);
	}

	private function getFieldCollection($permName)
	{
		if ($partial = $this->getSystemCollection($permName)) {
			return $partial;
		}

		$field = $this->definition->getFieldFromPermName($permName);
		$factory = $this->definition->getFieldFactory();

		if (! $field) {
			throw new Exception\FieldNotFound($permName);
		}

		$handler = $factory->getHandler($field);

		if (! $handler instanceof \Tracker_Field_Filterable) {
			throw new Exception\ModeNotSupported($permName, 'any mode');
		}

		return $handler->getAvailableFilters();
	}

	private function getSystemCollection($name)
	{
		switch ($name) {
		case 'itemId':
			$collection = new self($this->definition);
			// TODO : Add filters
			return $collection;
		case 'status':
			$collection = new self($this->definition);
			// TODO : Add filters
			return $collection;
		}
	}
}
