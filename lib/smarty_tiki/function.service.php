<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function smarty_function_service($params, $smarty)
{
	$servicelib = TikiLib::lib('service');
	$smarty->loadPlugin('smarty_modifier_escape');

	if (! isset($params['controller'])) {
		return 'missing-controller';
	}

	if (isset($params['_params'])) {
		$params += $params['_params'];
		unset($params['_params']);
	}

	$url = $servicelib->getUrl($params);
	return smarty_modifier_escape($url);
}

