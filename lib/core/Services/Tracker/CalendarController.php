<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Tracker_CalendarController
{
	function setUp()
	{
		Services_Exception_Disabled::check('calendar_fullcalendar');
	}

	function action_list($input)
	{
		$unifiedsearchlib = TikiLib::lib('unifiedsearch');
		$index = $unifiedsearchlib->getIndex();
		$dataSource = $unifiedsearchlib->getDataSource();

		$start = 'tracker_field_' . $input->beginField->word();
		$end = 'tracker_field_' . $input->endField->word();

		if ($resource = $input->resourceField->word()) {
			$resource = 'tracker_field_' . $resource;
		}

		$query = new Search_Query;
		$query->filterRange($input->start->int(), $input->end->int(), array($start, $end));
		$result = $query->search($index);

		$result = $dataSource->getInformation($result, array('title', $start, $end));

		$response = array();

		$smarty = TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_modifier_sefurl');
		foreach ($result as $row) {
			$item = Tracker_Item::fromId($row['object_id']);
			$response[] = array(
				'id' => $row['object_id'],
				'title' => $row['title'],
				'description' => '',
				'url' => smarty_modifier_sefurl($row['object_id'], $row['object_type']),
				'allDay' => false,
				'start' => (int) $row[$start],
				'end' => (int) $row[$end],
				'modifiable' => $item->canModify(),
				'color' => '#',
				'textcolor' => '#',
				'resource' => ($resource && isset($row[$resource])) ? $row[$resource] : '',
			);
		}

		return $response;
	}
}

