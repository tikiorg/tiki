<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_ContentSource_TrackerSource implements Search_ContentSource_Interface
{
	private $db;

	function __construct()
	{
		$this->db = TikiDb::get();
	}

	function getDocuments()
	{
		return $this->db->table('tiki_trackers')->fetchColumn('trackerId', array());
	}

	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory)
	{
		$lib = TikiLib::lib('trk');
		
		$tracker = $lib->get_tracker($objectId);

		if (! $tracker) {
			return false;
		}

		$data = array(
			'title' => $typeFactory->sortable($tracker['name']),
			'modification_date' => $typeFactory->timestamp($tracker['lastModif']),
			'creation_date' => $typeFactory->timestamp($tracker['created']),
			'description' => $typeFactory->plaintext($tracker['description']),

			'searchable' => $typeFactory->identifier('n'),

			'view_permission' => $typeFactory->identifier('tiki_p_view_trackers'),
		);

		return $data;
	}

	function getProvidedFields()
	{
		return array(
			'title',
			'modification_date',
			'creation_date',
			'description',

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

