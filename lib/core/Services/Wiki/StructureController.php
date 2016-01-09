<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Controller.php 55927 2015-07-26 16:48:44Z jonnybradley $

class Services_Wiki_StructureController
{
	function setUp()
	{
		Services_Exception_Disabled::check('feature_wiki');
		Services_Exception_Disabled::check('feature_wiki_structure');
	}

	function action_save_structure($input)
	{
		$data = json_decode($input->data->text());
		if ($data) {
			$structlib = TikiLib::lib('struct');
			$structlib->reorder_structure($data);
			$params = json_decode($input->params->text());

			$_GET = array();		// self_link and query objects used by get_toc adds all this request data to the action links
			$_POST = array();

			$html = $structlib->get_toc(
				$params->page_ref_id,
				$params->order,
				$params->showdesc,
				$params->numbering,
				$params->numberPrefix,
				$params->type,
				$params->page,
				$params->maxdepth,
				$params->structurePageName
			);
			
			//Empty structure caches to refresh structure data in menu module. Seems better to empty cache for any possible subnodes, might make it a bit slow
			$cachelib = TikiLib::lib('cache');
			$structurePages = array();
			$structurePages = $structlib->s_get_structure_pages($params->page_ref_id);
			foreach($structurePages as &$value) {
				$cachetype = 'structure_'.$value["page_ref_id"].'_';
				$cachelib->empty_type_cache($cachetype);
			}
			unset($value);
		}
		return array('html' => $html);
	}
}

