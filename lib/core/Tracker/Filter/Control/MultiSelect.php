<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tracker\Filter\Control;

class MultiSelect implements Control
{
	private $fieldName;
	private $options;
	private $values = [];

	function __construct($name, array $options)
	{
		$this->fieldName = $name;
		$this->options = $options;
	}

	function applyInput(\JitFilter $input)
	{
		$input->replaceFilter($this->fieldName, 'text');
		$this->values = $input->asArray($this->fieldName);
	}

	function getId()
	{
		return $this->fieldName;
	}

	function getValues()
	{
		return $this->values;
	}

	function __toString()
	{
		$smarty = \TikiLib::lib('smarty');
		$smarty->assign('control', [
			'field' => $this->fieldName,
			'options' => $this->options,
			'values' => array_fill_keys($this->values, true),
		]);
		return $smarty->fetch('filter_control/multi_select.tpl');
	}
}
