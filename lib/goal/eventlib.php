<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
		foreach ($this->getGoalEvents() as $eventType) {
			$manager->bind($eventType, function ($args, $eventName) use ($eventType) {
				if (isset($args['user'])) {
					$tikilib = TikiLib::lib('tiki');

					$user = $args['user'];

					if ($eventName == 'tiki.goal.reached') {
						$groups = $args['group'] ? [$args['group']] : [];
					} else {
						$groups = $tikilib->get_user_groups($user);
					}

					$data = [
						'eventType' => $eventType,
						'eventDate' => $tikilib->now,
						'user' => $user,
						'groups' => json_encode($groups),
					];

					if (! empty($args['type']) && ! empty($args['object'])) {
						$data['targetType'] = $args['type'];
						$data['targetObject'] = $args['object'];
					}

					$id = $this->table()->insert($data);

					TikiLib::lib('unifiedsearch')->invalidateObject('goalevent', $id);
				}
			});
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
					$list[] = $condition['eventType'];

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

