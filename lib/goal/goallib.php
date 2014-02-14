<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class GoalLib
{
	static $runner;

	function fetchGoal($goalId)
	{
		// TODO : Create table, connect to database
		return [
			'name' => 'Simple Contributor',
			'description' => 'Modify 5 wiki pages within 14 days',
			'daySpan' => 14,
			'from' => null,
			'to' => null,
			'type' => 'user',
			'conditions' => [
				['label' => 'Modifications', 'operator' => 'atLeast', 'count' => 5, 'metric' => '(result-count
					(filter (range (str modification_date)) (from (concat daySpan (str days ago))) (to (str now)))
					(filter (content (str tiki.wiki.update)) (field (str event_type)))
					(filter (content user) (field (str user)))
				)'],
			],
		];
	}

	function evaluateConditions(array $goal, array $context)
	{
		$this->prepareConditions($goal);
		$runner = $this->getRunner();

		foreach ($goal['conditions'] as & $cond) {
			$runner->setFormula($cond['metric']);
			$runner->setVariables(array_merge($goal, $context));
			$cond['metric'] = min($cond['count'], $runner->evaluate());
		}

		return $goal;
	}

	private function prepareConditions(array & $goal)
	{
		$runner = $this->getRunner();

		foreach ($goal['conditions'] as & $cond) {
			$cond['metric'] = $runner->setFormula($cond['metric']);
		}
	}

	public static function getRunner()
	{
		if (! self::$runner) {
			self::$runner = new Math_Formula_Runner(
				array(
					'Math_Formula_Function_' => '',
					'Tiki_Formula_Function_' => '',
				)
			);
		}

		return self::$runner;
	}
}

