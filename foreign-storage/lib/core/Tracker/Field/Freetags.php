<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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
