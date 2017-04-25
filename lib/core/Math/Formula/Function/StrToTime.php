<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Math_Formula_Function_StrToTime extends Math_Formula_Function
{
	function evaluate( $element )
	{
		$elements = array();

		if (count($element) > 2) {
			$this->error(tr('Too many arguments on strtotime.'));
		}

		if (count($element) < 1) {
			$this->error(tr('Too few arguments on strtotime.'));
		}

		foreach ( $element as $child ) {
			$elements[] = $this->evaluateChild($child);
		}

		$tikilib = TikiLib::lib('tiki');
		$tz = $tikilib->get_display_timezone();
		$old_tz = date_default_timezone_get();
		if( $tz )
			date_default_timezone_set($tz);

		$time = array_shift($elements);
		$now = intval(array_shift($elements));
		if (empty($now)) {
			$now = time();	// Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)
		}
		
		$new_time = strtotime( $time, $now );

		date_default_timezone_set($old_tz);

		return $new_time;
	}
}

