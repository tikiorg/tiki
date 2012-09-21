<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
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

	$smarty->loadPlugin('smarty_function_button');
	$smarty->loadPlugin('smarty_function_service');
	return smarty_function_button(
		array(
			'_keepall' => 'y',
			'_class' => 'favorite-toggle',
			'href' => smarty_function_service(
				array(
					'controller' => 'favorite',
					'action' => 'toggle',
					'type' => $params['type'],
					'object' => $params['object'],
				),
				$smarty
			),
			'_text' => tr('Favorite'),
		),
		$smarty
	);
}

