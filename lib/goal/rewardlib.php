<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class GoalRewardLib
{
	function getRewardList()
	{
		global $prefs;

		$creditTypes = $this->getCreditTypes();

		$list = [];

		if (! empty($prefs['goal_badge_tracker'])) {
			$list['tracker_badge_add'] = [
				'label' => tr('Add One-Time Badge'),
				'arguments' => ['trackerItemBadge'],
				'tracker' => $prefs['goal_badge_tracker'],
				'format' => function ($info) {
					return tr('%0 Badge', TikiLib::lib('object')->get_title('trackeritem', $info['trackerItemBadge']) ?: tr('Unknown'));
				},
				'applyUser' => function ($user, $reward) {
					$this->giveBadge($reward, 'user', $user);
				},
				'applyGroup' => function ($group, $reward) {
					$this->giveBadge($reward, 'group', $group);
				},
			];
			$list['tracker_badge_remove'] = [
				'label' => tr('Remove One-Time Badge'),
				'arguments' => ['trackerItemBadge'],
				'tracker' => $prefs['goal_badge_tracker'],
				'format' => function ($info) {
					return tr('%0 Badge (Remove)', TikiLib::lib('object')->get_title('trackeritem', $info['trackerItemBadge']) ?: tr('Unknown'));
				},
				'applyUser' => function ($user, $reward) {
					$this->removeBadge($reward, 'user', $user);
				},
				'applyGroup' => function ($group, $reward) {
					$this->removeBadge($reward, 'group', $group);
				},
			];
		}

		if (! empty($creditTypes)) {
			$list['credit'] = [
				'label' => tr('Credits'),
				'arguments' => ['creditType', 'creditQuantity'],
				'options' => $creditTypes,
				'format' => function ($info) use ($creditTypes) {
					if (! empty($creditTypes[$info['creditType']])) {
						return tr('%0 credit(s) - %1', $info['creditQuantity'], $creditTypes[$info['creditType']]);
					} else {
						return tr('Unknown credit type');
					}
				},
				'applyUser' => function ($user, $reward) {
					if (! empty($creditTypes[$reward['creditType']])) {
						$userId = TikiLib::lib('tiki')->get_user_id($user);
						$lib = TikiLib::lib('credits');
						$lib->addCredits($userId, $reward['creditType'], $reward['creditQuantity']);
					}
				},
				'applyGroup' => function ($group, $reward) {
					// Groups can't have credits
				},
			];
		}

		return $list;
	}

	private function getCreditTypes()
	{
		global $prefs;
		if ($prefs['feature_credits'] != 'y') {
			return [];
		}

		$lib = TikiLib::lib('credits');
		$types = $lib->getCreditTypes();

		$out = [];
		foreach ($types as $type) {
			$out[$type['credit_type']] = $type['display_text'];
		}

		return $out;
	}

	function giveRewardsToUser($user, $rewards, $list = null)
	{
		if (! $list) {
			$list = $this->getRewardList();
		}

		foreach ($rewards as $reward) {
			$type = $reward['rewardType'];
			$f = $list[$type]['applyUser']($user, $reward);
		}
	}

	function giveRewardsToMembers($group, $rewards)
	{
		$list = $this->getRewardList();

		foreach ($rewards as $reward) {
			$type = $reward['rewardType'];
			$f = $list[$type]['applyGroup']($group, $reward);
		}

		$lib = TikiLib::lib('user');
		$users = $lib->get_group_users($group);

		foreach ($users as $user) {
			$this->giveRewardsToUser($user, $rewards, $list);
		}
	}

	private function giveBadge($reward, $type, $object)
	{
		if ($reward['trackerItemBadge']) {
			TikiLib::lib('relation')->add_relation('tiki.badge.received', $type, $object, 'trackeritem', $reward['trackerItemBadge']);

			$search = TikiLib::lib('unifiedsearch');
			$search->invalidateObject($type, $object);
			$search->invalidateObject('trackeritem', $reward['trackerItemBadge']);
		}
	}

	private function removeBadge($reward, $type, $object)
	{
		if ($reward['trackerItemBadge']) {
			$lib = TikiLib::lib('relation');
			if ($relation = $lib->get_relation_id('tiki.badge.received', $type, $object, 'trackeritem', $reward['trackerItemBadge'])) {
				$lib->remove_relation($relation);
				$search = TikiLib::lib('unifiedsearch');
				$search->invalidateObject($type, $object);
				$search->invalidateObject('trackeritem', $reward['trackerItemBadge']);
			}
		}
	}
}

