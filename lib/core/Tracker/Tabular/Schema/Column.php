<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tracker\Tabular\Schema;

class Column
{
	private $permName;
	private $label;
	private $mode;
	private $renderTransform;
	private $parseIntoTransform;
	private $querySources = [];

	function __construct($permName, $mode)
	{
		$this->permName = $permName;
		$this->mode = $mode;
		$this->parseIntoTransform = function (& $info, $value) {
		};
	}

	function setLabel($label)
	{
		$this->label = $label;
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
		return "{$this->label} [{$this->permName}:{$this->mode}]";
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
}

