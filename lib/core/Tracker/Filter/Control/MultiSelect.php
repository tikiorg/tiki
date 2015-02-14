<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tracker\Filter\Control;

class MultiSelect implements Control
{
	protected $fieldName;
	protected $options;
	protected $extra;
	protected $values = [];

	function __construct($name, $options, callable $extra = null)
	{
		$this->fieldName = $name;
		$this->options = $options;
		$this->extra = $extra;
	}

	function applyInput(\JitFilter $input)
	{
		$input->replaceFilter($this->fieldName, 'text');
		$this->values = $input->asArray($this->fieldName);
	}

	function getQueryArguments()
	{
		return [$this->fieldName => $this->values];
	}

	function getDescription()
	{
		$this->applyOptions();
		return implode(', ', array_map(function ($val) {
			return $this->options[$val];
		}, $this->values)) ?: null;
	}

	function getId()
	{
		return $this->fieldName;
	}

	function isUsable()
	{
		$this->applyOptions();
		return count($this->options) > 0;
	}

	function hasValue()
	{
		return count($this->values) > 0;
	}

	function getValues()
	{
		return $this->values;
	}

	protected function applyOptions()
	{
		if (is_callable($this->options)) {
			$this->options = call_user_func($this->options);
		}

		if ($this->extra) {
			// Include values selected, but not in the provided options,
			// which can happen with dynamic filters
			foreach ($this->values as $value) {
				if (! isset($this->options[$value])) {
					if ($label = call_user_func($this->extra, $value)) {
						$this->options[$value] = $label;
					}
				}
			}
		}
	}

	function __toString()
	{
		$this->applyOptions();

		$smarty = \TikiLib::lib('smarty');
		$smarty->assign('control', [
			'field' => $this->fieldName,
			'options' => $this->options,
			'values' => array_fill_keys($this->values, true),
		]);
		return $smarty->fetch('filter_control/multi_select.tpl');
	}
}
