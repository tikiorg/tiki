<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tracker\Filter;

class Filter
{
	private $permName;
	private $mode;
	private $position = 'default';
	private $label;
	private $help;
	private $control;
	private $applyCondition;

	function __construct($permName, $mode)
	{
		$this->permName = $permName;
		$this->mode = $mode;
	}

	function getField()
	{
		return $this->permName;
	}
	
	function getMode()
	{
		return $this->mode;
	}

	function setLabel($label)
	{
		$this->label = $label;
		return $this;
	}

	function getLabel()
	{
		return $this->label;
	}

	function getPosition()
	{
		return $this->position;
	}

	function setPosition($position)
	{
		$this->position = $position;
		return $this;
	}

	function setHelp($help)
	{
		$this->help = $help;
		return $this;
	}

	function getHelp()
	{
		return $this->help;
	}

	function setControl(Control\Control $control)
	{
		$this->control = $control;
		return $this;
	}

	function getControl()
	{
		return $this->control;
	}

	function setApplyCondition(callable $apply)
	{
		$this->applyCondition = $apply;
		return $this;
	}

	function applyCondition(\Search_Query $query)
	{
		$cb = $this->applyCondition;
		$cb($this->control, $query);
	}

	function applyInput(\JitFilter $input)
	{
		$this->control->applyInput($input);
	}

	function copyProperties(self $other)
	{
		$this->help = $other->help;
		$this->label = $other->label;
		$this->position = $other->position;
		$this->control = clone $other->control;
		$this->applyCondition = $other->applyCondition;
	}
}
