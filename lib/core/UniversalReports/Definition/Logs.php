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
		
		return array(
			"values"=> array(
				"logs"=> $logs
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
					),
				),
			),
		);
	}

	function output($values = array())
	{
		return TikiLib::lib("logsqry")
			->type($values['logs']['value'])
			->start(strtotime($values['logs']['start']))
			->end(strtotime($values['logs']['end']))
			->fetchAll();
	}
}