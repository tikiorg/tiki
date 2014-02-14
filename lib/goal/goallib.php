<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class GoalLib
{
	static $runner;

	function listGoals()
	{
		$table = $this->table();

		$list = $table->fetchAll(['goalId', 'enabled', 'name', 'description', 'type', 'eligible'], [], -1, -1, [
			'name' => 'ASC',
		]);

		return array_map(function ($goal) {
			$goal['eligible'] = json_decode($goal['eligible'], true);
			return $goal;
		}, $list);
	}

	function removeGoal($goalId)
	{
		$this->table()->delete(['goalId' => $goalId]);
	}

	function replaceGoal($goalId, array $data)
	{
		$data = array_merge([
			'name' => 'No name',
			'description' => '',
			'type' => 'user',
			'enabled' => 0,
			'daySpan' => 14,
			'from' => null,
			'to' => null,
			'eligible' => [],
			'conditions' => [],
			'rewards' => [],
		], $data);

		$data['eligible'] = json_encode((array) $data['eligible']);
		$data['conditions'] = json_encode((array) $data['conditions']);
		$data['rewards'] = json_encode((array) $data['rewards']);

		if ($goalId) {
			$this->table()->update($data, ['goalId' => $goalId]);
			return $goalId;
		} else {
			return $this->table()->insert($data);
		}
	}

	function fetchGoal($goalId)
	{
		$goal = $this->table()->fetchFullRow(['goalId' => $goalId]);

		if ($goal) {
			$goal['eligible'] = json_decode($goal['eligible'], true) ?: [];
			$goal['conditions'] = json_decode($goal['conditions'], true) ?: [];
			$goal['rewards'] = json_decode($goal['rewards'], true) ?: [];

			return $goal;
		}
	}

	function isEligible(array $goal, array $context)
	{
		if ($goal['type'] == 'user') {
			return count(array_intersect($context['groups'], $goal['eligible'])) > 0;
		} elseif ($context['group']) {
			return in_array($context['group'], $goal['eligible']);
		} else {
			return false;
		}
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

		if ($goal['type'] == 'user') {
			$metric = str_replace('(filter-target)', '(filter (content user) (field "user"))', $metric);
		} else {
			$metric = str_replace('(filter-target)', '(filter (multivalue group) (field "goal_groups"))', $metric);
		}

		return $metric;
	}
	
	private function table()
	{
		return TikiDb::get()->table('tiki_goals');
	}
}

