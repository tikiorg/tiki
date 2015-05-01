<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
		global $prefs;

		$unifiedsearchlib = TikiLib::lib('unifiedsearch');
		$index = $unifiedsearchlib->getIndex();

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
		$query->setRange(0, $prefs['unified_lucene_max_result']);

		if ($body = $input->filters->none()) {
			$builder = new Search_Query_WikiBuilder($query);
			$builder->apply(WikiParser_PluginMatcher::match($body));
		}

		$result = $query->search($index);

		$response = array();

		$fields = array();
		if ($definition = Tracker_Definition::get($input->trackerId->int())) {
			foreach ($definition->getPopupFields() as $fieldId) {
				if ($field = $definition->getField($fieldId)) {
					$fields[] = $field;
				}
			}
		}

		$smarty = TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_modifier_sefurl');
		$trklib = TikiLib::lib('trk');
		foreach ($result as $row) {
			$item = Tracker_Item::fromId($row['object_id']);
			$description = '';
			foreach ($fields as $field) {
				if ($item->canViewField($field['fieldId'])) {
					$val = trim($trklib->field_render_value(
						array(
							'field' => $field,
							'item' => $item->getData(),
							'process' => 'y',
						)
					));
					if ($val) {
						if (count($fields) > 1) {
							$description .= "<h5>{$field['name']}</h5>";
						}
						$description .= $val;
					}
				}
			}
			$response[] = array(
				'id' => $row['object_id'],
				'trackerId' => isset($row['tracker_id']) ? $row['tracker_id'] : null,
				'title' => $row['title'],
				'description' => $description,
				'url' => smarty_modifier_sefurl($row['object_id'], $row['object_type']),
				'allDay' => false,
				'start' => $this->getTimestamp($row[$start]),
				'end' => $this->getTimestamp($row[$end]),
				'editable' => $item->canModify(),
				'color' => $this->getColor(isset($row[$coloring]) ? $row[$coloring] : ''),
				'textColor' => '#000',
				'resource' => ($resource && isset($row[$resource])) ? strtolower($row[$resource]) : '',
			);
		}

		return $response;
	}

	private function getTimestamp($value)
	{
		if (preg_match('/^\d{14}$/', $value)) {
			// Facing a date formated as YYYYMMDDHHIISS as indexed in lucene
			// Always stored as UTC
			return date_create_from_format('YmdHise', $value . 'UTC')->getTimestamp();
		} elseif (is_numeric($value)) {
			return $value;
		} else {
			return strtotime($value);
		}
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

