<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class GoalEventLib
{
	const CACHE_KEY = 'goal_events';

	function touch()
	{
		TikiLib::lib('cache')->invalidate(self::CACHE_KEY);
	}

	function bindEvents($manager)
	{
		try {
			foreach ($this->getGoalEvents() as $eventType) {
				$manager->bind($eventType, function ($args, $eventName) use ($eventType) {
					$tikilib = TikiLib::lib('tiki');

					$user = $args['user'];
					$group = isset($args['group']) ? $args['group'] : null;

					if ($eventName == 'tiki.goal.reached') {
						$groups = $group ? [$group] : [];
					} elseif (isset($args['goalType']) && $args['goalType'] == 'user') {
						$groups = $tikilib->get_user_groups($user);
					} else {
						$groups = [$group];
					}

					$data = [
						'eventType' => $eventType,
						'eventDate' => $tikilib->now,
						'user' => $user ?: '',
						'groups' => json_encode($groups),
					];

					if (! empty($args['type']) && ! empty($args['object'])) {
						$data['targetType'] = $args['type'];
						$data['targetObject'] = $args['object'];
					}

					$id = $this->table()->insert($data);

					TikiLib::lib('unifiedsearch')->invalidateObject('goalevent', $id);
				});
			}
		} catch (TikiDb_Exception $e) {
			// Prevent failures from locking-out users
		}
	}

	private function getGoalEvents()
	{
		$cachelib = TikiLib::lib('cache');

		if (! $list = $cachelib->getSerialized(self::CACHE_KEY)) {
			$list = [];

			$goals = TikiLib::lib('goal')->listConditions();

			foreach ($goals as $goal) {
				foreach ($goal['conditions'] as $condition) {
					if (! empty($condition['eventType'])) {
						$list[] = $condition['eventType'];
					}

					if ($condition['metric'] == 'goal-count-unbounded' || $condition['metric'] == 'goal-count') {
						$list[] = 'tiki.goal.reached';
					}
				}
			}
			
			$list = array_unique($list);
			$cachelib->cacheItem(self::CACHE_KEY, serialize($list));
		}

		return $list;
	}

	private function table()
	{
		return TikiDb::get()->table('tiki_goal_events');
	}
}

