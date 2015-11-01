<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tracker\Filter\Control;

class TextField implements Control
{
	private $fieldName;
	private $value = '';

	function __construct($name)
	{
		$this->fieldName = $name;
	}

	function applyInput(\JitFilter $input)
	{
		$this->value = $input->{$this->fieldName}->text();
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
		return $this->value ?: null;
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
		$smarty = \TikiLib::lib('smarty');
		$smarty->assign('control', [
			'field' => $this->fieldName,
			'value' => $this->value,
		]);
		return $smarty->fetch('filter_control/text_field.tpl');
	}
}
