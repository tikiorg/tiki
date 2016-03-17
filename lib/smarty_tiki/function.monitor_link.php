<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function smarty_function_monitor_link($params)
{
	global $user, $prefs;

	if ($prefs['monitor_enabled'] != 'y') {
		return;
	}

	if (! isset($params['type']) || ! isset($params['object'])) {
		return tr('Missing parameter.');
	}

	if (! $user) {
		return '';
	}

	$servicelib = TikiLib::lib('service');

	$smarty = TikiLib::lib('smarty');
	$smarty->assign('monitor_link', $params);
	return $smarty->fetch('monitor/link.tpl');
}

