<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Report_Definition_Tracker
{
	var $trackers = array();
	var $trackerFields = array();

	function __construct()
	{
		global $tikilib;
		foreach ($tikilib->table('tiki_trackers')->fetchAll(array('trackerId','name')) as $column) {
			$this->trackers[$column['trackerId']] = array(
				"label"=> $column['name'] . ' - ' . $column['trackerId'],
				"name"=> $column['name'],
				"value"=> $column['trackerId'],
			);
		}

		foreach ($tikilib->table('tiki_tracker_fields')->fetchAll(array('trackerId', 'fieldId', 'name')) as $column) {
			$this->trackerFields[$column['fieldId']] = array(
				"label"=> $column['name'] . ' - ' . $column['fieldId'],
				"name"=> $column['name'],
				"value"=> $column['fieldId'],
				"dependancy"=> $column['trackerId'],
			);
		}
	}

	function input()
	{
		/*
			type:
				single (if value, turns into list, if no value, is textbox)
				multi (needs value, is checkbox)
				date	(simple date range)
				singeOneToOne (

		*/
		return array(
			"values"=> array(
				"trackers"=>			$this->trackers,
				"trackerFields"=>		$this->trackerFields,
			),
			"options"=> array(
				array(
					"label"=> 		tr("Tracker"),
					"key"=> 		"tracker",
					"type"=> 		"single",
					"values"=> 		"trackers",
					"required"=>	true,
					"join"=>		array(
										"label" =>	tr(" Join Tracker "),
										"type"	=>	"joinInner",
										"relationLabel"=>tr(" on "),
										"settingsLabel"=>tr(" equals "),
										"values" => "trackers",
										"right" => array(
											"values" => "trackerFields",
											"keyDependancy" => "tracker_fields",
										),
										"left" => array(
											"values" => array(array(
												"label"=>"Item Id",
												"value"=>"item_id"
											)),
										),
					),
					"options" =>	array(
										array(
											"label"=> 		tr("Start"),
											"key"=> 		"start",
											"type"=> 		"date",
										),
										array(
											"label"=> 		tr("End"),
											"key"=> 		"end",
											"type"=> 		"date",
										),
										array(
											"label"=> 		tr("Item Id"),
											"key"=> 		"itemId",
											"type"=> 		"single",
										),
										array(
											"label"=> 		tr("Search"),
											"relationLabel"=>tr(" for "),
											"key"=> 		"search",
											"type"=> 		"singleOneToOne",
											"dependancy"=>	"tracker",
											"values"=> 		"trackerFields",
											"repeats"=>		true,
										),
										array(
											"label"=> 		tr("Status"),
											"key"=> 		"status",
											"type"=> 		"multi",
											"values"=> 		array("o", "p", "c"),
										),
										array(
											"label"=> 		tr("Fields"),
											"key"=> 		"fields",
											"type"=> 		"multi",
											"dependancy"=>	"tracker",
											"values"=> 		"trackerFields",
										),
										array(
											"label"=> 		tr("Limit"),
											"key"=> 		"limit",
											"type"=> 		"single",
										),
					),
				),
			),
		);
	}

	private function innerJoin($leftTracker, $rightTracker, $leftSetting, $rightSetting)
	{
		foreach ($leftTracker as $key => $leftItem) {
            if (
                isset($leftTracker[$key]) &&
                isset($rightTracker[$leftTracker[$key][$rightSetting]]) &&
                is_array($leftTracker[$key]) &&
                is_array($rightTracker[$leftTracker[$key][$rightSetting]])
            ) {
			    $leftTracker[$key] = $leftTracker[$key] + $rightTracker[$leftTracker[$key][$rightSetting]];
            }
		}
		return $leftTracker;
	}

	private function query($values = array())
	{
		$tracker = $values['tracker'];

		$qry = Tracker_Query::tracker($tracker['value'])
			->start($tracker['start']['value'])
			->end($tracker['end']['value'])
			->itemId($tracker['itemId']['value'])
			->excludeDetails();

		if (!empty($tracker['status'])) {
			$allStatus = '';
			foreach ($tracker['status'] as $status) {
				if (!empty($status['value'])) $allStatus .= $status['value'];
			}

			$qry->status($allStatus);
		}

		if (!empty($tracker['search'])) {
			for ($i = 0, $count_tracker_search = count($tracker['search']); $i < $count_tracker_search; $i++) {
				if (!empty($tracker['search'][$i]['value']) && !empty($tracker['search'][$i + 1]['value'])) {
					$qry->filter(
						array(
							"field"=> trim($tracker['search'][$i]['value']),
							"value"=> trim($tracker['search'][$i + 1]['value'])
						)
					);
				}
				$i++; //searches are in groups of 2
			}
		}

		if (!empty($tracker['limit']['value'])) {
			$qry->limit($tracker['limit']['value']);
		}

		$result = $qry->query();

		if (!empty($tracker['fields'])) {
			$newResult = array();
			foreach ($result as $itemKey => $item) {
				$newResult[$itemKey] = array();
				foreach ($tracker['fields'] as $field) {
					$newResult[$itemKey][$field['value']] = $result[$itemKey][$field['value']];
				}
			}

			$result = $newResult;
			unset($newResult);
		}

		foreach ($tracker['join'] as $join) {
			$result = $this->innerJoin($result, $this->query($join), $join['left']['value'], $join['right']['value']);
		}

		return $result;
	}

	function output($values = array())
	{
		$result = $this->query($values);

		foreach ($result as $itemKey => $item) {
			foreach ($item as $fieldKey => $field) {
				$result[$itemKey][$this->trackerFields[$fieldKey]['name'] . " - " . $fieldKey] = $field;
				unset($result[$itemKey][$fieldKey]);
			}
		}

		return $result;
	}
}
