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
			$manager->bind($eventType, function ($args) use ($eventType) {
				if (isset($args['user'])) {
					$tikilib = TikiLib::lib('tiki');

					$user = $args['user'];
					$groups = $tikilib->get_user_groups($user);

					$id = $this->table()->insert([
						'eventType' => $eventType,
						'eventDate' => $tikilib->now,
						'user' => $user,
						'groups' => json_encode($groups),
					]);

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

