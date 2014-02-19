<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Goal_Controller
{
	function setUp()
	{
		Services_Exception_Disabled::check('goal_enabled');
		Services_Exception_Denied::checkAuth();
	}

	function action_show($input)
	{
		global $user;
		$goallib = TikiLib::lib('goal');
		$info = $goallib->fetchGoal($input->goalId->int());

		if (! $info) {
			throw new Services_Exception_NotFound;
		}

		if (! $info['enabled']) {
			throw new Services_Exception_Denied(tr('Goal currently disabled'));
		}

		$context = [
			'user' => $user,
			'group' => $input->group->groupname(),
			'groups' => Perms::get()->getGroups(),
		];

		if ($info['type'] == 'group' && ! $context['group']) {
			return [
				'FORWARD' => [
					'controller' => 'goal',
					'action' => 'show_list',
					'goalId' => $info['goalId'],
				],
			];
		}

		if (! $goallib->isEligible($info, $context)) {
			throw new Services_Exception_Denied(tr('Not eligible for this goal'));
		}

		$info = $goallib->evaluateConditions($info, $context);

		return array(
			'title' => $info['name'],
			'goal' => $info,
		);
	}

	function action_show_list($input)
	{
		$goallib = TikiLib::lib('goal');
		$info = $goallib->fetchGoal($input->goalId->int());

		if (! $info) {
			throw new Services_Exception_NotFound;
		}
		
		return [
			'title' => $info['name'],
			'goal' => $info,
		];
	}

	function action_admin($input)
	{
		$perms = Perms::get();
		if (! $perms->admin) {
			throw new Services_Exception_Denied(tr('Reserved to administrators'));
		}

		$goallib = TikiLib::lib('goal');

		$goals = $goallib->listGoals();

		return [
			'title' => tr('Manage Goals'),
			'list' => $goals,
		];
	}

	function action_create($input)
	{
		$perms = Perms::get();
		if (! $perms->admin) {
			throw new Services_Exception_Denied(tr('Reserved to administrators'));
		}

		$name = $input->name->text();
		$description = $input->description->text();

		if ($_SERVER['REQUEST_METHOD'] == 'POST' && $name) {
			$goallib = TikiLib::lib('goal');
			$id = $goallib->replaceGoal(0, array(
				'name' => $name,
				'description' => $description,
				'type' => 'user',
			));

			return [
				'FORWARD' => [
					'controller' => 'goal',
					'action' => 'edit',
					'goalId' => $id,
				],
			];
		}

		return [
			'title' => tr('Create Goal'),
			'name' => $name,
			'description' => $description,
			'type' => $type,
		];
	}

	function action_edit($input)
	{
		$perms = Perms::get();
		if (! $perms->admin) {
			throw new Services_Exception_Denied(tr('Reserved to administrators'));
		}

		$goallib = TikiLib::lib('goal');
		$goal = $goallib->fetchGoal($input->goalId->int());

		if (! $goal) {
			throw new Services_Exception_NotFound;
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$type = $input->type->alpha();

			$goal['name'] = $input->name->text();
			$goal['description'] = $input->description->text();
			$goal['enabled'] = $input->enabled->int();
			$goal['eligible'] = $input->eligible->groupname();

			if (in_array($type, ['user', 'group'])) {
				$goal['type'] = $type;
			}

			$rangeType = $input->range_type->word();
			$daySpan = $input->daySpan->int();
			$from = $input->from->isodatetime();
			$to = $input->to->isodatetime();

			if ($rangeType == 'rolling' && $daySpan) {
				$goal['daySpan'] = $daySpan;
				$goal['from'] = null;
				$goal['to'] = null;
			} elseif ($rangeType == 'fixed' && $from && $to) {
				$goal['daySpan'] = 0;
				$goal['from'] = $from;
				$goal['to'] = $to;
			}

			$conditions = json_decode($input->conditions->none(), true);
			if (is_array($conditions)) {
				// Basic validation to make sure we have json
				$goal['conditions'] = $conditions;
			}

			$goallib->replaceGoal($input->goalId->int(), $goal);
		}

		return [
			'title' => tr('Edit Goal'),
			'goal' => $goal,
			'groups' => TikiLib::lib('user')->list_all_groups(),
		];
	}

	function action_delete($input)
	{
		$perms = Perms::get();
		if (! $perms->admin) {
			throw new Services_Exception_Denied(tr('Reserved to administrators'));
		}

		$goallib = TikiLib::lib('goal');
		$goal = $goallib->fetchGoal($input->goalId->int());

		if (! $goal) {
			throw new Services_Exception_NotFound;
		}

		$removed = false;
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$goallib->removeGoal($input->goalId->int());
			$removed = true;
		}

		return [
			'title' => tr('Remove Goal'),
			'removed' => $removed,
			'goal' => $goal,
			'groups' => TikiLib::lib('user')->list_all_groups(),
		];
	}

	/**
	 * Action is completely stateless. Renders the provided data.
	 */
	function action_render_conditions($input)
	{
		$perms = Perms::get();
		if (! $perms->admin) {
			throw new Services_Exception_Denied(tr('Reserved to administrators'));
		}

		$conditions = json_decode($input->conditions->none(), true);

		if (! is_array($conditions)) {
			throw new Services_Exception_MissingValue('conditions');
		}

		return [
			'title' => tr('Conditions'),
			'conditions' => array_filter($conditions),
		];
	}

	/**
	 * Action is completely stateless. Pass in parameters, get updated parameters.
	 */
	function action_edit_condition($input)
	{
		$condition = [
			'label' => tr('Pages created'),
			'operator' => 'atLeast',
			'count' => 5,
			'metric' => 'event-count',
			'eventType' => 'tiki.wiki.create',
			'hidden' => 0,
		];

		$metricList = TikiLib::lib('goal')->getMetricList();

		$operator = $input->operator->word();
		if (! in_array($operator, ['atLeast', 'atMost'])) {
			$operator = null;
		}

		$metric = $input->metric->text();
		if (! isset($metricList[$metric])) {
			$metric = null;
		}

		$condition['label'] = $input->label->text() ?: $condition['label'];
		$condition['count'] = isset($input['count']) ? $input->count->int() : $condition['count'];
		$condition['operator'] = $operator ?: $condition['operator'];
		$condition['metric'] = $metric ?: $condition['metric'];
		$condition['hidden'] = $input->hidden->int();

		$condition['eventType'] = $input->eventType->attribute_type() ?: $condition['eventType'];

		return [
			'title' => tr('Condition'),
			'condition' => $condition,
			'metrics' => $metricList,
		];
	}
}

