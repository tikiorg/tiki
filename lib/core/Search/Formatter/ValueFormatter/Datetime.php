<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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

		$tikilib = TikiLib::lib('tiki');

		// indexed datetime value is always UTC, so use correct timezone when converting back to timestamp
		$old_tz = date_default_timezone_get();
		date_default_timezone_set('UTC');
		$time = strtotime($value);
		date_default_timezone_set($old_tz);

		if (is_numeric($value)) {	// expects a unix timestamp but might be getting the default value
			return $tikilib->date_format($this->format, $value);
		} elseif (false !== $time) {
			return $tikilib->date_format($this->format, $time);
		} else {
			return $value;
		}
	}
}

