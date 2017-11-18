<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_ContentSource_CalendarSource implements Search_ContentSource_Interface
{
	private $db;

	function __construct()
	{
		$this->db = TikiDb::get();
	}

	function getDocuments()
	{
		return $this->db->table('tiki_calendars')->fetchColumn('calendarId', []);
	}

	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory)
	{
		$item = TikiLib::lib('calendar')->get_calendar($objectId);

		if (! $item) {
			return false;
		}

		$data = [
			'title' => $typeFactory->sortable($item['name']),
			'creation_date' => $typeFactory->timestamp($item['created']),
			'modification_date' => $typeFactory->timestamp($item['lastmodif']),
			'description' => $typeFactory->plaintext($item['description']),
			'language' => $typeFactory->identifier('unknown'),

			'personal' => $typeFactory->identifier($item['personal']),
			'user' => $typeFactory->identifier($item['user']),

			'view_permission' => $typeFactory->identifier('tiki_p_view_calendar'),
		];

		return $data;
	}

	function getProvidedFields()
	{
		return [
			'title',
			'description',
			'language',
			'creation_date',
			'modification_date',

			'personal',
			'user',

			'searchable',

			'view_permission',
		];
	}

	function getGlobalFields()
	{
		return [
			'title' => true,
			'description' => true,
		];
	}
}
