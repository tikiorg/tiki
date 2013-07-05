<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Event_Customizer_RuleSet
{
	private $parser;
	private $rules = array();

	function __construct()
	{
		$this->parser = new Math_Formula_Parser;
	}

	function addRule($function)
	{
		$this->rules[] = $this->parser->parse($function);
	}

	function compile(Math_Formula_Runner $runner)
	{
		$rules = $this->rules;
		return function ($arguments, $eventName, $priority) use ($rules, $runner) {
			$runner->setVariables(array(
				'args' => $arguments,
				'event' => $eventName,
				'priority' => $priority,
			));
			foreach ($rules as $rule) {
				$runner->setFormula($rule);
				$runner->evaluate();
			}
		};
	}
}

