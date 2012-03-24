<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Formatter_ValueFormatter_Datetime extends Search_Formatter_ValueFormatter_Abstract
{
	function render($name, $value, array $entry)
	{
		global $prefs, $tikilib;
		if (is_numeric($value)) {	// expects a unix timestamp but might be getting the default value
			return $tikilib->date_format($tikilib->get_short_datetime_format(), $value);
		} else {
			return $value;
		}
	}
}

