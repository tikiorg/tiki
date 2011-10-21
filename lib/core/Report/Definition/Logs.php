<?php
class Report_Definition_Logs
{
	function input()
	{
		$logs = array();
		foreach(TikiLib::lib('logsqry')->listTypes() as $type) {
			$logs[] = array(
				"name"=> tr(ucwords($type)),
				"value"=> $type,
			);
		}
		
		$actions = array(
			array(
				"name"=> tr("All"),
				"value"=> ""
			)
		);
		
		foreach(TikiLib::lib('logsqry')->listActions() as $action) {
			$actions[] = array(
				"name"=> tr(ucwords($action)),
				"value"=> $action,
			);
		}
		
		$fields = array();
		foreach(TikiLib::fetchAll("SHOW COLUMNS FROM tiki_actionlog") as $column) {
			$fields[] = array(
				"name"=> tr(ucwords($column['Field'])),
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
						"name"=> tr("None"),
						"value"=> ""
					),
					array(
						"name"=> tr("Count"),
						"value"=> "count"
					),
					array(
						"name"=> tr("Count By Date"),
						"value"=> "countByDate"
					),
					array(
						"name"=> tr("Count By Date Filter Id"),
						"value"=> "countByDateFilterId"
					),
					array(
						"name"=> tr("Count Users Filter Id"),
						"value"=> "countUsersFilterId"
					),
					array(
						"name"=> tr("Count Users IP Filter Id"),
						"value"=> "countUsersIPFilterId"
					),
				),
				"sort"=> array(
					array(
						"name"=> tr("None"),
						"value"=> ""
					),
					array(
						"name"=> tr("Ascending By Date"),
						"value"=> "asc"
					),
					array(
						"name"=> tr("Descending By Date"),
						"value"=> "desc"
					)
				)
			),
			"options"=> array(
				array(
					"label"=> 		tr("Logs"),
					"name"=> 		"logs",
					"type"=> 		"single",
					"values"=> 		"logs",
					"repeats"=>		false,
					"options" =>	array(
						array(
							"label"=>		tr("Action"),
							"name"=>		"action",
							"type"=>		"single",
							"values"=> 		"actions",
							"repeats"=>		false,
						),
						array(
							"label"=> 		tr("Start"),
							"name"=> 		"start",
							"type"=> 		"date",
							"repeats"=>		false,
						),
						array(
							"label"=> 		tr("End"),
							"name"=> 		"end",
							"type"=> 		"date",
							"repeats"=>		false,
						),
						array(
							"label"=>		tr("Fields"),
							"name"=>		"fields",
							"type"=>		"multi",
							"values"=> 		"fields",
							"repeats"=>		false,
						),
						array(
							"label"=>		tr("Grouping"),
							"name"=>		"grouping",
							"type"=>		"single",
							"values"=>		"grouping"
						),
						array(
							"label"=>		tr("Sort"),
							"name"=>		"sort",
							"type"=>		"single",
							"values"=>		"sort"
						),
						array(
							"label"=>		tr("Limit"),
							"name"=>		"limit",
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
		foreach($tikilib->fetchAll("
			SELECT itemId FROM tiki_tracker_items WHERE createdBy = ?
		", array($user)) as $item) {
			$usersItems[] = $item['itemId'];
		}
		
		if (!empty($values['logs']['grouping'])) {
			switch($values['logs']['grouping']['value']) {
				case "count": 					$qry->count(); 								break;
				case "countByDate": 			$qry->countByDate(); 						break;
				case "countByDateFilterId": 	$qry->countByDateFilterId($usersItems); 	break;
				case "countUsersFilterId": 		$qry->countUsersFilterId($usersItems); 		break;
				case "countUsersIPFilterId": 	$qry->countUsersIPFilterId($usersItems);	break;
			}
		}
		
		if (!empty($values['logs']['limit'])) {
			$qry->limit($values['logs']['limit']['value']);
		}
		
		if (!empty($values['logs']['sort'])) {
			switch ($values['logs']['sort']['value']) {
				case "asc": 	$qry->asc(); 	break;
				case "desc": 	$qry->desc(); 	break;
			}
		}
		
		$result = $qry->fetchAll();
		
		if (!empty($values['logs']['fields'])) {
			$newResult = array();
			
			foreach($result as $row) {
				$newRow = array();
				foreach($values['logs']['fields'] as $field) {
					$newRow[$field['value']] = $row[$field['value']];
				}
				$newResult[] = $newRow;
			}
			
			$result = $newResult;
		}
		
		return $result;
	}
}