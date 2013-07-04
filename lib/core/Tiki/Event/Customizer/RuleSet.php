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

	function compile(Tiki_Event_Manager $manager)
	{
		$rules = $this->rules;
		$runner = new Math_Formula_Runner(
			array(
				function ($eventName) use ($manager) {
					if ($eventName == 'event-trigger') {
						return new Tiki_Event_Function_EventTrigger($manager);
					}
				},
				'Math_Formula_Function_' => '',
				'Tiki_Event_Function_' => '',
			)
		);

		return function ($arguments) use ($rules, $runner) {
			$runner->setVariables($arguments);
			foreach ($rules as $rule) {
				$runner->setFormula($rule);
				$runner->evaluate();
			}
		};
	}
}

