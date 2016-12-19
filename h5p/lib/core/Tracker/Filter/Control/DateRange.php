<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tracker\Filter\Control;

class DateRange implements Control
{
	private $fieldName;
	private $from = '';
	private $to = '';

	function __construct($name)
	{
		$this->fieldName = $name;
	}

	function applyInput(\JitFilter $input)
	{
		$this->from = $input->{$this->fieldName . '_from'}->int();
		$this->to = $input->{$this->fieldName . '_to'}->int();
	}

	function getQueryArguments()
	{
		if ($this->from && $this->to) {
			return [
				$this->fieldName . '_from' => $this->from,
				$this->fieldName . '_to' => $this->to,
			];
		} else {
			return [];
		}
	}

	function getDescription()
	{
		if ($this->hasValue()) {
			$tikilib = \TikiLib::lib('tiki');
			return tr('From %0 to %1',
				$tikilib->get_short_date($this->from),
				$tikilib->get_short_date($this->to)
				);
		} else {
			return '';
		}
	}

	function getId()
	{
		return $this->fieldName . '_from';
	}

	function isUsable()
	{
		return true;
	}

	function hasValue()
	{
		return ! empty($this->from) && ! empty($this->to);
	}

	function getFrom()
	{
		return $this->from;
	}

	function getTo()
	{
		// Date ranges are inclusive, so use end of day so last day is included
		return $this->to + 3600*24 - 1;
	}

	function __toString()
	{
		$smarty = \TikiLib::lib('smarty');
		$smarty->assign('control', [
			'field' => $this->fieldName,
			'from' => $this->from,
			'to' => $this->to,
		]);
		return $smarty->fetch('filter_control/date_range.tpl');
	}
}
