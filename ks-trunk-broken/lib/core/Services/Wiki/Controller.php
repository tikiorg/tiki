<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Wiki_Controller
{
	function setUp()
	{
		global $prefs;

		if ($prefs['feature_wiki'] !== 'y') {
			throw new Services_Exception(tr('Feature disabled'), 403);
		}
		if ($prefs['feature_wiki_structure'] !== 'y') {
			throw new Services_Exception(tr('Feature disabled'), 403);
		}
	}

	function action_save_structure($input)
	{
		$rc = false;
		$data = json_decode($input->data->text());
		if ($data) {
			global $structlib; include_once('lib/structures/structlib.php');
			$structlib->reorder_structure($data);
			$params = json_decode($input->params->text());

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
		}
		return array('html' => $html);
	}

}

