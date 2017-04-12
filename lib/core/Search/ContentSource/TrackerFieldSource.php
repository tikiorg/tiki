<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_ContentSource_TrackerFieldSource implements Search_ContentSource_Interface
{
	private $db;

	function __construct()
	{
		$this->db = TikiDb::get();
	}

	function getDocuments()
	{
		return $this->db->table('tiki_tracker_fields')->fetchColumn('fieldId', array());
	}

	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory)
	{
		$lib = TikiLib::lib('trk');
		
		$field = $lib->get_tracker_field($objectId);

		if (! $field) {
			return false;
		}

		$trackername = tr('unknown');
		if ($definition = Tracker_Definition::get($field['trackerId'])) {
			$trackername = $definition->getConfiguration('name');
		}

		$data = array(
			'title' => $typeFactory->sortable($field['name']),
			'description' => $typeFactory->plaintext($field['description']),
			'tracker_id' => $typeFactory->identifier($field['trackerId']),
			'tracker_name' => $typeFactory->sortable($trackername),

			'searchable' => $typeFactory->identifier('n'),

			'view_permission' => $typeFactory->identifier('tiki_p_view_trackers'),
		);

		return $data;
	}

	function getProvidedFields()
	{
		return array(
			'title',
			'description',
			'tracker_id',
			'tracker_name',

			'searchable',

			'view_permission',
		);
	}

	function getGlobalFields()
	{
		return array(
			'title' => true,
			'description' => true,
		);
	}
}

