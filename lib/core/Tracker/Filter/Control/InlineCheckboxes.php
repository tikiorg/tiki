<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tracker\Filter\Control;

class InlineCheckboxes extends MultiSelect
{
	function getId()
	{
		$first = key($this->options);
		return $this->fieldName . '-' . $first;
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
		return $smarty->fetch('filter_control/inline_checkboxes.tpl');
	}
}
