<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Provides for logging of events via the Web Server log
 * via a HTTP response header. Need to be activated via setting
 * up TIKI_HEADER_REPORT_EVENTS as a server environment variable
 */
class Tiki_Event_Function_EventLog extends Math_Formula_Function
{
	private $recorder;

	function __construct($recorder)
	{
		$this->recorder = $recorder;
	}

	function evaluate( $element )
	{
		$event = $this->evaluateChild($element[0]);
		$arguments = $this->evaluateChild($element[1]);

		$includes = array();
		$excludes = array();
		$includes_or_excludes = array();
		if (isset($element[2])) {
			$includes_or_excludes = explode("&", $element[2]);
		} 
		foreach ($includes_or_excludes as $rule) {
			if (substr($rule, 0, 1) == '+') {
				$includes[] = substr($rule, 1);
			} else if (substr($rule, 0, 1) == '-') {
				$excludes[] = substr($rule, 1);
			}
		}	

		$this->recorder->logEvent($event, $arguments, $includes, $excludes);

		return 1;
	}
}

