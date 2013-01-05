<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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

		if ($coloring = $input->coloringField->word()) {
			$coloring = 'tracker_field_' . $coloring;
		}

		$query = $unifiedsearchlib->buildQuery(array());
		$query->filterRange($input->start->int(), $input->end->int(), array($start, $end));

		if ($body = $input->filters->none()) {
			$builder = new Search_Query_WikiBuilder($query);
			$builder->apply(WikiParser_PluginMatcher::match($body));
		}

		$result = $query->search($index);

		$result = $dataSource->getInformation($result, array('title', $start, $end));

		$response = array();

		$smarty = TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_modifier_sefurl');
		foreach ($result as $row) {
			$item = Tracker_Item::fromId($row['object_id']);
			$response[] = array(
				'id' => $row['object_id'],
				'trackerId' => isset($row['tracker_id']) ? $row['tracker_id'] : null,
				'title' => $row['title'],
				'description' => '',
				'url' => smarty_modifier_sefurl($row['object_id'], $row['object_type']),
				'allDay' => false,
				'start' => (int) $row[$start],
				'end' => (int) $row[$end],
				'editable' => $item->canModify(),
				'color' => $this->getColor(isset($row[$coloring]) ? $row[$coloring] : ''),
				'textColor' => '#000',
				'resource' => ($resource && isset($row[$resource])) ? $row[$resource] : '',
			);
		}

		return $response;
	}

	private function getColor($value)
	{
		static $colors = array('#6cf', '#6fc', '#c6f', '#cf6', '#f6c', '#fc6');
		static $map = array();

		if (! isset($map[$value])) {
			$color = array_shift($colors);
			$colors[] = $color;
			$map[$value] = $color;
		}
		
		return $map[$value];
	}
}

