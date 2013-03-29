<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_ContentSource_TrackerItemSource implements Search_ContentSource_Interface
{
	private $db;
	private $trklib;

	function __construct()
	{
		$this->db = TikiDb::get();
		$this->trklib = TikiLib::lib('trk');
	}

	function getDocuments()
	{
		return $this->db->table('tiki_tracker_items')->fetchColumn('itemId', array());
	}

	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory)
	{
		/*
			If you wonder why this method uses straight SQL and not trklib, it's because
			trklib performs no meaningful work when extracting the data and strips all
			required semantics.
		*/

		$data = array(
			'title' => $typeFactory->sortable(tr('Unknown')),
			'language' => $typeFactory->identifier('unknown'),
		);

		$item = $this->trklib->get_tracker_item($objectId);

		$itemObject = Tracker_Item::fromInfo($item);
		$permNeeded = $itemObject->getViewPermission();

		$definition = Tracker_Definition::get($item['trackerId']);

		if (! $definition) {
			return $data;
		}

		foreach (self::getIndexableHandlers($definition, $item) as $baseKey => $handler) {
			$data = array_merge($data, $handler->getDocumentPart($baseKey, $typeFactory));
		}

		$ownerGroup = $itemObject->getOwnerGroup();
		$data = array_merge(
			$data,
			array(
				'title' => $typeFactory->sortable($this->trklib->get_isMain_value($item['trackerId'], $objectId)),
				'modification_date' => $typeFactory->timestamp($item['lastModif']),
				'contributors' => $typeFactory->multivalue(array_unique(array($item['createdBy'], $item['lastModifBy']))),

				'tracker_status' => $typeFactory->identifier($item['status']),
				'tracker_id' => $typeFactory->identifier($item['trackerId']),

				'view_permission' => $typeFactory->identifier($permNeeded),

				// Fake attributes, removed before indexing
				'_permission_accessor' => $itemObject->getPerms(),
				'_extra_groups' => $ownerGroup ? array($ownerGroup) : null,
			)
		);

		return $data;
	}

	function getProvidedFields()
	{
		static $data;

		if (is_array($data)) {
			return $data;
		}

		$data = array(
			'title',
			'language',
			'modification_date',
			'contributors',

			'tracker_status',
			'tracker_id',

			'parent_view_permission',
			'parent_object_id',
			'parent_object_type',
		);

		foreach ($this->getAllIndexableHandlers() as $baseKey => $handler) {
			$data = array_merge($data, $handler->getProvidedFields($baseKey));
		}

		return array_unique($data);
	}

	function getGlobalFields()
	{
		static $data;

		if (is_array($data)) {
			return $data;
		}

		$data = array();

		foreach ($this->getAllIndexableHandlers() as $baseKey => $handler) {
			$data = array_merge($data, $handler->getGlobalFields($baseKey));
		}

		$data['title'] = true;
		return $data;
	}

	public static function getIndexableHandlers($definition, $item = array())
	{
		global $prefs;
		$indexKey = $prefs['unified_trackerfield_keys'];
		$factory = $definition->getFieldFactory();

		$handlers = array();
		foreach ($definition->getFields() as $field) {
			$fieldKey = 'tracker_field_' . $field[$indexKey];
			$handler = $factory->getHandler($field, $item);

			if ($handler instanceof Tracker_Field_Indexable) {
				$handlers[$fieldKey] = $handler;
			}
		}

		return $handlers;
	}

	private function getAllIndexableHandlers()
	{
		$trackers = $this->db->table('tiki_trackers')->fetchColumn('trackerId', array());

		$handlers = array();
		foreach ($trackers as $trackerId) {
			$definition = Tracker_Definition::get($trackerId);
			$handlers = array_merge($handlers, self::getIndexableHandlers($definition));
		}

		return $handlers;
	}
}

