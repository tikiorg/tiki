<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_ContentTemplate_Controller
{
	function setUp()
	{
		global $prefs;

		if ($prefs['feature_wiki_templates'] != 'y') {
			throw new Services_Exception_Disabled('feature_wiki_templates');
		}
	}
	
	function action_list($input)
	{
		// Validate access
		$access = TikiLib::lib('access');
		$access->check_permission('tiki_p_use_content_templates');
		
		// Load the templates library
		$templateslib =  TikiLib::lib('template');
		
		$section = 'wiki';
		$offset = 0;
		$maxRecords = -1;	
		$sort_mode = 'name_asc';
		$find = null;
		
		$contentTmpl = $templateslib->list_templates($section, $offset, $maxRecords, $sort_mode, $find);
		
		// Build the result		
		$result = array();
		$name = "";
		$content = "";
		foreach ($contentTmpl['data'] as $val) {
			if (count($contentTmpl) > 0) {
				$templateId = $val['templateId'];
				$templateData = $templateslib->get_template($templateId);
			
				$name = $templateData['name'];
				if (isset($templateData['content'])) {
					$content = $templateData['content'];
				}
			}
			$result[] = array('title' => $name,  'html'=> $content);
		}

		// Done
		return array(
			'data' => $result,
			'cant' => count($result),
			);
	}
}

