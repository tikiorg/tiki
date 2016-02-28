<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tracker\Tabular\Source;

class QuerySourceEntry
{
	private $data;

	function __construct($data)
	{
		$this->data = $data;
	}

	function render(\Tracker\Tabular\Schema\Column $column)
	{
		$field = $column->getField();
		$key = 'tracker_field_' . $field;

		if (isset($this->data[$key])) {
			$value = $this->data[$key];
		} else {
			$value = null;
		}

		$extra = [];
		foreach ($column->getQuerySources() as $target => $field) {
			if (isset($this->data[$field])) {
				$extra[$target] = $this->data[$field];
			}
		}

		return $column->render($value, $extra);
	}
}

