<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Event_Manager
{
	private $eventRegistry = array();

	function bind($eventName, $callback, array $arguments = array())
	{
		if (! is_callable($callback)) {
			$callback = array(new Event_Chain($this, $callback), 'trigger');
		}

		$this->eventRegistry[$eventName][] = array(
			'callback' => $callback,
			'arguments' => $arguments,
		);
	}

	function trigger($eventName, array $arguments = array())
	{
		if (isset ($this->eventRegistry[$eventName])) {
			foreach ($this->eventRegistry[$eventName] as $callback) {
				call_user_func($callback['callback'], array_merge(
					$callback['arguments'],
					$arguments
				));
			}
		}
	}
}

