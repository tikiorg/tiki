<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tracker\Tabular\Source;

class TrackerSourceEntry
{
	private $item;
	private $data;

	function __construct($itemId)
	{
		$this->item = \Tracker_Item::fromId($itemId);
		$this->data = $this->item->getData();
	}

	function render(\Tracker\Tabular\Schema\Column $column)
	{
		$field = $column->getField();
		return $column->render($this->data['fields'][$field]);
	}
}

