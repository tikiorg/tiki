<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
	private $isExportOnly = false;
	private $displayAlign = 'left';
	private $renderTransform;
	private $parseIntoTransform;
	private $querySources = [];
	private $incompatibilities = [];
	private $plainReplacement = null;

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

	function getDisplayAlign()
	{
		return $this->displayAlign;
	}

	function setDisplayAlign($align)
	{
		$this->displayAlign = $align;
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

	function setExportOnly($exportOnly)
	{
		$this->isExportOnly = (bool) $exportOnly;
		return $this;
	}

	function setPlainReplacement($replacement)
	{
		$this->plainReplacement = $replacement;
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

	function isExportOnly()
	{
		return $this->isExportOnly;
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

	function getPlainReplacement()
	{
		return $this->plainReplacement;
	}

	function render($value)
	{
		return call_user_func_array($this->renderTransform, func_get_args());
	}

	function parseInto(& $info, $value)
	{
		$c = $this->parseIntoTransform;
		if (! $this->isReadOnly) {
			$c($info, $value);
		}
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

		$selfCount = 0;

		foreach ($schema->getColumns() as $column) {
			if ($column->is($this->permName, $this->mode)) {
				$selfCount++;
			}

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

		if ($selfCount > 1) {
			throw new \Exception(tr('Column "%0:%1" found multiple times.', $this->permName, $this->mode));
		}
	}

	function withWrappedRenderTransform(callable $callback)
	{
		$column = new self($this->permName, $this->mode);
		$column->label = $this->label;
		$column->isPrimary = $this->isPrimary;
		$column->isReadOnly = $this->isReadOnly;
		$column->isExportOnly = $this->isExportOnly;
		$column->displayAlign = $this->displayAlign;
		$column->parseIntoTransform = $this->parseIntoTransform;
		$column->querySources = $this->querySources;
		$column->incompatibilities = $this->incompatibilities;
		$column->plainReplacement = $this->plainReplacement;

		$column->renderTransform = function () use ($callback) {
			$value = call_user_func_array($this->renderTransform, func_get_args());
			return $callback($value);
		};

		return $column;
	}
}

