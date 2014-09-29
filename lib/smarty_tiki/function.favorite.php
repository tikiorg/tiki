<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function smarty_function_favorite($params, $smarty)
{
	global $prefs, $user;

	// Disabled, do nothing
	if (empty($user) || $prefs['user_favorites'] != 'y') {
		return;
	}

	$servicelib = TikiLib::lib('service');
	$smarty = TikiLib::lib('smarty');
	$smarty->loadPlugin('smarty_modifier_escape');

	$url = $servicelib->getUrl(array(
		'controller' => 'favorite',
		'action' => 'toggle',
		'type' => $params['type'],
		'object' => $params['object'],
	));

	$url = smarty_modifier_escape($url);
	$e_user = smarty_modifier_escape($user);

	return '<a class="btn btn-default favorite-toggle" href="' . $url . '" data-key="favorite_' . $e_user . '">' . tr('Favorite') . '</a>';
}

