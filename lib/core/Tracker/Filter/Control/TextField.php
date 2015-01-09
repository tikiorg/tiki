<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
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

	function getId()
	{
		return $this->fieldName;
	}

	function __toString()
	{
		$smarty = \TikiLib::lib('smarty');
		$smarty->assign('control_text_field', [
			'field' => $this->fieldName,
			'value' => $this->value,
		]);
		return $smarty->fetch('filter_control/text_field.tpl');
	}
}
