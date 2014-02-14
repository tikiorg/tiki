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
			'eligible' => ['Registered'],
			'conditions' => [
				['label' => 'Modifications', 'operator' => 'atLeast', 'count' => 5, 'metric' => 'event-count', 'arguments' => [
					'eventType' => "tiki.wiki.update",
				]],
				['label' => 'Creations', 'operator' => 'atLeast', 'count' => 2, 'metric' => 'event-count', 'arguments' => [
					'eventType' => "tiki.wiki.create",
				]],
			],
		];
	}

	function isEligible(array $goal, array $context)
	{
		return count(array_intersect($context['groups'], $goal['eligible'])) > 0;
	}

	function evaluateConditions(array $goal, array $context)
	{
		$this->prepareConditions($goal);
		$runner = $this->getRunner();

		$goal['complete'] = true;

		foreach ($goal['conditions'] as & $cond) {
			$runner->setFormula($cond['metric']);
			$runner->setVariables(array_merge($goal, $context, $cond['arguments']));
			$cond['metric'] = min($cond['count'], $runner->evaluate());

			if ($cond['operator'] == 'atLeast') {
				$cond['complete'] = $cond['metric'] >= $cond['count'];
			} else {
				$cond['complete'] = $cond['metric'] <= $cond['count'];
			}

			$goal['complete'] = $goal['complete'] && $cond['complete'];
		}

		return $goal;
	}

	private function prepareConditions(array & $goal)
	{
		$runner = $this->getRunner();

		foreach ($goal['conditions'] as & $cond) {
			$metric = $this->prepareMetric($cond['metric'], $goal);
			$cond['metric'] = $runner->setFormula($metric);
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

	private function prepareMetric($metric, $goal)
	{
		switch ($metric) {
		case 'event-count':
			$metric = '(result-count
				(filter-date)
				(filter-target)
				(filter (content eventType) (field "event_type"))
			)';
			break;
		}

		if ($goal['daySpan']) {
			$metric = str_replace('(filter-date)', '(filter (range "modification_date") (from (concat daySpan " days ago")) (to "now"))', $metric);
		} else {
			$metric = str_replace('(filter-date)', '(filter (range "modification_date") (from from) (to to))', $metric);
		}

		$metric = str_replace('(filter-target)', '(filter (content user) (field "user"))', $metric);

		return $metric;
	}
}

