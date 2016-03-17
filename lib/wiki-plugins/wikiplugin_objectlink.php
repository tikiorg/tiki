<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_objectlink_info()
{
	return array(
		'name' => tra('Object Link'),
		'description' => tra('Display a link to an object'),
		'prefs' => array('wikiplugin_objectlink'),
		'iconname' => 'link',
		'introduced' => 10,
		'tags' => array( 'basic' ),
		'format' => 'html',
		'inline' => true,
		'params' => array(
			'type' => array(
				'required' => true,
				'name' => tr('Type'),
				'description' => tr('The object type'),
				'since' => '10.0',
				'accepted' => 'wiki, user, external, relation_source, relation_target, freetag, trackeritem',
				'filter' => 'text',
				'type' => 'text',
			),
			'id' => array(
				'required' => true,
				'name' => tra('Object ID'),
				'description' => tra('The item to display'),
				'since' => '10.0',
				'filter' => 'text',
				'profile_reference' => 'type_in_param',
			),
		),
	);
}

function wikiplugin_objectlink($data, $params)
{
	$smarty = TikiLib::lib('smarty');
	$smarty->loadPlugin('smarty_function_object_link');

	return smarty_function_object_link(
		array(
			'type' => $params['type'],
			'id' => $params['id'],
		),
		$smarty
	);
}

