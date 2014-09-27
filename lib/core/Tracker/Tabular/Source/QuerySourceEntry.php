<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
		$textKey = 'tracker_field_' . $field . '_text';
		$extra = [
			'itemId' => $this->data['object_id'],
			'status' => $this->data['tracker_status'],
		];

		$value = $this->data[$key];
		if (isset($this->data[$textKey])) {
			$extra['text'] = $this->data[$textKey];
		}

		return $column->render($value, $extra);
	}
}

