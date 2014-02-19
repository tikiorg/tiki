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

	function listConditions()
	{
		$table = $this->table();

		$list = $table->fetchAll(['goalId', 'conditions'], [], -1, -1, [
		]);

		return array_map(function ($goal) {
			$goal['conditions'] = json_decode($goal['conditions'], true);
			return $goal;
		}, $list);
	}

	function removeGoal($goalId)
	{
		$this->table()->delete(['goalId' => $goalId]);

		TikiLib::lib('goalevent')->touch();
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
			'conditions' => [
				[
					'label' => tr('Goal achieved'),
					'operator' => 'atMost',
					'count' => 0,
					'metric' => 'goal-count-unbounded',
					'hidden' => 1,
				],
			],
			'rewards' => [],
		], $data);

		$data['eligible'] = json_encode((array) $data['eligible']);
		$data['conditions'] = json_encode((array) $data['conditions']);
		$data['rewards'] = json_encode((array) $data['rewards']);

		if ($goalId) {
			$this->table()->update($data, ['goalId' => $goalId]);
		} else {
			$goalId = $this->table()->insert($data);
		}

		TikiLib::lib('goalevent')->touch();

		return $goalId;
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
			$arguments = [];
			foreach (['eventType'] as $arg) {
				if (isset($cond[$arg])) {
					$arguments[$arg] = $cond[$arg];
				}
			}

			$runner->setFormula($cond['metric']);
			$runner->setVariables(array_merge($goal, $context, $arguments));
			$cond['metric'] = $runner->evaluate();

			if ($cond['operator'] == 'atLeast') {
				$cond['complete'] = $cond['metric'] >= $cond['count'];
				$cond['metric'] = min($cond['count'], $cond['metric']);
			} else {
				$cond['complete'] = $cond['metric'] <= $cond['count'];
			}

			$goal['complete'] = $goal['complete'] && $cond['complete'];
		}

		if ($goal['complete']) {
			$tx = TikiDb::get()->begin();

			TikiLib::events()->trigger('tiki.goal.reached', [
				'type' => 'goal',
				'object' => $goal['goalId'],
				'name' => $goal['name'],
				'goalType' => $goal['type'],
				'user' => $context['user'],
				'group' => $context['group'],
			]);

			if ($goal['type'] == 'group') {
				$this->giveRewardsToMembers($context['group'], $goal['rewards']);
			} else {
				$this->giveRewardsToUser($context['user'], $goal['rewards']);
			}

			$tx->commit();
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
				(filter (type "goalevent"))
			)';
			break;
		case 'event-count-unbounded':
			$metric = '(result-count
				(filter-target)
				(filter (content eventType) (field "event_type"))
				(filter (type "goalevent"))
			)';
			break;
		case 'goal-count':
			$metric = '(result-count
				(filter-date)
				(filter-target)
				(filter (content "tiki.goal.reached") (field "event_type"))
				(filter (type "goalevent"))
				(filter (content (concat "goal:" goalId)) (field "target"))
			)';
			break;
		case 'goal-count-unbounded':
			$metric = '(result-count
				(filter-target)
				(filter (content "tiki.goal.reached") (field "event_type"))
				(filter (type "goalevent"))
				(filter (content (concat "goal:" goalId)) (field "target"))
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

	function getMetricList()
	{
		return [
			'event-count' => ['label' => tr('Event Count'), 'arguments' => ['eventType']],
			'event-count-unbounded' => ['label' => tr('Event Count (Forever)'), 'arguments' => ['eventType']],
			'goal-count' => ['label' => tr('Goal Reached (Periodic)'), 'arguments' => []],
			'goal-count-unbounded' => ['label' => tr('Goal Reached (Forever)'), 'arguments' => []],
		];
	}

	function getRewardList()
	{
		return [
			'credit' => [
				'label' => tr('Credits'),
				'arguments' => ['creditType', 'creditQuantity'],
				'format' => function ($info) {
					return tr('%0 credit(s) - %1', $info['creditQuantity'], $info['creditType']);
				},
				'apply' => function ($user, $reward) {
					$userId = TikiLib::lib('tiki')->get_user_id($user);
					$lib = TikiLib::lib('credits');
					$lib->addCredits($userId, $reward['creditType'], $reward['creditQuantity']);
				},
			],
		];
	}

	private function table()
	{
		return TikiDb::get()->table('tiki_goals');
	}

	private function giveRewardsToUser($user, $rewards)
	{
		$list = $this->getRewardList();

		foreach ($rewards as $reward) {
			$type = $reward['rewardType'];
			$f = $list[$type]['apply'];
			$f($user, $reward);
		}
	}

	private function giveRewardsToMembers($group, $rewards)
	{
		$lib = TikiLib::lib('user');
		$users = $lib->get_group_users($group);

		foreach ($users as $user) {
			$this->giveRewardsToUser($user, $rewards);
		}
	}
}

