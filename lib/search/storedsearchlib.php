<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

class StoredSearchLib
{
	public function createBlank($label, $priority, $description)
	{
		$userId = TikiLib::lib('login')->getUserId();

		if ($userId && $this->isValidPriority($priority)) {
			return $this->table()->insert(array(
				'userId' => $userId,
				'label' => $label,
				'priority' => $priority,
				'description' => $description,
			));
		}
	}

	public function getUserQueries()
	{
		$userId = TikiLib::lib('login')->getUserId();

		return $this->table()->fetchAll(array('queryId', 'label', 'priority', 'description', 'lastModif'), array(
			'userId' => $userId,
		), -1, -1, array(
			'label' => 'ASC',
		));
	}

	public function getEditableQuery($queryId)
	{
		$data = $this->fetchQuery($queryId);
		if (! $data) {
			return false;
		}

		if (! $this->canUserStoreQuery($data)) {
			return false;
		}

		return $data;
	}

	public function deleteQuery($data)
	{
		$this->table()->delete(array('queryId' => $data['queryId']));
		$this->removeFromIndex("{$data['priority']}-{$data['queryId']}");
	}

	public function storeUserQuery($queryId, $query)
	{
		$data = $this->getEditableQuery($queryId);

		$query = clone $query;

		$unifiedsearchlib = TikiLib::lib('unifiedsearch');
		// Apply jail and base properties
		$unifiedsearchlib->initQueryBase($query);

		$this->table()->update(array(
			'query' => serialize($query),
			'lastModif' => TikiLib::lib('tiki')->now,
		), array(
			'queryId' => $queryId,
		));

		$priority = $this->getPriority($data['priority']);
		if ($priority['indexed']) {
			$this->loadInIndex($GLOBALS['user'], "{$data['priority']}-$queryId", $query);
		}

		return true;
	}

	function updateQuery($queryId, $label, $priority, $description)
	{
		$data = $this->getEditableQuery($queryId);

		if (! $data) {
			return false;
		}

		if (! $this->isValidPriority($priority)) {
			return false;
		}

		$this->table()->update(array(
			'label' => $label,
			'priority' => $priority,
			'description' => $description,
			'lastModif' => TikiLib::lib('tiki')->now,
		), array(
			'queryId' => $queryId,
		));

		$oldPriority = $this->getPriority($data['priority']);
		if ($oldPriority['indexed'] && $data['priority'] != $priority) {
			$this->removeFromIndex("{$data['priority']}-$queryId");
		}

		$newPriority = $this->getPriority($priority);

		if ($newPriority['indexed'] && ! empty($data['query'])) {
			$this->loadInIndex($GLOBALS['user'], "$priority-$queryId", unserialize($data['query']));
		}
	}

	public function getPresentedQuery($queryId)
	{
		$data = $this->fetchQuery($queryId);

		if (! empty($data['query'])) {
			$query = unserialize($data['query']);
		} else {
			$query = new Search_Query;
		}

		$unifiedsearchlib = TikiLib::lib('unifiedsearch');
		$unifiedsearchlib->initQueryPresentation($query);
		
		return array(
			'query' => $query,
			'label' => $data['label'],
			'description' => TikiLib::lib('parser')->parse_data($data['description']),
		);
	}

	public function reloadAll()
	{
		$table = $this->table();
		$queries = $table->fetchColumn('queryId', [
			'priority' => $table->in($this->getIndexedPriorities()),
		]);
		$unifiedsearchlib = TikiLib::lib('unifiedsearch');
		$index = $unifiedsearchlib->getIndex('data-write');

		$tikilib = TikiLib::lib('tiki');
		foreach ($queries as $queryId) {
			$info = $this->fetchQuery($queryId);
			$user = $tikilib->get_user_login($info['userId']);

			if (! empty($info['query'])) {
				$query = unserialize($info['query']);
				$this->loadInIndex($user, "{$info['priority']}-$queryId", $query, $index);
			}
		}
	}

	public function getPriorities()
	{
		global $prefs;

		static $list;
		if (! $list) {
			$list = array(
				'manual' => array(
					'label' => tr('On Demand'),
					'description' => tr('You can revisit the results of this saved search on demand.'),
					'class' => 'label-default',
					'indexed' => false,
				),
			);

			$index = TikiLib::lib('unifiedsearch')->getIndex();
			if ($prefs['monitor_enabled'] == 'y' && $index instanceof Search_Index_QueryRepository) {
				$monitorlib = TikiLib::lib('monitor');
				foreach ($monitorlib->getPriorities() as $priority => $info) {
					if ($priority != 'none') {
						$info['indexed'] = true;
						$list[$priority] = $info;
					}
				}
			}
		}

		return $list;
	}

	private function loadInIndex($user, $name, $query, $index = null)
	{
		if (! $index) {
			$unifiedsearchlib = TikiLib::lib('unifiedsearch');
			$index = $unifiedsearchlib->getIndex('data-write');
		}

		if ($index) {
			$userlib = TikiLib::lib('user');
			$groups = array_keys($userlib->get_user_groups_inclusion($user));
			$query->filterPermissions($groups);

			$query->store($name, $index);
		}
	}
	
	private function removeFromIndex($name)
	{
		$unifiedsearchlib = TikiLib::lib('unifiedsearch');
		$index = $unifiedsearchlib->getIndex('data-write');

		if ($index && $index instanceof Search_Index_QueryRepository) {
			$index->unstore($name);
		}
	}

	private function table()
	{
		return TikiDb::get()->table('tiki_search_queries');
	}

	private function isValidPriority($priority)
	{
		return !! $this->getPriority($priority);
	}

	private function getPriority($priority)
	{
		$priorities = $this->getPriorities();
		if (isset($priorities[$priority])) {
			return $priorities[$priority];
		}
	}

	private function fetchQuery($queryId)
	{
		return $this->table()->fetchFullRow(array(
			'queryId' => $queryId,
		));
	}

	private function canUserStoreQuery($query)
	{
		$userId = TikiLib::lib('login')->getUserId();

		return $userId && $query && $userId == $query['userId'];
	}

	private function getIndexedPriorities()
	{
		$indexed = [];
		foreach ($this->getPriorities() as $key => $info) {
			if ($info['indexed']) {
				$indexed[] = $key;
			}
		}
		return $indexed;
	}

	public function handleQueryNotification($args, $event)
	{
		if (! $query = $this->fetchQuery($args['query'])) {
			return;
		}

		$monitorlib = TikiLib::lib('monitor');

		$monitorlib->directNotification($query['priority'], $query['userId'], $event, $args);
	}
}

