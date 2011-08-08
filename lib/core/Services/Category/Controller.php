<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Category_Controller
{
	function setUp()
	{
		global $prefs;

		if ($prefs['feature_categories'] != 'y') {
			throw new Services_Exception_Disabled('feature_categories');
		}
	}

	function action_list_categories($input)
	{
		global $prefs;

		$parentId = $input->parentId->int();
		$descends = $input->descends->int();

		if (! $parentId) {
			throw new Services_Exception_MissingValue('parentId');
		}

		$categlib = TikiLib::lib('categ');
		return $categlib->get_viewable_child_categories($parentId, $descends);
	}
}

