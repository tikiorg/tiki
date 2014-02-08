<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Controller.php 46965 2013-08-02 19:05:59Z jonnybradley $

class Services_Module_Controller
{

	function action_execute($input)
	{
		$modlib = TikiLib::lib('mod');

		$modname = $input->module->text();
		if ($modname) {
			$params = $input->params->array();

			$params = array_merge($params, array('nobox' => 'y'));

			$module_reference = array(
				'name' => $modname,
				'params' => $params,
			);

			$result = $modlib->execute_module($module_reference);
		}
		return array('html' => $result);
	}

}

