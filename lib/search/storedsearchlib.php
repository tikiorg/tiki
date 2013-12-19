<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

class StoredSearchLib
{
	public function createBlank($label, $priority)
	{
		$userId = TikiLib::lib('login')->getUserId();

		if ($userId && $this->isValidPriority($priority)) {
			return $this->table()->insert(array(
				'userId' => $userId,
				'label' => $label,
				'priority' => $priority,
			));
		}
	}

	public function getUserQueries()
	{
		$userId = TikiLib::lib('login')->getUserId();

		return $this->table()->fetchAll(array('queryId', 'label', 'priority', 'lastModif'), array(
			'userId' => $userId,
		));
	}

	public function storeUserQuery($queryId, $query)
	{
		if (! $this->canUserStoreQuery($queryId)) {
			return false;
		}

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

		$this->loadInIndex($GLOBALS['user'], $queryId, $query);

		return true;
	}

	function getPriorities($priority)
	{
		return array(
			'manual' => array(
				'label' => tr('Manual'),
				'description' => tr('You can revisit the results of this query on demand'),
				'class' => 'label-default',
			),
		);
	}

	private function loadInIndex($user, $name, $query)
	{
		$unifiedsearchlib = TikiLib::lib('unifiedsearch');
		$index = $unifiedsearchlib->getIndex();

		if ($index) {
			$userlib = TikiLib::lib('user');
			$groups = array_keys($userlib->get_user_groups_inclusion($user));
			$query->filterPermissions($groups);

			$query->store($name, $index);
		}
	}

	private function table()
	{
		return TikiDb::get()->table('tiki_search_queries');
	}

	private function isValidPriority($priority)
	{
		$priorities = $this->getPriorities();
		return isset($priorities[$priority]);
	}

	private function canUserStoreQuery($queryId)
	{
		$userId = TikiLib::lib('login')->getUserId();
		$owner = $this->table()->fetchOne('userId', array(
			'queryId' => $queryId,
		));

		return $userId && $owner && $userId == $owner;
	}
}

