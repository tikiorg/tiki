<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Math_Formula_Function_StrToTime extends Math_Formula_Function
{
	function evaluate( $args )
	{
		$elements = array();

		if (count($args) > 2) {
			$this->error(tr('Too many arguments on strtotime.'));
		}

		if (count($args) < 1) {
			$this->error(tr('Too few arguments on strtotime.'));
		}

		foreach ( $args as $child ) {
			$elements[] = $this->evaluateChild($child);
		}

		$tikilib = TikiLib::lib('tiki');
		$tz = $tikilib->get_display_timezone();
		$oldTz = date_default_timezone_get();
		if( $tz ) {
			date_default_timezone_set($tz);
		}

		$time = array_shift($elements);
		$now = intval(array_shift($elements));
		if (empty($now)) {
			$now = time();	// Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)
		}
		
		$newTime = strtotime( $time, $now );

		date_default_timezone_set($oldTz);

		return $newTime;
	}
}
