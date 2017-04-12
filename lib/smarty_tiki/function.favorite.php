<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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

	$url = smarty_modifier_escape($url, 'url');
	$e_user = smarty_modifier_escape($user);

	if (isset($params['label'])){
		$label = $params['label'];
	}else{
		$label = tr('Favorite');
	}

	if (isset($params['button_classes'])){
		$button_classes= $params['button_classes'];
	}else{
		$button_classes = "btn btn-default";
	}

	return '<a class="'. $button_classes .' favorite-toggle" href="' . $url . '" data-key="favorite_' . $e_user . '"> ' . $label . '</a>';
}

