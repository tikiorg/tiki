<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_ContentSource_TrackerItemSource implements Search_ContentSource_Interface
{
	private $db;
	private $fields = null;

	function __construct()
	{
		$this->db = TikiDb::get();
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
		$item = $this->db->table('tiki_tracker_items')->fetchRow(array('trackerId', 'createdBy', 'lastModifBy', 'status', 'lastModif'), array(
			'itemId' => $objectId,
		));
		$data = array(
			'title' => $typeFactory->sortable(''),
			'language' => $typeFactory->identifier('unknown'),
			'modification_date' => $typeFactory->timestamp($item['lastModif']),
			'contributors' => $typeFactory->multivalue(array_unique(array($item['createdBy'], $item['lastModifBy']))),

			'tracker_status' => $typeFactory->identifier($item['status']),
			'tracker_id' => $typeFactory->identifier($item['trackerId']),

			'parent_object_type' => $typeFactory->identifier('tracker'),
			'parent_object_id' => $typeFactory->identifier($item['trackerId']),
			'parent_view_permission' => $typeFactory->identifier('tiki_p_view_trackers'),
		);

		$itemData = $this->db->table('tiki_tracker_item_fields')->fetchMap('fieldId', 'value', array(
			'itemId' => $objectId,
		));

		$definition = Tracker_Definition::get($item['trackerId']);
		if (! $definition) {
			return $data;
		}

		if ($languageField = $definition->getLanguageField()) {
			$data['language'] = $typeFactory->identifier($itemData[$languageField]);
		}

		$fields = $definition->getFields();

		$title = '';
		foreach ($fields as $field) {
			$fieldId = $field['fieldId'];
			$value = isset($itemData[$fieldId]) ? $itemData[$fieldId] : null;

			if (in_array($field['type'], array('A', 'i'))) {
				// Skip attachments and images
				continue;
			}

			if ($field['isMain'] == 'y') {
				$title .= ' ' . $value;
			}

			// Make all fields sortable, except for textarea
			$type = ($field['type'] == 'a') ? 'wikitext' : 'sortable';

			$data['tracker_field_' . $fieldId] = $typeFactory->$type($value);
		}

		$data['title'] = $typeFactory->sortable(trim($title));

		return $data;
	}

	function getProvidedFields()
	{
		if (is_null($this->fields)) {
			$this->fields = array(
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

			$fields = $this->db->table('tiki_tracker_fields')->fetchColumn('fieldId', array());
			foreach ($fields as $field) {
				$this->fields[] = 'tracker_field_' . $field;
			}
		}

		return $this->fields;
	}

	function getGlobalFields()
	{
		// Preserve individual tracker fields
		return array_fill_keys(array_diff($this->getProvidedFields(), array(
			'language',
			'modification_date',
			'contributors',

			'tracker_status',
			'tracker_id',

			'parent_view_permission',
			'parent_object_id',
			'parent_object_type',
		)), true);
	}
}

