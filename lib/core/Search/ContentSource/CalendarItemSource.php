<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_ContentSource_CalendarItemSource implements Search_ContentSource_Interface, Tiki_Profile_Writer_ReferenceProvider
{
	private $db;

	function __construct()
	{
		$this->db = TikiDb::get();
	}

	function getReferenceMap()
	{
		return array(
			'calendar_id' => 'calendar',
		);
	}

	function getDocuments()
	{
		$files = $this->db->table('tiki_calendar_items');
		return $files->fetchColumn(
			'calitemId',
			[],
			-1,
			-1,
			'ASC'
		);
	}

	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory)
	{
		$lib = TikiLib::lib('calendar');

		$item = $lib->get_item($objectId);

		if (! $item) {
			return false;
		}

		$allday = (bool) $item['allday'];

		if ($item['status'] == 0) {
			$status_text = tr('Tentative');
		} else if ($item['status'] == 1) {
			$status_text = tr('Confirmed');
		} else if ($item['status'] == 2) {
			$status_text = tr('Cancelled');
		}

		$data = array(
			'title' => $typeFactory->sortable($item['name']),
			'language' => $typeFactory->identifier(empty($item['lang']) ? 'unknown' : $item['lang']),
			'creation_date' => $typeFactory->timestamp($item['created']),
			'modification_date' => $typeFactory->timestamp($item['lastmodif']),
			'contributors' => $typeFactory->multivalue([$item['user']]),
			'description' => $typeFactory->plaintext($item['description']),

			'calendar_id' => $typeFactory->identifier($item['calendarId']),
			'start_date' => $typeFactory->timestamp($item['start'], $allday),
			'end_date' => $typeFactory->timestamp($item['end'], $allday),
			'priority' => $typeFactory->numeric($item['priority']),
			'status' => $typeFactory->numeric($item['status']),
			'status_text' => $typeFactory->identifier($status_text),
			'url' => $typeFactory->identifier($item['url']),
			'recurrence_id' => $typeFactory->identifier($item['recurrenceId']),
			// TODO index recurrences too here?

			'view_permission' => $typeFactory->identifier('tiki_p_view_events'),

			'parent_object_type' => $typeFactory->identifier('calendar'),
			'parent_object_id' => $typeFactory->identifier($item['calendarId']),
			'parent_view_permission' => $typeFactory->identifier('tiki_p_view_calendar'),
		);

		return $data;
	}

	function getProvidedFields()
	{
		return array(
			'title',
			'language',
			'creation_date',
			'modification_date',
			'contributors',
			'description',
			'filename',
			'filetype',
			'filesize',

			'gallery_id',
			'file_comment',
			'file_content',

			'parent_view_permission',
			'parent_object_id',
			'parent_object_type',
		);
	}

	function getGlobalFields()
	{
		return array(
			'title' => true,
			'description' => true,
			'filename' => true,

			'file_comment' => false,
			'file_content' => false,
		);
	}
}

