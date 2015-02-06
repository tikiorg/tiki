<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tracker\Tabular\Source;

class CsvSourceEntry
{
	private $data;

	function __construct($data)
	{
		$this->data = $data;
	}

	function render(\Tracker\Tabular\Schema\Column $column)
	{
		$entry = $this->data[spl_object_hash($column)];
		return $column->render($entry);
	}

	function parseInto(& $info, $column)
	{
		$entry = $this->data[spl_object_hash($column)];
		$column->parseInto($info, $entry);
	}
}

