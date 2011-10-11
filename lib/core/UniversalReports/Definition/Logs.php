<?php
class UniversalReports_Definition_Logs
{
	var $trkqrylib;
	var $report = array();
	
	function __construct()
	{

	}
	
	static function definition() {
		global $tikilib;
		$trackers = array();
		foreach($tikilib->table('tiki_trackers')->fetchAll(array('trackerId','name')) as $column) {
			$trackers[] = array(
				"name"=> $column['name'] . ' - ' . $column['trackerId'],
				"value"=> $column['trackerId'],
			);
		}
		
		$trackerFields = array();
		foreach($tikilib->table('tiki_tracker_fields')->fetchAll(array('trackerId', 'fieldId', 'name')) as $column) {
			$trackerFields[] = array(
				"name"=> $column['name'] . ' - ' . $column['fieldId'],
				"value"=> $column['fieldId'],
				"dependancy"=> $column['trackerId'],
			);
		}

		/*
			type:
				single (if value, turns into list, if no value, is textbox)
				multi (needs value, is checkbox)
				date	(simple date range)
				singeOneToOne (
				
		*/
		return array(
			"values"=> array(
				"trackers"=>		$trackers,
				"trackerFields"=>	$trackerFields,
				"trackerItemStatus"=>array("o", "p", "c"),
			),
			"options"=> array(
				array(
					"label"=> 		tr("Tracker"),
					"name"=> 		"tracker",
					"type"=> 		"single",
					"values"=> 		"trackers",
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
							"label"=> 		tr("Item Id"),
							"name"=> 		"trackerItemId",
							"type"=> 		"single",
							"repeats"=>		false,
						),
						array(
							"label"=> 		tr("Status"),
							"name"=> 		"status",
							"type"=> 		"multi",
							"values"=> 		"trackerItemStatus",
							"repeats"=>		false,
						),
						array(
							"label"=> 		tr("Search"),
							"name"=> 		"search",
							"type"=> 		"singleOneToOne",
							"values"=> 		"trackerFields,",
							"repeats"=>		true,
						),
						array(
							"label"=> 		tr("Fields"),
							"name"=> 		"trackerFields",
							"type"=> 		"multi",
							"dependancy"=>	"tracker",
							"values"=> 		"trackerFields",
							"repeats"=>		false,
						),
					),
				),
			),
		);
	}
	
	static function assemble($report)
	{
		$me = new self();
		$me->report = $report;
		
		
		return $me;
	}
	
	function output()
	{
		return $this->report;
	}
}