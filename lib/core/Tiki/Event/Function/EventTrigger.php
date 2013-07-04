<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Event_Function_EventTrigger extends Math_Formula_Function
{
	private $manager;

	function __construct(Tiki_Event_Manager $manager)
	{
		$this->manager = $manager;
	}

	function evaluate( $element )
	{
		$arguments = array();
		$eventName = $element[0];

		if ($element->arguments) {
			foreach ($element->arguments as $argument) {
				$arguments[$argument->getType()] = $this->evaluateChild($argument[0]);
			}
		}

		$this->manager->trigger($eventName, $arguments);

		return 1;
	}
}

