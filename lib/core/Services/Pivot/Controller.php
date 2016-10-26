<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

class Services_Pivot_Controller
{
	function setUp()
	{
		global $prefs;
		$this->utilities = new Services_Tracker_Utilities;
	}

	function action_fetchUnifiedData($input)
	{
		global $prefs;
    
		$unifiedsearchlib = TikiLib::lib('unifiedsearch');
		$index = $unifiedsearchlib->getIndex();
        //getting data by tracker id
		$query = $unifiedsearchlib->buildQuery(array());
 		$query->filterType('trackerItem');
		$query->filterContent($input->trackerId->word(), 'tracker_id');

        $query->setRange(0, $prefs['unified_lucene_max_result']);
        $result = $query->search($index);
		
		$builder = new Search_Query_WikiBuilder($query);
		$builder->wpquery_list_max($query,count($result));
		$builder->apply(WikiParser_PluginMatcher::match($body));
	    
		
        $response = array();

		$fields = array();
		
		//building tracker fields array for mapping with column values
		if ($definition = Tracker_Definition::get($input->trackerId->int())) {
			$fields=$definition->getFields();
		}


		$trklib = TikiLib::lib('trk');
		
		//loop to fetch item values and map with fields
		foreach ($result as $row) {
			$item = Tracker_Item::fromId($row['object_id']);
			$fieldsArr=array();
			foreach ($fields as $field) {
				if ($item->canViewField($field['fieldId'])) {
					$val = trim($trklib->field_render_value(
						array(
							'field' => $field,
							'item' => $item->getData(),
							'process' => 'y',
						)
					));
					$fieldsArr[$field['name']]=strip_tags($val);
				}
			   
			}
			//storing fields array to return array	
			$response[] = $fieldsArr;
		}
		return $response;
	}//end of function getUnifedData
}