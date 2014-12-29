<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function smarty_function_service_inline($params, $smarty)
{
	$servicelib = TikiLib::lib('service');

	if (! isset($params['controller'])) {
		return 'missing-controller';
	}
	if (! isset($params['action'])) {
		return 'missing-action';
	}
	$controller = $params['controller'];
	$action = $params['action'];
	unset($params['controller']);
	unset($params['action']);

	try {
		return $servicelib->render($controller, $action, $params);
	} catch (Services_Exception $e) {
		if (empty($params['_silent'])) {
			$smarty->loadPlugin('smarty_block_remarksbox');
			$repeat = false;
			return smarty_block_remarksbox(['type' => 'warning', 'title' => tr('Unavailable')], $e->getMessage(), $smarty, $repeat);
		}
	}
}

