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

		$owner = Search_Query_Relation::token('tiki.mustread.owns.invert', 'user', $user);
		$required = Search_Query_Relation::token('tiki.mustread.required.invert', 'user', $user);

		$lib = TikiLib::lib('unifiedsearch');
		$query = $lib->buildQuery([
			'tracker_id' => $prefs['mustread_tracker'],
		]);
		$sub = $query->getSubQuery('relations');
		$sub->filterRelation($owner);
		$sub->filterRelation($required);

		$result = $query->search($lib->getIndex());

		$lib = TikiLib::lib('relation');
		$relations = array_map(function ($item) {
			return Search_Query_Relation::token($item['relation'], $item['type'], $item['itemId']);
		}, $lib->get_relations_from('user', $user, 'tiki.mustread.'));
		$relations = array_fill_keys($relations, 1);

		foreach ($result as & $row) {
			if (isset($relations[Search_Query_Relation::token('tiki.mustread.owns', $row['object_type'], $row['object_id'])])) {
				$row['reason'] = 'owner';
			} elseif (isset($relations[Search_Query_Relation::token('tiki.mustread.required', $row['object_type'], $row['object_id'])])) {
				$row['reason'] = 'read';
			}
		}

		return [
			'title' => tr('Must Read'),
			'list' => $result,
		];
	}

	function action_detail($input)
	{
		$item = $this->getItem($input->id->int());
		return [
			'title' => tr('Must Read'),
			'item' => $item->getData(),
			'plain' => $input->plain->int(),
		];
	}

	function action_circulate($input)
	{
		$item = $this->getItem($input->id->int());

		return [
			'title' => tr('Circulate'),
			'item' => $item->getData(),
		];
	}

	function action_circulate_members($input)
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			throw new Services_Exception_NotAvailable(tr('Invalid request method'));
		}

		$item = $this->getItem($input->id->int());

		$group = $input->group->groupname();

		$userlib = TikiLib::lib('user');
		if (! $userlib->group_exists($group)) {
			throw new Services_Exception_FieldError('group', tr('Group does not exist.'));
		}

		$add = 0;
		$skip = 0;

		$tx = TikiDb::get()->begin();

		$members = $userlib->get_members($group);

		foreach ($members as $user) {
			$result = $this->requestAction($item->getId(), $user);

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
				'group' => $group,
				'added' => $add,
				'skipped' => $skip,
			));
		}


		$tx->commit();

		return [
			'group' => $group,
			'add' => $add,
			'skip' => $skip,
		];
	}

	private function requestAction($item, $user)
	{
		$relationlib = TikiLib::lib('relation');
		return (bool) $relationlib->add_relation('tiki.mustread.required', 'user', $user, 'trackeritem', $item, true);
	}

	private function getItem($id)
	{
		global $prefs;
		$tracker = Tracker_Definition::get($prefs['mustread_tracker']);

		$item = Tracker_Item::fromId($id);
		if (! $item || $tracker !== $item->getDefinition()) {
			throw new Services_Exception_NotFound(tr('Must Read Item not found'));
		}

		return $item;
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
}

