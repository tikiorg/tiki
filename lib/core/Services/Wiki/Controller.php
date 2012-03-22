<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
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
			$structure_info = $structlib->reorder_structure($data);

			$html = $structlib->get_toc($structure_info['structure_id'], 'asc', false, false, '', 'admin', '', 0, '');
		}
		return array('html' => $html);
	}

}

