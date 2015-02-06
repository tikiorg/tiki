<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Category_Controller
{
	function setUp()
	{
		global $prefs;

		if ($prefs['feature_categories'] != 'y') {
			throw new Services_Exception_Disabled('feature_categories');
		}
	}

	function action_list_categories($input)
	{
		global $prefs;

		$parentId = $input->parentId->int();
		$descends = $input->descends->int();

		if (! $parentId) {
			throw new Services_Exception_MissingValue('parentId');
		}

		$categlib = TikiLib::lib('categ');
		return $categlib->getCategories(array('identifier'=>$parentId, 'type'=>$descends ? 'descendants' : 'children'));
	}

	function action_categorize($input)
	{
		$categId = $input->categId->int();
		$objects = (array) $input->objects->none();

		$perms = Perms::get('category', $categId);

		if (! $perms->add_objects) {
			throw new Services_Exception(tr('Permission denied'), 403);
		}

		$objects = $this->convertObjects($objects);
		if (count($objects) && $input->confirm->int()) {
			return $this->processObjects('doCategorize', $categId, $objects);
		} else {
			return array(
				'categId' => $categId,
				'objects' => $objects,
				'confirm' => 0,
			);
		}
	}

	function action_uncategorize($input)
	{
		$categId = $input->categId->digits();
		$objects = (array) $input->objects->none();

		$perms = Perms::get('category', $categId);

		if (! $perms->remove_objects) {
			throw new Services_Exception(tr('Permission denied'), 403);
		}

		$objects = $this->convertObjects($objects);

		if (count($objects) && $input->confirm->int()) {
			return $this->processObjects('doUncategorize', $categId, $objects);
		} else {
			return array(
				'categId' => $categId,
				'objects' => $objects,
				'confirm' => 0,
			);
		}
	}

	function action_select($input)
	{
		$categlib = TikiLib::lib('categ');
		$objectlib = TikiLib::lib('object');
		$smarty = TikiLib::lib('smarty');

		$type = $input->type->text();
		$object = $input->object->text();

		$perms = Perms::get($type, $object);
		if (! $perms->modify_object_categories) {
			throw new Services_Exception_Denied('Not allowed to modify categories');
		}

		$input->replaceFilter('subset', 'int');
		$subset = $input->asArray('subset', ',');

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$smarty->loadPlugin('smarty_modifier_sefurl');
			$name = $objectlib->get_title($type, $object);
			$url = smarty_modifier_sefurl($object, $type);
			$targetCategories = (array) $input->categories->int();
			$count = $categlib->update_object_categories($targetCategories, $object, $type, '', $name, $url, $subset, false);
		}

		$categories = $categlib->get_object_categories($type, $object);
		return array(
			'subset' => implode(',', $subset),
			'categories' => array_combine(
				$subset,
				array_map(
					function ($categId) use ($categories) {
						return array(
							'name' => TikiLib::lib('object')->get_title('category', $categId),
							'selected' => in_array($categId, $categories),
						);
					},
					$subset
				)
			),
		);
	}

	private function processObjects($function, $categId, $objects)
	{
		$unifiedsearchlib = TikiLib::lib('unifiedsearch');

		$tx = TikiDb::get()->begin();

		foreach ($objects as & $object) {
			$type = $object['type'];
			$id = $object['id'];

			$object['catObjectId'] = $this->$function($categId, $type, $id);
		}

		$tx->commit();

		$query = $unifiedsearchlib->buildQuery([]);
		$query->filterCategory((string) $categId);
		$query->setRange(0, 1);
		$result = $query->search($unifiedsearchlib->getIndex());

		return array(
			'categId' => $categId,
			'count' => count($result),
			'objects' => $objects,
			'confirm' => 1,
		);
	}

	private function doCategorize($categId, $type, $id)
	{
		$categlib = TikiLib::lib('categ');
		return $categlib->categorize_any($type, $id, $categId);
	}

	private function doUncategorize($categId, $type, $id)
	{
		$categlib = TikiLib::lib('categ');
		if ($oId = $categlib->is_categorized($type, $id)) {
			$categlib->uncategorize($oId, $categId);
			return $oId;
		}
		return 0;
	}

	private function convertObjects($objects)
	{
		$out = array();
		foreach ($objects as $object) {
			$object = explode(':', $object, 2);

			if (count($object) == 2) {
				list($type, $id) = $object;
				$objectPerms = Perms::get($type, $id);

				if ($objectPerms->modify_object_categories) {
					$out[] = array('type' => $type, 'id' => $id);
				}
			}
		}

		return $out;
	}
}

