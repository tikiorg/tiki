<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tracker\Filter\Control;

class ObjectSelector implements Control
{
	private $fieldName;
	private $filters;
	private $value = '';

	function __construct($name, array $filters)
	{
		$this->fieldName = $name;
		$this->filters = $filters;
	}

	function applyInput(\JitFilter $input)
	{
		$this->value = (string) $input->{$this->fieldName}->int();
	}

	function getQueryArguments()
	{
		if ($this->value) {
			return [$this->fieldName => $this->value];
		} else {
			return [];
		}
	}

	function getDescription()
	{
		if ($this->value) {
			return \TikiLib::lib('object')->get_title($this->filters['type'], $this->value);
		}
	}

	function getId()
	{
		return $this->fieldName;
	}

	function isUsable()
	{
		return true;
	}

	function hasValue()
	{
		return ! empty($this->value);
	}

	function getValue()
	{
		return $this->value;
	}

	function __toString()
	{
		$params = $this->filters;
		$params['_simpleid'] = $this->fieldName;
		$params['_simplename'] = $this->fieldName;
		$params['_simplevalue'] = $this->value;

		$smarty = \TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_function_object_selector');

		return smarty_function_object_selector($params, $smarty);
	}
}
