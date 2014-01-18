<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class MonitorLib
{
	private $queue = [];

	function getPriorities()
	{
		// TODO : Add more priorities
		return array(
			'none' => ['label' => ''],
			'critical' => ['label' => tr('Critical')],
		);
	}

	function getOptions($user, $type, $object)
	{
		$tikilib = TikiLib::lib('tiki');
		$userId = $tikilib->get_user_id($user);

		$events = $this->getApplicableEvents($type);
		$options = [];

		$options[] = $this->gatherOptions($userId, $events, $type, $object);

		$categlib = TikiLib::lib('categ');
		$categories = $categlib->get_object_categories($type, $object);
		$parents = $categlib->get_with_parents($categories);

		foreach ($parents as $categoryId) {
			$perms = Perms::get('category', $categoryId);
			if ($perms->view_category) {
				$options[] = array_map(function ($item) use ($categories) {
					$item['isParent'] = ! in_array($item['object'], $categories);
					return $item;
				}, $this->gatherOptions($userId, $events, 'category', $categoryId));
			}
		}

		unset($events['tiki.save']); // Disallow global watch-all
		$options[] = $this->gatherOptions($userId, $events, 'global', null);

		return call_user_func_array('array_merge', $options);
	}

	function replacePriority($user, $event, $target, $priority)
	{
		$tikilib = TikiLib::lib('tiki');
		$userId = $tikilib->get_user_id($user);

		if ($userId === -1 || ! $userId) {
			return false;
		}

		$priorities = $this->getPriorities();
		if (! isset($priorities[$priority])) {
			return false;
		}

		$table = $this->table();

		$base = ['userId' => $userId, 'target' => $target, 'event' => $event];

		if ($priority === 'none') {
			$table->delete($base);
		} else {
			$table->insertOrUpdate(['priority' => $priority], $base);
		}

		return true;
	}

	/**
	 * Bind all events required to process notifications.
	 * One event is bound per active event type to collect the
	 * notifications to be sent out. A final event is sent out on
	 * shutdown to process the queued notifications.
	 */
	function bindEvents(Tiki_Event_Manager $events)
	{
		$events->bind('tiki.process.shutdown', function () {
			$this->finalEvent();
		});

		$db = TikiDb::get();
		foreach ($db->fetchAll('SELECT DISTINCT event FROM tiki_user_monitors') as $row) {
			$event = $row['event'];
			$events->bind($event, function ($args, $originalEvent) use ($event) {
				$this->handleEvent($args, $originalEvent, $event);
			});
		}
	}

	private function handleEvent($args, $originalEvent, $registeredEvent)
	{
		if (! isset($args['type']) || ! isset($args['object'])) {
			return;
		}

		$eventId = $args['EVENT_ID'];

		// Handle newly encountered events
		if (! isset($this->queue[$eventId])) {
			$this->queue[$eventId] = [
				'event' => $originalEvent,
				'arguments' => $args,
				'events' => [],
			];
		}

		$this->queue[$eventId]['events'][] = $registeredEvent;
	}

	private function finalEvent()
	{
		$queue = $this->queue;
		$this->queue = [];

		$activitylib = TikiLib::lib('activity');

		$tx = TikiDb::get()->begin();

		// TODO : Implement sendTo
		// TODO : Shrink large events / truncate content ? 

		foreach ($queue as $item) {
			list($args, $sendTo) = $this->finalHandleEvent($item['arguments'], $item['events']);

			if ($args) {
				$activitylib->recordEvent($item['event'], $args);
			}
		}

		$tx->commit();
	}
	
	private function finalHandleEvent($args, $events)
	{
		$targets = $this->collectTargets($args);

		$table = $this->table();
		$results = $table->fetchAll(['priority', 'userId'], [
			'event' => $table->in($events),
			'target' => $table->in($targets),
		]);

		if (empty($results)) {
			return [null, []];
		}

		$sendTo = [];
		$args['stream'] = isset($args['stream']) ? (array) $args['stream'] : [];

		foreach ($results as $row) {
			// Add entries to the named streams, each user will have a few of those
			$priority = $row['priority'];
			$args['stream'][] = $priority . $row['userId'];

			if ($priority == 'critical') {
				$sendTo[] = $row['userId'];
			}
		}

		return [$args, array_unique($sendTo)];
	}

	private function gatherOptions($userId, $events, $type, $object)
	{
		if ($object) {
			$objectInfo = $this->getObjectInfo($type, $object);
		} else {
			$objectInfo = array(
				'type' => 'global',
				'target' => 'global',
				'title' => tr('Anywhere'),
				'isContainer' => true,
			);
		}

		$options = [];

		$isContainer = $objectInfo['isContainer'];
		foreach ($events as $eventName => $info) {
			if ($isContainer || ! $info['global']) {
				$options[] = $this->createOption($userId, $eventName, $info['label'], $objectInfo);
			}
		}

		return $options;
	}

	private function getObjectInfo($type, $object)
	{
		$objectlib = TikiLib::lib('object');

		$title = $objectlib->get_title($type, $object);

		list($type, $object) = $this->cleanObjectId($type, $object);

		return array(
			'type' => $type,
			'object' => $object,
			'target' => "$type:$object",
			'title' => $title,
			'isContainer' => $type == 'category',
		);
	}

	private function cleanObjectId($type, $object)
	{
		// Hash must be short, so never use page names or such, use IDs
		if ($type == 'wiki page') {
			$tikilib = TikiLib::lib('tiki');
			$object = $tikilib->get_page_id_from_name($object);
		}

		return [$type, (int) $object];
	}

	private function createOption($userId, $eventName, $label, $objectInfo)
	{
		$conditions = ['userId' => $userId, 'event' => $eventName, 'target' => $objectInfo['target']];
		$active = $this->table()->fetchOne('priority', $conditions);

		return array(
			'priority' => $active ?: 'none',
			'event' => $eventName,
			'target' => $objectInfo['target'],
			'hash' => md5($eventName . $objectInfo['target']),
			'type' => $objectInfo['type'],
			'object' => $objectInfo['object'],
			'description' => $objectInfo['isContainer']
				? tr('%0 in %1', $label, $objectInfo['title'])
				: tr('%0 for %1', $label, $objectInfo['title']),
		);
	}

	private function getApplicableEvents($type)
	{
		switch ($type) {
		case 'wiki page':
			return [
				'tiki.save' => ['global' => false, 'label' => tr('Any activity')],
				'tiki.wiki.save' => ['global' => true, 'label' => tr('Page created or modified')],
				'tiki.wiki.create' => ['global' => true, 'label' => tr('Page created')],
				'tiki.wiki.update' => ['global' => false, 'label' => tr('Page modified')],
			];
		default:
			return [];
		}
	}

	private function collectTargets($args)
	{
		$type = $args['type'];
		$object = $args['object'];

		$categlib = TikiLib::lib('categ');
		$categories = $categlib->get_object_categories($type, $object);
		$categories = $categlib->get_with_parents($categories);
		$targets = array_map(function ($categoryId) {
			return "category:$categoryId";
		}, $categories);
		
		list($type, $object) = $this->cleanObjectId($type, $object);
		$targets[] = 'global';
		$targets[] = "$type:$object";

		return $targets;
	}

	private function table()
	{
		return TikiDb::get()->table('tiki_user_monitors');
	}
}

