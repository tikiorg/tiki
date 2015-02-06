<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Event_Function_EventRecord extends Math_Formula_Function
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

		$this->recorder->recordEvent($event, $arguments);

		return 1;
	}
}

