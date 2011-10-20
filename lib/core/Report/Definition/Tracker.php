<?php
class Report_Definition_Tracker
{	
	function input() {
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
					"required"=>	true,
					"options" =>	array(
						array(
							"label"=> 		tr("Start"),
							"name"=> 		"start",
							"type"=> 		"date",
							"repeats"=>		false,
							"required"=>	false,
						),
						array(
							"label"=> 		tr("End"),
							"name"=> 		"end",
							"type"=> 		"date",
							"repeats"=>		false,
							"required"=>	false,
						),
						array(
							"label"=> 		tr("Item Id"),
							"name"=> 		"itemId",
							"type"=> 		"single",
							"repeats"=>		false,
							"required"=>	false,
						),
						array(
							"label"=> 		tr("Status"),
							"name"=> 		"status",
							"type"=> 		"multi",
							"values"=> 		"trackerItemStatus",
							"repeats"=>		false,
							"required"=>	false,
						),
						array(
							"label"=> 		tr("Search"),
							"relationLabel"=>tr(" for "),
							"name"=> 		"search",
							"type"=> 		"singleOneToOne",
							"dependancy"=>	array("tracker"),
							"values"=> 		array("trackerFields"),
							"repeats"=>		true,
							"required"=>	false,
						),
						array(
							"label"=> 		tr("Fields"),
							"name"=> 		"fields",
							"type"=> 		"multi",
							"dependancy"=>	"tracker",
							"values"=> 		"trackerFields",
							"repeats"=>		false,
							"required"=>	false,
						),
					),
				),
			),
		);
	}
	
	function output($values = array())
	{
		global $tikilib;
		$tracker = $values['tracker'];
		
		$qry = TikiLib::lib('trkqry')->tracker($tracker['value'])
			->start($tracker['start']['value'])
			->end($tracker['end']['value'])
			->itemId($tracker['itemId']['value'])
			->excludeDetails();
		
		if (!empty($tracker['status'])) {
			$allStatus = '';
			foreach($tracker['status'] as $status) {
				if (!empty($status['value'])) $allStatus .= $status['value'];
			}
			
			$qry->status($allStatus);
		}
		
		if (!empty($tracker['search'])) {
			$fieldIds = array();
			$search = array();
			
			for($i = 0; $i < count($tracker['search']); $i++) {
				if (!empty($tracker['search'][$i]) && $tracker['search'][$i + 1]) {
					$fieldIds[] = $tracker['search']['value'][$i];
					$search[] = $tracker['search']['value'][$i + 1];
				}
				$i++			; //searches are in groups of 2
			}
			
			if (!empty($fieldIds) && !empty($search)) {
				$qry
					->fields($fieldIds)
					->equals($search);
			}
		}
		
		$result = $qry->query(); 
		
		if (!empty($tracker['fields'])) {
			$newResult = array();
			foreach($result as $itemKey => $item) {
				$newResult[$itemKey] = array();
				foreach($tracker['fields'] as $field) {
					$newResult[$itemKey][$field['value']] = $result[$itemKey][$field['value']]; 
				}
			}
			
			$result = $newResult;
			unset($newResult);
		}
		
		return $result;
	}
}