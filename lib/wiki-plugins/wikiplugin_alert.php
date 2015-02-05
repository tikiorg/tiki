<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_alert_info()
{
	return array(
		'name' => tra('Dismissable Bootstrap Alert'),
		'description' => tra('Sets a dismissable bootstrap alert'),
		'default' => 'y',
		'format' => 'html',
		'filter' => 'wikicontent',
		'tags' => array('advanced'),
		'params' => array(
			'type' => array(
				'name' => tr('Alert Type'),
				'description' => tr('Bootstrap type for alert (primary, success, etc.)'),
				'required' => false,
				'filter' => 'text'
			),
			'dismissable' => array(
				'name' => tr('Sets whether alert is dismissable'),
				'description' => tr('Should be y/n depending on if alert is dismissable. Default n.'),
				'required' => false,
				'filter' => 'text'
			),
			'store_cookie' => array(
				'name' => tr('Sets whether alert is dismissable'),
				'description' => tr('Should be y/n depending on whether we want to store a cookie after alert is dismissed. Default n.'),
				'required' => false,
				'filter' => 'text'
			),
			'id' => array(
				'name' => tr('Sets the id for the alert'),
				'description' => tr('Sets an HTML id for the account. This is used for cookie purposes as well.'),
				'required' => false,
				'filter' => 'text'
			),
			'version' => array(
				'name' => tr('Sets a version for the alert for cookie purposes'),
				'description' => tr('Sets a version for the alert. If new version, the alert should show up again even if it was previously dismissed'),
				'required' => false,
				'filter' => 'text'
			),
		),
	);
}

function wikiplugin_alert($data, $params)
{
	global $smarty,$user;
	$userlib = TikiLib::lib('user');
	if ($params['dismissable']=='y' && $params['store_cookie']=='y'){
		$smarty->assign('dismissable', $params['dismissable']);
		$smarty->assign('cookie_hash', $cookiehash);
	}
	
	if (isset($params['type'])){
		$type = $params['type'];
	}else{
		$type = 'info';
	}

	$smarty->assign('user', $user);
	$smarty->assign('type', $type);
	$smarty->assign('id', $params['id']);
	$smarty->assign('version', $params['version']);
	$smarty->assign('contents', $data);

	return $smarty->fetch('templates/wiki-plugins/wikiplugin_alert.tpl');
}
