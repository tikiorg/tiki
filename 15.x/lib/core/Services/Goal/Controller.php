<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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

		$messages = [];
		$perms = Perms::get('goal', $input->goalId->int());
		$isAdmin = $perms->goal_admin;

		if (! $info['enabled']) {
			if (! $isAdmin) {
				throw new Services_Exception_Denied(tr('The goal is currently disabled'));
			} else {
				$messages[] = tr('This goal is not enabled.');
			}
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

		if ($info['enabled'] && $goallib->isEligible($info, $context)) {
			$info = $goallib->evaluateConditions($info, $context);
		} else {
			if (! $isAdmin) {
				throw new Services_Exception_Denied(tr('Not eligible for this goal'));
			}

			// Goal is only visible because user is admin, mock some of the data
			$info = $goallib->unevaluateConditions($info);
			$messages[] = tr('The goal has not been evaluated, administrator view.');
		}

		$info['conditions'] = array_filter($info['conditions'], function ($item) {
			return empty($item['hidden']);
		});
		$info['rewards'] = array_filter($info['rewards'], function ($item) {
			return empty($item['hidden']);
		});

		return array(
			'title' => $info['name'],
			'goal' => $info,
			'messages' => $messages,
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
		if (! $perms->goal_admin) {
			throw new Services_Exception_Denied(tr('Reserved for administrators'));
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
		if (! $perms->goal_admin) {
			throw new Services_Exception_Denied(tr('Reserved for administrators'));
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
		$perms = Perms::get('goal', $input->goalId->int());
		if (! $perms->goal_admin) {
			throw new Services_Exception_Denied(tr('Reserved for administrators'));
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

			$rewards = json_decode($input->rewards->none(), true);
			if (is_array($rewards)) {
				// Basic validation to make sure we have json
				$goal['rewards'] = $rewards;
			}

			$goallib->replaceGoal($input->goalId->int(), $goal);

			return [
				'FORWARD' => [
					'controller' => 'goal',
					'action' => 'admin',
				],
			];
		}

		return [
			'title' => tr('Edit Goal'),
			'goal' => $goal,
			'groups' => $goallib->listEligibleGroups(),
		];
	}

	function action_delete($input)
	{
		$perms = Perms::get('goal', $input->goalId->int());
		if (! $perms->goal_admin) {
			throw new Services_Exception_Denied(tr('Reserved for administrators'));
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
			'groups' => $goallib->listEligibleGroups(),
		];
	}

	/**
	 * Action is completely stateless. Renders the provided data.
	 */
	function action_render_conditions($input)
	{
		// No need to check permissions, no actions possible

		$conditions = json_decode($input->conditions->none(), true);

		if (! is_array($conditions)) {
			throw new Services_Exception_MissingValue('conditions');
		}

		return [
			'title' => tr('Conditions'),
			'conditions' => array_values(array_filter($conditions)),
		];
	}

	/**
	 * Action is completely stateless. Pass in parameters, get updated parameters.
	 */
	function action_edit_condition($input)
	{
		// No need to check permissions, no actions possible

		$condition = [
			'label' => tr('Pages created'),
			'operator' => 'atLeast',
			'count' => 5,
			'metric' => 'event-count',
			'eventType' => 'tiki.wiki.create',
			'hidden' => 0,
			'trackerItemBadge' => 0,
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
		$condition['trackerItemBadge'] = $this->getTrackerItemBadge($input);

		return [
			'title' => tr('Condition'),
			'condition' => $condition,
			'metrics' => $metricList,
		];
	}

	/**
	 * Action is completely stateless. Renders the provided data.
	 */
	function action_render_rewards($input)
	{
		// No need to check permissions, no actions possible

		$rewards = json_decode($input->rewards->none(), true);

		if (! is_array($rewards)) {
			throw new Services_Exception_MissingValue('rewards');
		}

		return [
			'title' => tr('Rewards'),
			'rewards' => array_values(array_filter($rewards)),
		];
	}

	/**
	 * Action is completely stateless. Pass in parameters, get updated parameters.
	 */
	function action_edit_reward($input)
	{
		// No need to check permissions, no actions possible

		$rewardList = TikiLib::lib('goalreward')->getRewardList();

		if (empty($rewardList)) {
			throw new Services_Exception_NotAvailable(tr('No available rewards'));
		}

		$reward = [
			'label' => tr('Pages created'),
			'rewardType' => key($rewardList),
			'creditType' => null,
			'creditQuantity' => 1,
			'hidden' => 0,
			'trackerItemBadge' => 0,
		];

		$rewardType = $input->rewardType->text();
		if (! isset($rewardList[$rewardType])) {
			$rewardType = key($rewardList);
		}

		$reward['rewardType'] = $rewardType;
		$reward['hidden'] = $input->hidden->int();

		$reward['creditType'] = $input->creditType->word();
		$reward['creditQuantity'] = isset($input['creditQuantity']) ? $input->creditQuantity->int() : $reward['creditQuantity'];

		$reward['trackerItemBadge'] = $this->getTrackerItemBadge($input);

		$reward['eventType'] = $input->eventType->attribute_type() ?: $reward['eventType'];

		$f = $rewardList[$reward['rewardType']]['format'];
		$reward['label'] = $f($reward);

		return [
			'title' => tr('Reward'),
			'reward' => $reward,
			'rewards' => $rewardList,
		];
	}

	function action_edit_eligible($input)
	{
		$perms = Perms::get('goal', $input->goalId->int());
		if (! $perms->goal_modify_eligible) {
			throw new Services_Exception_Denied(tr('Restricted access'));
		}

		$goalId = $input->goalId->int();

		$goallib = TikiLib::lib('goal');
		$goal = $goallib->fetchGoal($goalId);

		if (! $goal) {
			throw new Services_Exception_NotFound;
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$goal = [
				'eligible' => $input->eligible->groupname(),
			];

			$goallib->replaceGoal($goalId, $goal);

			return [
				'FORWARD' => [
					'controller' => 'goal',
					'action' => 'show',
					'goalId' => $goalId,
				],
			];
		}

		return [
			'title' => tr('Modify Eligibility for %0', $goal['name']),
			'goal' => $goal,
			'groups' => $goallib->listEligibleGroups(),
		];
	}

	private function getTrackerItemBadge($input)
	{
		if ($badge = $input->trackerItemBadge->int()) {
			return $badge;
		} elseif ($object = $input->trackerItemBadge->none()) {
			list($type, $id) = explode(':', $object, 2);
			if ($type == 'trackeritem' && intval($id)) {
				return intval($id);
			}
		}

		return 0;
	}
}

