<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_AutoSave_Controller
{
	function setUp()
	{
		Services_Exception_Disabled::check('feature_ajax');
		Services_Exception_Disabled::check('ajax_autosave');
	}

	function action_get($input)
	{
		include_once 'lib/ajax/autosave.php';
		$res = get_autosave($input->editor_id->text(), $input->referer->text());
		return array(
			'data' => $res,
		);
	}

	function action_save($input)
	{
		include_once 'lib/ajax/autosave.php';
		$data = $input->data->none();
		$res = auto_save($input->editor_id->text(), $data, $input->referer->text());

		return array(
			'data' => $res,
		);
	}

	function action_delete($input)
	{
		include_once 'lib/ajax/autosave.php';
		remove_save($input->editor_id->text(), $input->referer->text());

		return array();
	}
}

