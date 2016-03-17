<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Event_Customizer_Executor implements Tiki_Event_EdgeProvider
{
	private $ruleSet;
	private $runner;

	function __construct(Tiki_Event_Customizer_RuleSet $ruleSet, Math_Formula_Runner $runner)
	{
		$this->ruleSet = $ruleSet;
		$this->runner = $runner;
	}

	function __invoke($arguments, $eventName, $priority)
	{
		$rules = $this->ruleSet->getRules();
		$runner = $this->runner;

		$runner->setVariables(
			array(
				'args' => $arguments,
				'event' => $eventName,
				'priority' => $priority,
			)
		);

		foreach ($rules as $rule) {
			try {
				$runner->setFormula($rule);
				$runner->evaluate();
			} catch (Math_Formula_Exception $e) {
				// Skip errors
			}
		}
	}

	function getTargetEvents()
	{
		$out = array();

		foreach ($this->ruleSet->getRules() as $rule) {
			$out = array_merge($out, $this->findTrigger($rule));
		}

		return $out;
	}

	private function findTrigger($element)
	{
		if ($element->getType() == 'event-trigger') {
			return array($element[0]);
		} else {
			$out = array();
			foreach ($element as $child) {
				if ($child instanceof Math_Formula_Element) {
					$out = array_merge($out, $this->findTrigger($child));
				}
			}

			return $out;
		}
	}
}

