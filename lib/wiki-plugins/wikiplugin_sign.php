<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_sign_info()
{
	return array(
		'name' => tr('Signature'),
		'description' => tr('By writing {sign} in a wiki page, it will be converted to your username with a timestamp.'),
		'prefs' => array('wikiplugin_sign'),
		'tags' => array('basic'),
		'inline' => true,
		'format' => 'html',
		'params' => array(
			'user' => array(
				'required' => false,
				'name' => tr('User'),
				'description' => tr('Auto-generated, the username.'),
				'default' => '',
				'filter' => 'text',
				'advanced' => true,
			),
			'datetime' => array(
				'required' => false,
				'name' => tr('Date and time'),
				'description' => tr('Auto-generated, the timestamp'),
				'default' => '',
				'filter' => 'text',
				'advanced' => true,
			),
		),
	);
}

function wikiplugin_sign_rewrite($data, $params, $context)
{
	if (empty($params['user']) && empty($params['datetime'])) {
		global $user;
		$date = gmdate(DateTime::W3C);
		return "{sign user=\"$user\" datetime=\"$date\"}";
	}

	return false;
}

function wikiplugin_sign($data, $params)
{
	if (empty($params['datetime'])) {
		return false;
	}

	$user = isset($params['user']) ? $params['user'] : '';

	$time = strtotime($params['datetime']);

	if ($time === false) {
		return false;
	}

	$smarty = TikiLib::lib('smarty');
	$smarty->assign(
		'sign',
		array(
			'user' => $user,
			'datetime' => $params['datetime'],
			'time' => $time,
		)
	);
	return $smarty->fetch('wiki-plugins/wikiplugin_sign.tpl');
}

