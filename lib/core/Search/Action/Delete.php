<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Action_Delete implements Search_Action_Action
{
	function getValues()
	{
		return array(
			'object_type' => true,
			'object_id' => true,
		);
	}

	function validate(JitFilter $data)
	{
		$object_type = $data->object_type->text();

		if ($object_type != 'file') {
			return false;
		}

		return true;
	}

	function execute(JitFilter $data)
	{
		$object_type = $data->object_type->text();

		switch ($object_type) {
		case 'file':
			$fileId = $data->object_id->int();
			$filegallib = TikiLib::lib('filegal');
			$info = $filegallib->get_file_info($fileId);

			if (! $info) {
				return false;
			}

			$filegallib->remove_file($info);

			break;
		default:
			return false;
		}

		return true;
	}
}

