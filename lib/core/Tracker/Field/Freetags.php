<?php

/**
 * Handler class for Freetags
 * 
 * Letter key: ~F~
 *
 */
class Tracker_Field_Freetags extends Tracker_Field_Abstract
{
	function getFieldData(array $requestData = array())
	{	
		$data = array();
		
		$ins_id = $this->getInsertId();
		
		if (isset($requestData[$ins_id])) {
			$data['value'] = $requestData[$ins_id];
		} else {
			global $prefs;
			
			$data['value'] = $this->getValue();
			
			$freetaglib = TikiLib::lib('freetag');
			$data['freetags'] = $freetaglib->_parse_tag($data['value']);
			$data['tag_suggestion'] = $freetaglib->get_tag_suggestion(
				implode(' ', $data['freetags']),
				$prefs['freetags_browse_amount_tags_suggestion']
			);	
		}
					
		return $data;
	}

	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/freetags.tpl', $context);
	}
	
	function renderOutput($context = array())
	{
		return $this->renderTemplate('trackeroutput/freetags.tpl', $context);
	}
}