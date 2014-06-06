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
		Services_Exception_Disabled::check('mustread_enabled');
	}

	function action_list($input)
	{
		global $prefs, $user;

		$owner = Search_Query_Relation::token('tiki.mustread.owns.invert', 'user', $user);

		$lib = TikiLib::lib('unifiedsearch');
		$query = $lib->buildQuery([
			'tracker_id' => $prefs['mustread_tracker'],
		]);
		$query->getSubQuery('relations')
			->filterRelation($owner);

		$result = $query->search($lib->getIndex());

		$lib = TikiLib::lib('relation');
		$relations = array_map(function ($item) {
			return Search_Query_Relation::token($item['relation'], $item['type'], $item['itemId']);
		}, $lib->get_relations_from('user', $user, 'tiki.mustread.'));
		$relations = array_fill_keys($relations, 1);

		foreach ($result as & $row) {
			if (isset($relations[Search_Query_Relation::token('tiki.mustread.owns', $row['object_type'], $row['object_id'])])) {
				$row['reason'] = 'owner';
			}
		}

		return [
			'title' => tr('Must Read'),
			'list' => $result,
		];
	}

	function action_detail($input)
	{
		global $prefs;
		$tracker = Tracker_Definition::get($prefs['mustread_tracker']);

		$id = $input->id->int();

		$item = Tracker_Item::fromId($id);
		if (! $item || $tracker !== $item->getDefinition()) {
			throw new Services_Exception_NotFound(tr('Must Read Item not found'));
		}

		return [
			'title' => tr('Must Read'),
			'item' => $item->getData(),
			'plain' => $input->plain->int(),
		];
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

