<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
	private $multi = false;

	function __construct($name, array $filters, $multi = false)
	{
		$this->fieldName = $name;
		$this->filters = $filters;
		$this->multi = $multi;
	}

	function applyInput(\JitFilter $input)
	{
		if ($this->multi) {
			$value = $input->{$this->fieldName}->text();
			if (! is_array($value)) {
				$value = preg_split("/\r\n|\r|\n|,/", $value);    // any line ends or comma
			}
			$this->value = $value;
		} else {
			$this->value = (string)$input->{$this->fieldName}->int();
		}
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

		$smarty = \TikiLib::lib('smarty');

		if ($this->multi) {
			$params['_name'] = $this->fieldName;
			$type = $this->filters['type'];
			$value = array_map(function ($v) use ($type) { return str_replace($type . ':', '', $v); }, $this->value);
			$params['_simplevalue'] = implode(',', $value);
			$params['_separator'] = ',';

			$smarty->loadPlugin('smarty_function_object_selector_multi');
			$result = smarty_function_object_selector_multi($params, $smarty);
		} else {
			$params['_simplevalue'] = $this->value;

			$smarty->loadPlugin('smarty_function_object_selector');
			$result = smarty_function_object_selector($params, $smarty);
		}

		return $result;
	}
}
