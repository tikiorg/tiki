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

		//Services_Exception_Disabled::check('pivot_table');

	}

		function action_fetchUnifiedData($input)
	{
		global $prefs;

		$unifiedsearchlib = TikiLib::lib('unifiedsearch');
		$index = $unifiedsearchlib->getIndex();

		$query = $unifiedsearchlib->buildQuery(array());
 		$query->filterType('trackerItem');
		$query->filterContent($input->trackerId->word(), 'tracker_id');

        $query->setRange(0, $prefs['unified_lucene_max_result']);

	//	if ($body = $input->filters->none()) {
			$builder = new Search_Query_WikiBuilder($query);
			$builder->apply(WikiParser_PluginMatcher::match($body));
		//}

		$result = $query->search($index);

		$response = array();

		$fields = array();
		if ($definition = Tracker_Definition::get($input->trackerId->int())) {
			/*foreach ($definition->getFields() as $fieldId) {
				
				if ($field = $definition->getField($fieldId)) {
					$fields[] = $field;
				    
				}
			} */
			$fields=$definition->getFields();
		}

		$smarty = TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_modifier_sefurl');
		$trklib = TikiLib::lib('trk');
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
				
			$response[] = $fieldsArr;
		}

	    	
		return $response;
	}

	
	
}

