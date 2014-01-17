<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class MonitorLib
{
	function getPriorities()
	{
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
		foreach ($categories as $categoryId) {
			$perms = Perms::get('category', $categoryId);
			if ($perms->view_category) {
				$options[] = $this->gatherOptions($userId, $events, 'category', $categoryId);
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
			var_dump($userId, $user);
			return false;
		}

		$priorities = $this->getPriorities();
		if (! isset($priorities[$priority])) {
			var_dump($priority);
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

		// Hash must be short, so never use page names or such, use IDs
		if ($type == 'wiki page') {
			$tikilib = TikiLib::lib('tiki');
			$object = $tikilib->get_page_id_from_name($object);
		}

		return array(
			'type' => $type,
			'target' => "$type:$object",
			'title' => $title,
			'isContainer' => $type == 'category',
		);
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

	private function table()
	{
		return TikiDb::get()->table('tiki_user_monitors');
	}
}

