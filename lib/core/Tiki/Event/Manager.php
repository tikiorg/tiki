<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Event_Manager
{
	private $eventRegistry = array();
	private $priorities = array();
	private $currentPriority = false;
	private $counter = 0;

	function reset()
	{
		$this->eventRegistry = array();
		$this->priorities = array();
	}

	/**
	 * Binds an event at normal priority and handles event chaining.
	 */
	function bind($eventName, $callback, array $arguments = array())
	{
		$priority = 0;

		if (! is_callable($callback)) {
			$callback = new Tiki_Event_Chain($this, $callback);
			$priority = false;
		}

		$this->bindPriority($priority, $eventName, $callback, $arguments);
	}

	/**
	 * Bind the event at a specific priority. Allows some event to be forced to execute after others. For example,
	 * normal priorities may alter data, but indexing should not happen until all data has been modified.
	 *
	 * Priorities are numeric, false indicates that the event executes at all levels. This is used for chaining
	 * and happens transparently when using bind() with an event as the callback.
	 */
	function bindPriority($priority, $eventName, $callback, array $arguments = array())
	{
		if ($priority !== false) {
			$this->priorities[] = $priority;
		}

		$this->eventRegistry[$eventName][] = array(
			'priority' => $priority,
			'callback' => $callback,
			'arguments' => $arguments,
		);
	}

	function trigger($eventName, array $arguments = array())
	{
		$arguments['EVENT_ID'] = ++$this->counter;

		$priorities = array_unique($this->priorities);
		sort($priorities);
		$this->priorities = $priorities;

		foreach ($priorities as $p) {
			$this->internalTrigger($eventName, $arguments, $p, $eventName);
		}
	}

	function internalTrigger($eventName, array $arguments, $priority, $originalEvent)
	{
		if (isset ($this->eventRegistry[$eventName])) {
			foreach ($this->eventRegistry[$eventName] as $callback) {
				if ($callback['priority'] === false || $callback['priority'] === $priority) {
					call_user_func(
						$callback['callback'], 
						array_merge(
							$callback['arguments'],
							$arguments
						),
						$originalEvent,
						$priority
					);
				}
			}
		}
	}

	function getEventGraph()
	{
		$edges = array();
		$nodes = array_keys($this->eventRegistry);

		foreach ($this->eventRegistry as $from => $callbackList) {
			foreach ($callbackList as $callback) {
				if ($callback['callback'] instanceof Tiki_Event_EdgeProvider) {
					foreach ($callback['callback']->getTargetEvents() as $eventName) {
						$edges[] = array(
							'from' => $from,
							'to' => $eventName,
						);
						$nodes[] = $eventName;
					}
				}
			}
		}

		return array(
			'nodes' => array_values(array_unique($nodes)),
			'edges' => $edges,
		);
	}
}

