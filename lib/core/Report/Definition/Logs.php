<?php
class UniversalReports_Definition_Logs
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
				"fields"=> $fields
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
						)
					),
				),
			),
		);
	}

	function output($values = array())
	{
		$result = TikiLib::lib("logsqry")
			->type($values['logs']['value'])
			->start(strtotime($values['logs']['start']))
			->end(strtotime($values['logs']['end']))
			->fetchAll();
		
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