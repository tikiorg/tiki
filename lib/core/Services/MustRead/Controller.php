<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_MustRead_Controller
{
	function setUp()
	{
		Services_Exception_Denied::checkAuth();
		Services_Exception_Disabled::check('mustread_enabled');
	}

	function action_list($input)
	{
		global $prefs, $user;

		$selection = null;

		if ($id = $input->id->int()) {
			$selection = $this->getItem($input->id->int());
		}

		$owner = Search_Query_Relation::token('tiki.mustread.owns.invert', 'user', $user);
		$complete = Search_Query_Relation::token('tiki.mustread.complete.invert', 'user', $user);

		$lib = TikiLib::lib('unifiedsearch');
		$query = $lib->buildQuery([
			'tracker_id' => $prefs['mustread_tracker'],
		]);
		$query->filterRelation("NOT $complete");

		$sub = $query->getSubQuery('relations');

		$sub->filterRelation($owner);

		foreach ($this->getAvailableActions() as $key => $label) {
			$token = Search_Query_Relation::token("tiki.mustread.$key.invert", 'user', $user);
			$sub->filterRelation($token);
		}

		$result = $query->search($lib->getIndex());

		foreach ($result as & $row) {
			$row['reason'] = $this->findReason($row['object_id']);
		}

		return [
			'title' => tr('Must Read'),
			'list' => $result,
			'canAdd' => Tracker_Item::newItem($prefs['mustread_tracker'])->canModify(),
			'selection' => $selection ? $selection->getId() : null,
			'notification' => $input->notification->word(),
		];
	}

	function action_mark($input)
	{
		global $user;

		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			throw new Services_Exception_NotAvailable(tr('Invalid request method'));
		}

		$tx = TikiDb::get()->begin();

		$complete = $input->complete->int();
		$completed = [];

		foreach ($complete as $item) {
			$this->getItem($item); // Validate the item exists

			$result = $this->markComplete($item, $user);

			if ($result) {
				$completed[] = $item;

				TikiLib::events()->trigger('tiki.mustread.complete', array(
					'type' => 'trackeritem',
					'object' => $item,
					'user' => $user,
				));
			}
		}

		if (count($completed) > 0) {
			TikiLib::events()->trigger('tiki.mustread.completed', array(
				'type' => 'user',
				'object' => $user,
				'targets' => $completed,
			));
		}

		$tx->commit();
		
		return [
			'FORWARD' => ['action' => 'list'],
		];
	}

	function action_detail($input)
	{
		$item = $this->getItem($input->id->int());
		$itemId = $item->getId();

		$lib = TikiLib::lib('unifiedsearch');
		$query = $this->getUsers($itemId, $input->notification->word());

		return [
			'title' => tr('Must Read'),
			'item' => $item->getData(),
			'reason' => $this->findReason($itemId),
			'canCirculate' => $this->canCirculate($item),
			'plain' => $input->plain->int(),
			'resultset' => $query ? $query->search($lib->getIndex()) : false,
			'counts' => [
				'sent' => $this->getUserCount($itemId, 'sent'),
				'open' => $this->getUserCount($itemId, 'open'),
				'unopen' => $this->getUserCount($itemId, 'unopen'),
			],
		];
	}

	function action_list_members($input)
	{
		$group = $input->group->groupname();

		$users = $this->getUsers($input->id->int(), 'sent');

		$lib = TikiLib::lib('unifiedsearch');
		$query = $lib->buildQuery([
			'object_type' => 'user',
		]);
		$query->filterMultivalue($group, 'user_groups');
		$query->filterRelation(new Search_Expr_Not($users->getSubQuery('relations')->getExpr()));
		$query->setRange(0, 500);

		$current = (array) $input->current->username();
		foreach ($current as $user) {
			$query->filterContent("NOT \"$user\"", 'object_id');
		}

		$result = $query->search($lib->getIndex());

		return [
			'title' => tr('List Members'),
			'group' => $group,
			'resultset' => $result,
		];
	}

	function action_circulate($input)
	{
		$item = $this->getItem($input->id->int());

		if (! $this->canCirculate($item)) {
			throw new Services_Exception_Denied(tr('Cannot circulate'));
		}

		return [
			'title' => tr('Circulate'),
			'item' => $item->getData(),
			'actions' => $this->getAvailableActions(),
		];
	}

	function action_circulate_members($input)
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			throw new Services_Exception_NotAvailable(tr('Invalid request method'));
		}

		$item = $this->getItem($input->id->int());

		if (! $this->canCirculate($item)) {
			throw new Services_Exception_Denied(tr('Cannot circulate'));
		}

		$group = $input->group->groupname();

		$userlib = TikiLib::lib('user');
		if (! $userlib->group_exists($group)) {
			throw new Services_Exception_FieldError('group', tr('Group does not exist.'));
		}

		$add = 0;
		$skip = 0;

		$tx = TikiDb::get()->begin();

		$members = $userlib->get_members($group);
		$action = $this->getAction($input);

		foreach ($members as $user) {
			$result = $this->requestAction($item->getId(), $user, $action);

			if ($result) {
				$add++;
			} else {
				$skip++;
			}
		}

		if ($add > 0) {
			TikiLib::events()->trigger('tiki.mustread.addgroup', array(
				'type' => 'trackeritem',
				'object' => $item->getId(),
				'user' => $GLOBALS['user'],
				'group' => $group,
				'added' => $add,
				'skipped' => $skip,
				'action' => $action,
			));
		}

		$tx->commit();

		return [
			'group' => $group,
			'add' => $add,
			'skip' => $skip,
		];
	}

	function action_circulate_users($input)
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			throw new Services_Exception_NotAvailable(tr('Invalid request method'));
		}

		$item = $this->getItem($input->id->int());

		if (! $this->canCirculate($item)) {
			throw new Services_Exception_Denied(tr('Cannot circulate'));
		}

		$users = array_filter((array) $input->user->username());

		$add = [];
		$skip = [];

		$tx = TikiDb::get()->begin();
		$action = $this->getAction($input);

		foreach ($users as $user) {
			$result = $this->requestAction($item->getId(), $user, $action);

			if ($result) {
				$add[] = $user;
			} else {
				$skip[] = $user;
			}
		}

		if (count($add) > 0) {
			TikiLib::events()->trigger('tiki.mustread.adduser', array(
				'type' => 'trackeritem',
				'object' => $item->getId(),
				'user' => $GLOBALS['user'],
				'added' => $add,
				'skipped' => $skip,
				'action' => $action,
			));
		}

		$tx->commit();

		return [
			'selection' => $users,
			'add' => count($add),
			'skip' => count($skip),
		];
	}

	private function requestAction($item, $user, $action)
	{
		$relationlib = TikiLib::lib('relation');
		$ret = (bool) $relationlib->add_relation('tiki.mustread.' . $action, 'user', $user, 'trackeritem', $item, true);

		if ($ret) {
			TikiLib::events()->trigger('tiki.mustread.required', array(
				'type' => 'user',
				'object' => $user,
				'user' => $GLOBALS['user'],
				'target' => $item,
				'action' => $action,
			));
		}

		return $ret;
	}

	private function markComplete($item, $user)
	{
		$relationlib = TikiLib::lib('relation');
		return (bool) $relationlib->add_relation('tiki.mustread.complete', 'user', $user, 'trackeritem', $item, true);
	}

	private function getItem($id)
	{
		global $prefs;
		$tracker = Tracker_Definition::get($prefs['mustread_tracker']);

		$item = Tracker_Item::fromId($id);
		if (! $item || $tracker !== $item->getDefinition()) {
			throw new Services_Exception_NotFound(tr('Must Read Item not found'));
		}

		if (! $item->canView()) {
			throw new Services_Exception_Denied(tr('Permission denied'));
		}

		return $item;
	}

	private function findReason($itemId)
	{
		global $user;
		static $relations = [];

		if (! isset($relations[$user])) {
			$lib = TikiLib::lib('relation');
			$rels = array_map(function ($item) {
				return Search_Query_Relation::token($item['relation'], $item['type'], $item['itemId']);
			}, $lib->get_relations_from('user', $user, 'tiki.mustread.'));
			$relations[$user] = array_fill_keys($rels, 1);
		}

		if (isset($relations[$user][Search_Query_Relation::token('tiki.mustread.owns', 'trackeritem', $itemId)])) {
			return 'owner';
		}

		foreach ($this->getAvailableActions() as $key => $label) {
			if (isset($relations[$user][Search_Query_Relation::token("tiki.mustread.$key", 'trackeritem', $itemId)])) {
				return $key;
			}
		}
	}

	private function canCirculate($itemId)
	{
		if ($itemId instanceof Tracker_Item) {
			$itemId = $itemId->getId();
		}

		$reason = $this->findReason($itemId);
		return $reason === 'owner' || $reason === 'circulation';
	}

	private function getUsers($itemId, $list)
	{
		$lib = TikiLib::lib('unifiedsearch');
		$query = $lib->buildQuery([]);

		$complete = Search_Query_Relation::token('tiki.mustread.complete', 'trackeritem', $itemId);

		$relations = $query->getSubQuery('relations');

		foreach ($this->getAvailableActions() as $key => $label) {
			$token = Search_Query_Relation::token("tiki.mustread.$key", 'trackeritem', $itemId);
			$relations->filterRelation($token);
		}

		if ($list == 'sent') {
			// All, no additional filtering
		} elseif ($list == 'open') {
			$query->filterRelation($complete);
		} elseif ($list == 'unopen') {
			$query->filterRelation("NOT \"$complete\"");
		} else {
			return false;
		}

		return $query;
	}

	private function getUserCount($itemId, $list)
	{
		$lib = TikiLib::lib('unifiedsearch');
		$query = $this->getUsers($itemId, $list);
		$query->setRange(0, 0);
		$resultset = $query->search($lib->getIndex());

		return $resultset->count();
	}

	private function getAvailableActions()
	{
		return [
			'required' => tr('Read'),
			'comment' => tr('Respond'),
			'circulation' => tr('Circulate'),
		];
	}

	private function getAction($input)
	{
		$action = $input->required_action->word();
		if (isset($this->getAvailableActions()[$action])) {
			return $action;
		} else {
			return 'required';
		}
	}

	/**
	 * Event handler.
	 *
	 * Assign a relation between the item creator and the must read ownership.
	 */
	public static function handleItemCreation(array $args)
	{
		global $prefs, $user;

		if ($prefs['mustread_tracker'] == $args['trackerId']) {
			$lib = TikiLib::lib('relation')->add_relation('tiki.mustread.owns', 'user', $user, $args['type'], $args['object']);
		}
	}

	public static function handleUserCreation(array $args)
	{
		global $prefs;
		if ($prefs['monitor_enabled'] == 'y') {
			// All users created get auto-assigned notifications on must read required events, they are free to adjust the level themselves later
			TikiLib::lib('monitor')->replacePriority($args['object'], 'tiki.mustread.required', "user:{$args['userId']}", 'critical');
		}
	}
}

