<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Formatter_ValueFormatter_Datetime extends Search_Formatter_ValueFormatter_Abstract
{
	protected $format;

	function __construct()
	{
		$tikilib = TikiLib::lib('tiki');
		$this->format = $tikilib->get_short_datetime_format();
	}

	function render($name, $value, array $entry)
	{
		if (preg_match('/^\d{14}$/', $value)) {
			// Facing a date formated as YYYYMMDDHHIISS as indexed in lucene
			// Always stored as UTC
			$value = date_create_from_format('YmdHise', $value . 'UTC')->getTimestamp();
		}

		if (is_numeric($value)) {	// expects a unix timestamp but might be getting the default value
			$tikilib = TikiLib::lib('tiki');
			return $tikilib->date_format($this->format, $value);
		} else {
			return $value;
		}
	}
}

