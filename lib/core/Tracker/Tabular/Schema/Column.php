<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tracker\Tabular\Schema;

class Column
{
	const HEADER_PATTERN = '/\[(\*?)(\w+):([^\]]+)\]$/';

	private $permName;
	private $label;
	private $mode;
	private $isPrimary = false;
	private $isReadOnly = false;
	private $renderTransform;
	private $parseIntoTransform;
	private $querySources = [];
	private $incompatibilities = [];

	function __construct($permName, $mode)
	{
		$this->permName = $permName;
		$this->mode = $mode;
		$this->parseIntoTransform = function (& $info, $value) {
		};
	}

	function getLabel()
	{
		return $this->label;
	}

	function setLabel($label)
	{
		$this->label = $label;
		return $this;
	}

	function addIncompatibility($field, $mode)
	{
		$this->incompatibilities[] = [$field, $mode];
		return $this;
	}

	function setRenderTransform(callable $transform)
	{
		$this->renderTransform = $transform;
		return $this;
	}

	function setParseIntoTransform(callable $transform)
	{
		$this->parseIntoTransform = $transform;
		return $this;
	}

	function setPrimaryKey($pk)
	{
		$this->isPrimary = (bool) $pk;
		return $this;
	}

	function setReadOnly($readOnly)
	{
		$this->isReadOnly = (bool) $readOnly;
		return $this;
	}

	function is($field, $mode)
	{
		return $field == $this->permName && $mode == $this->mode;
	}

	function isPrimaryKey()
	{
		return $this->isPrimary;
	}

	function isReadOnly()
	{
		return $this->isReadOnly;
	}

	function getField()
	{
		return $this->permName;
	}

	function getMode()
	{
		return $this->mode;
	}

	function getEncodedHeader()
	{
		if ($this->isReadOnly) {
			return $this->label;
		} else {
			$pk = $this->isPrimary ? '*' : '';
			return "{$this->label} [$pk{$this->permName}:{$this->mode}]";
		}
	}

	function render($value)
	{
		return call_user_func_array($this->renderTransform, func_get_args());
	}

	function parseInto(& $info, $value)
	{
		$c = $this->parseIntoTransform;
		$c($info, $value);
	}

	function addQuerySource($name, $field)
	{
		$this->querySources[$name] = $field;
		return $this;
	}

	function getQuerySources()
	{
		return $this->querySources;
	}

	function validateAgainst(\Tracker\Tabular\Schema $schema)
	{
		if ($this->isPrimary && $this->isReadOnly) {
			throw new \Exception(tr('Primary Key fields cannot be read-only.'));
		}

		foreach ($schema->getColumns() as $column) {
			foreach ($this->incompatibilities as $entry) {
				list($field, $mode) = $entry;

				if ($column->is($field, $mode)) {
					// Skip incompatibility if either field is read-only
					if ($this->isReadOnly() || $column->isReadOnly()) {
						continue;
					}

					throw new \Exception(tr('Column "%0" cannot co-exist with "%1".',
						$column->getEncodedHeader(),
						$this->getEncodedHeader()));
				}
			}
		}
	}
}

