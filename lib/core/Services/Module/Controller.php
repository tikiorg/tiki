<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Module_Controller
{

	/**
	 * @param JitFilter $input
	 * @return array
	 */
	function action_execute($input)
	{
		$modlib = TikiLib::lib('mod');
		$result = '';

		$modname = $input->module->text();
		if ($modname) {
			$params = $input->isArray('params') ? $input->asArray('params') : [];

			$moduleId = $input->moduleId->int();

			if ($moduleId) {
				$module_reference = $modlib->get_assigned_module($moduleId);
				parse_str($module_reference['params'], $module_reference['params']);
				$module_reference['params'] = array_merge($params, $module_reference['params']);
			} else {
				$module_reference = [
					'name' => $modname,
					'params' => $params,
				];
			}


			$result = $modlib->execute_module($module_reference);
		}
		return ['html' => $result];
	}
}
