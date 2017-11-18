<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_GlobalSource_RelationSource implements Search_GlobalSource_Interface
{
	private $relationlib;
	private $contentSources;

	function __construct()
	{
		$this->relationlib = TikiLib::lib('relation');
	}

	function setContentSources($contentSources)
	{
		$this->contentSources = $contentSources;
	}

	function getProvidedFields()
	{
		return [
			'relations',
			'relation_types',
		];
	}

	function getGlobalFields()
	{
		return [];
	}

	function getData($objectType, $objectId, Search_Type_Factory_Interface $typeFactory, array $data = [])
	{
		global $prefs;
		if (isset($data['relations']) || isset($data['relation_types'])) {
			return [];
		}

		$relations = [];
		$relation_objects = [];
		$types = [];

		$relation_objects_to_index = [];
		if ($prefs['unified_engine'] == 'elastic') { // only index full objects in elasticsearch
			$relation_objects_to_index = array_map('trim', explode(',', $prefs['unified_relation_object_indexing']));
		}

		$from = $this->relationlib->get_relations_from($objectType, $objectId);
		foreach ($from as $rel) {
			$relations[] = Search_Query_Relation::token($rel['relation'], $rel['type'], $rel['itemId']);
			$types[] = $rel['relation'];

			if (in_array($rel['relation'], $relation_objects_to_index)) {
				$contentSource = $this->contentSources[$rel['type']]; //new Search_ContentSource_TrackerItemSource();
				$data = $contentSource->getDocument($rel['itemId'], $typeFactory);
				$permissionSource = new Search_GlobalSource_PermissionSource(Perms::getInstance());
				$data = array_merge(
					$data,
					$permissionSource->getData($rel['type'], $rel['itemId'], $typeFactory, $data)
				);
				foreach ($data as &$item) {
					if ($item instanceof Search_Type_Interface) {
						$item = $item->getValue();
					}
				}
				$data['relation'] = $rel['relation'];
				$data['object_type'] = $rel['type'];
				$data['object_id'] = $rel['itemId'];
				$relation_objects[] = $data;
			}
		}

		$to = $this->relationlib->get_relations_to($objectType, $objectId);
		foreach ($to as $rel) {
			$relations[] = Search_Query_Relation::token($rel['relation'] . '.invert', $rel['type'], $rel['itemId']);
			$rel_type = $rel['relation'] . '.invert';
			$types[] = $rel_type;

			if (in_array($rel_type, $relation_objects_to_index)) {
				$contentSource = $this->contentSources[$rel['type']]; //new Search_ContentSource_TrackerItemSource();
				$data = $contentSource->getDocument($rel['itemId'], $typeFactory);
				$permissionSource = new Search_GlobalSource_PermissionSource(Perms::getInstance());
				$data = array_merge(
					$data,
					$permissionSource->getData($rel['type'], $rel['itemId'], $typeFactory, $data)
				);
				foreach ($data as &$item) {
					if ($item instanceof Search_Type_Interface) {
						$item = $item->getValue();
					}
				}
				$data['relation'] = $rel['relation'];
				$data['object_type'] = $rel['type'];
				$data['object_id'] = $rel['itemId'];
				$relation_objects[] = $data;
			}
		}

		//take the type array and get a count of each indiv. type
		$type_count = array_count_values($types);
		$rel_count = [];
		foreach ($type_count as $key => $val) {
			//instead of returning an assoc. array, format to "relation:count" format for input in index
			$rel_count[] = $key . ":" . $val;
		}

		return [
			'relations' => $typeFactory->multivalue($relations),
			'relation_objects' => $typeFactory->nested($relation_objects),
			'relation_types' => $typeFactory->multivalue(array_unique($types)),
			'relation_count' => $typeFactory->multivalue($rel_count),
		];
	}
}
