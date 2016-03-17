<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Event_Customizer
{
	private $ruleSets = array();

	function addRule($eventName, $function)
	{
		$this->getRuleSet($eventName)->addRule($function);
	}

	function bind(Tiki_Event_Manager $manager, Math_Formula_Runner $runner)
	{
		foreach ($this->ruleSets as $eventName => $ruleSet) {
			$manager->bind($eventName, new Tiki_Event_Customizer_Executor($ruleSet, $runner));
		}
	}

	private function getRuleSet($eventName)
	{
		if (! isset($this->ruleSets[$eventName])) {
			$this->ruleSets[$eventName] = new Tiki_Event_Customizer_RuleSet;
		}

		return $this->ruleSets[$eventName];
	}
}

