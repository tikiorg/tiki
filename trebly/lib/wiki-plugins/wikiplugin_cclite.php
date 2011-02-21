<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * General purpose cclite utility plugin
 * To perform transaction list and summary     
 */
function wikiplugin_cclite_info() {
	global $prefs;

	return array(
		'name' => tra('Cclite'),
		'documentation' => 'PluginCclite',
		'description' => tra('General purpose cclite utility plugin'),
//		'validate' => '',
		'prefs' => array( 'wikiplugin_cclite', 'payment_feature' ),
		'icon' => 'pics/icons/money.png',
		'params' => array(
			'mode' => array(
				'required' => false,
				'name' => tra('Mode'),
				'description' => tr('Mode of operation - summary or recent. Default: summary'),
				'filter' => 'text',
				'default' => 'summary',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Account summary'), 'value' => 'summary'), 
					array('text' => tra('Recent transactions'), 'value' => 'recent'), 
				),
			),
			'registry' => array(
				'required' => false,
				'name' => tra('Registry'),
				'description' => tr('Registry to query. Default: site preference (or first in list when more than one)'),
				'filter' => 'text',
				'default' => '',
			),
		),
	);
}

function wikiplugin_cclite( $data, $params, $offset ) {
	global $smarty, $userlib, $prefs, $user, $headerlib;
	//global $paymentlib; require_once 'lib/payment/paymentlib.php';
	global $cclitelib;  require_once 'lib/payment/cclitelib.php';
	
	if (empty($user)) {
		return '{REMARKSBOX(type=note, title=Cclite)}' . tra('You need to be logged in to view this information.')
				. '{REMARKSBOX}';
	}
	
	$default = array( 'mode'=>'summary', 'registry' => '' );
	if (is_array($default['registry']) && !empty($default['registry'])) {
		$default['registry'] = $default['registry'][0];
	}
	$params = array_merge( $default, $params );
	
	switch ($params['mode']) {
		case 'recent':
			$result = $cclitelib->cclite_send_request('recent');
			break;
		case 'summary':
		default:
			$result = $cclitelib->cclite_send_request('summary');
			break;
			
	}
	//$r = $cclitelib->cclite_send_request('logoff');
	$result = 'In development...<br />' . strip_tags($result);
	$smarty->assign( 'wp_cclite_result', $result );
	return '~np~' . $smarty->fetch( 'wiki-plugins/wikiplugin_cclite.tpl' ) . '~/np~';
}

