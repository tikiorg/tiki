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
		$logs = array();
		foreach (TikiLib::lib('logsqry')->listTypes() as $type) {
			$logs[] = array(
				"label"=> tr(ucwords($type)),
				"value"=> $type,
			);
		}

		$actions = array(
			array(
				"label"=> tr("All"),
				"value"=> ""
			)
		);

		foreach (TikiLib::lib('logsqry')->listActions() as $action) {
			$actions[] = array(
				"label"=> tr(ucwords($action)),
				"value"=> $action,
			);
		}

		$fields = array();
		foreach (TikiLib::fetchAll("SHOW COLUMNS FROM tiki_actionlog") as $column) {
			$fields[] = array(
				"label"=> tr(ucwords($column['Field'])),
				"value"=> $column['Field'],
			);
		}

		return array(
			"values"=> array(
				"logs"=> $logs,
				"actions"=>$actions,
				"fields"=> $fields,
				"grouping"=> array(
					array(
						"label"=> tr("None"),
						"value"=> ""
					),
					array(
						"label"=> tr("Count"),
						"value"=> "count"
					),
					array(
						"label"=> tr("Count By Date"),
						"value"=> "countByDate"
					),
					array(
						"label"=> tr("Count By Date Filter Id"),
						"value"=> "countByDateFilterId"
					),
					array(
						"label"=> tr("Count Users Filter Id"),
						"value"=> "countUsersFilterId"
					),
					array(
						"label"=> tr("Count Users IP Filter Id"),
						"value"=> "countUsersIPFilterId"
					),
				),
				"sort"=> array(
					array(
						"label"=> tr("None"),
						"value"=> ""
					),
					array(
						"label"=> tr("Ascending By Date"),
						"value"=> "asc"
					),
					array(
						"label"=> tr("Descending By Date"),
						"value"=> "desc"
					)
				)
			),
			"options"=> array(
				array(
					"label"=> 		tr("Logs"),
					"key"=> 		"logs",
					"type"=> 		"single",
					"values"=> 		"logs",
					"repeats"=>		false,
					"options" =>	array(
						array(
							"label"=>		tr("Action"),
							"key"=>			"action",
							"type"=>		"single",
							"values"=> 		"actions",
							"repeats"=>		false,
						),
						array(
							"label"=> 		tr("Start"),
							"key"=> 		"start",
							"type"=> 		"date",
							"repeats"=>		false,
						),
						array(
							"label"=> 		tr("End"),
							"key"=> 		"end",
							"type"=> 		"date",
							"repeats"=>		false,
						),
						array(
							"label"=>		tr("Fields"),
							"key"=>			"fields",
							"type"=>		"multi",
							"values"=> 		"fields",
							"repeats"=>		false,
						),
						array(
							"label"=>		tr("Grouping"),
							"key"=>			"grouping",
							"type"=>		"single",
							"values"=>		"grouping"
						),
						array(
							"label"=>		tr("Sort"),
							"key"=>			"sort",
							"type"=>		"single",
							"values"=>		"sort"
						),
						array(
							"label"=>		tr("Limit"),
							"key"=>			"limit",
							"type"=>		"single",
						)
					),
				),
			),
		);
	}

	function output($values = array())
	{
		global $tikilib,$user;

		$qry = TikiLib::lib("logsqry")
			->type($values['logs']['value'])
			->action($values['logs']['action']['value'])
			->start(strtotime($values['logs']['start']['value']))
			->end(strtotime($values['logs']['end']['value']));

		$usersItems = array();//user items need to be choosable as to what type
		foreach ($tikilib->fetchAll(
			"SELECT itemId FROM tiki_tracker_items WHERE createdBy = ?",
			array($user)
		) as $item) {
			$usersItems[] = $item['itemId'];
		}

		if (!empty($values['logs']['grouping'])) {
			switch($values['logs']['grouping']['value']) {
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

		if (!empty($values['logs']['limit'])) {
			$qry->limit($values['logs']['limit']['value']);
		}

		if (!empty($values['logs']['sort'])) {
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

		if (!empty($values['logs']['fields'])) {
			$newResult = array();

			foreach ($result as $row) {
				$newRow = array();
				foreach ($values['logs']['fields'] as $field) {
					$newRow[$field['value']] = $row[$field['value']];
				}
				$newResult[] = $newRow;
			}

			$result = $newResult;
		}


		//date correction/format
		foreach ($result as $key => $row) {
			if (isset($result[$key]['lastModif']))
				$result[$key]['lastModif'] = $tikilib->get_short_datetime($result[$key]['lastModif']);
		}

		return $result;
	}
}
