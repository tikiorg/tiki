<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_votings_info()
{
	return array(
		'name' => tra('Votings'),
		'documentation' => 'PluginVotings',
		'description' => tra('Saves voting information in Smarty variables for display'),
		'prefs' => array( 'wikiplugin_votings' ),	
		'format' => 'html',
		'iconname' => 'thumbs-up',
		'introduced' => 8,
		'params' => array(
			'objectkey' => array(
				'required' => true,
				'name' => tra('Object Key'),
				'description' => tra('Object key that is used to record votes'),
				'since' => '8.0',
				'filter' => 'text',
				'default' => '',
			),
			'returnval' => array(
				'required' => false,
				'name' => tra('Return value'),
				'description' => tra('Value to display as output of plugin'),
				'since' => '8.0',
				'filter' => 'text',
				'default' => '',
			),
		)
	);
}
function wikiplugin_votings($data, $params)
{
	global $user;
	if (!isset($params['objectkey'])) {
		return '';
	} else {
		$key = $params['objectkey'];
	}
	$smarty = TikiLib::lib('smarty');
	$votings = TikiDb::get()->table('tiki_user_votings');

	$data = $votings->fetchRow(array('count' => $votings->count(), 'total' => $votings->sum('optionId')), array('id' => $key));

	$result = $votings->fetchAll(array('user'), array('id' => $key));

	foreach ($result as $res) {
		$field['users'][] = $res['user'];
	}	

	$field['numvotes'] = $data['count'];
	$field['total'] = $data['total'];

	if ($field['numvotes']) {
		$field['voteavg'] = $field['total'] / $field['numvotes'];
	} else {
		$field['voteavg'] = 0;
	}
	// be careful optionId is the value - not the optionId
	$field['my_rate'] = $votings->fetchOne('optionId', array('id' => $key, 'user' => $user));	

	$smarty->assign('wp_votings', $field); 

	if (!empty($params['returnval']) && isset($field[$params['returnval']])) {
		return $field[$params['returnval']];
	} else {
		return '';
	}
}						   
