<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Report_Definition_Logs
{
	function input()
	{
		$logs = [];
		foreach (TikiLib::lib('logsqry')->listTypes() as $type) {
			$logs[] = [
				"label" => tr(ucwords($type)),
				"value" => $type,
			];
		}

		$actions = [
			[
				"label" => tr("All"),
				"value" => ""
			]
		];

		foreach (TikiLib::lib('logsqry')->listActions() as $action) {
			$actions[] = [
				"label" => tr(ucwords($action)),
				"value" => $action,
			];
		}

		$fields = [];
		foreach (TikiLib::fetchAll("SHOW COLUMNS FROM tiki_actionlog") as $column) {
			$fields[] = [
				"label" => tr(ucwords($column['Field'])),
				"value" => $column['Field'],
			];
		}

		return [
			"values" => [
				"logs" => $logs,
				"actions" => $actions,
				"fields" => $fields,
				"grouping" => [
					[
						"label" => tr("None"),
						"value" => ""
					],
					[
						"label" => tr("Count"),
						"value" => "count"
					],
					[
						"label" => tr("Count By Date"),
						"value" => "countByDate"
					],
					[
						"label" => tr("Count By Date Filter Id"),
						"value" => "countByDateFilterId"
					],
					[
						"label" => tr("Count Users Filter Id"),
						"value" => "countUsersFilterId"
					],
					[
						"label" => tr("Count Users IP Filter Id"),
						"value" => "countUsersIPFilterId"
					],
				],
				"sort" => [
					[
						"label" => tr("None"),
						"value" => ""
					],
					[
						"label" => tr("Ascending By Date"),
						"value" => "asc"
					],
					[
						"label" => tr("Descending By Date"),
						"value" => "desc"
					]
				]
			],
			"options" => [
				[
					"label" => tr("Logs"),
					"key" => "logs",
					"type" => "single",
					"values" => "logs",
					"repeats" => false,
					"options" => [
						[
							"label" => tr("Action"),
							"key" => "action",
							"type" => "single",
							"values" => "actions",
							"repeats" => false,
						],
						[
							"label" => tr("Start"),
							"key" => "start",
							"type" => "date",
							"repeats" => false,
						],
						[
							"label" => tr("End"),
							"key" => "end",
							"type" => "date",
							"repeats" => false,
						],
						[
							"label" => tr("Fields"),
							"key" => "fields",
							"type" => "multi",
							"values" => "fields",
							"repeats" => false,
						],
						[
							"label" => tr("Grouping"),
							"key" => "grouping",
							"type" => "single",
							"values" => "grouping"
						],
						[
							"label" => tr("Sort"),
							"key" => "sort",
							"type" => "single",
							"values" => "sort"
						],
						[
							"label" => tr("Limit"),
							"key" => "limit",
							"type" => "single",
						]
					],
				],
			],
		];
	}

	function output($values = [])
	{
		global $tikilib,$user;

		$qry = TikiLib::lib("logsqry")->type($values['logs']['value']);

		if (isset($values['logs']['action']) && isset($values['logs']['action']['value'])) {
			$qry->action($values['logs']['action']['value']);
		}
		if (isset($values['logs']['start']) && isset($values['logs']['start']['value'])) {
			$qry->start(strtotime($values['logs']['start']['value']));
		}
		if (isset($values['logs']['end']) && isset($values['logs']['end']['value'])) {
			$qry->end(strtotime($values['logs']['end']['value']));
		}

		$usersItems = [];//user items need to be choosable as to what type
		foreach ($tikilib->fetchAll(
			"SELECT itemId FROM tiki_tracker_items WHERE createdBy = ?",
			[$user]
		) as $item) {
			$usersItems[] = $item['itemId'];
		}

		if (! empty($values['logs']['grouping'])) {
			switch ($values['logs']['grouping']['value']) {
				case "count":
					$qry->count();
					break;
				case "countByDate":
					$qry->countByDate();
					break;
				case "countByDateFilterId":
					$qry->countByDateFilterId($usersItems);
					break;
				case "countUsersFilterId":
					$qry->countUsersFilterId($usersItems);
					break;
				case "countUsersIPFilterId":
					$qry->countUsersIPFilterId($usersItems);
					break;
			}
		}

		if (! empty($values['logs']['limit'])) {
			$qry->limit($values['logs']['limit']['value']);
		}

		if (! empty($values['logs']['sort'])) {
			switch ($values['logs']['sort']['value']) {
				case "asc":
					$qry->asc();
					break;
				case "desc":
					$qry->desc();
					break;
			}
		}

		$result = $qry->fetchAll();

		if (! empty($values['logs']['fields'])) {
			$newResult = [];

			foreach ($result as $row) {
				$newRow = [];
				foreach ($values['logs']['fields'] as $field) {
					$newRow[$field['value']] = $row[$field['value']];
				}
				$newResult[] = $newRow;
			}

			$result = $newResult;
		}


		//date correction/format
		foreach ($result as $key => $row) {
			if (isset($result[$key]['lastModif'])) {
				$result[$key]['lastModif'] = $tikilib->get_short_datetime($result[$key]['lastModif']);
			}
		}

		return $result;
	}
}
