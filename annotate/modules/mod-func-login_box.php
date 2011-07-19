<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_login_box_info() {
	return array(
		'name' => tra('Log In'),
		'description' => tra('Log-in box'),
		'prefs' => array(),
		'documentation' => 'Module login_box',
		'params' => array(
			'input_size' => array(
				'name' => tra('Input size'),
				'description' => tra('Number of characters for username and password input fields.'),
				'filter' => 'int'
			),
			'mode' => array(
				'name' => tra('Mode'),
				'description' => tra('Display mode: module, header or popup. Leave empty for module mode'),
			),
			'register' => array(
				'name' => tra('Show Register'),
				'description' => tra('Show the register link') . ' (y/n)',
				'filter' => 'alpha',
			),
			'forgot' => array(
				'name' => tra('Show I Forgot'),
				'description' => tra('Show the "I forgot my password" link') . ' (y/n)',
				'filter' => 'alpha',
			),
			'remember' => array(
				'name' => tra('Show Remember me'),
				'description' => tra('Show the "Remember me" checkbox') . ' (y/n)',
				'filter' => 'alpha',
			),
		)
	);
}

function module_login_box( $mod_reference, &$module_params ) {
	global $smarty, $prefs;
	
	static $module_logo_instance = 0;
	
	$module_logo_instance++;
	
	$smarty->assign('module_logo_instance', $module_logo_instance);
	$smarty->assign('mode', isset($module_params['mode']) ? $module_params['mode'] : 'module');
	
	$smarty->assign('registration', 'n');	// stops the openid form appearing in the module, only on tiki-login_scr.php
	
	if ($prefs['allowRegister'] === 'y' && (empty($module_params['register']) || $module_params['register'] === 'y')) {
		$module_params['show_register'] = 'y';
	} else {
		$module_params['show_register'] = 'n';
	}
	if ($prefs['forgotPass'] === 'y' && $prefs['change_password'] === 'y' && (empty($module_params['forgot']) || $module_params['forgot'] === 'y')) {
		$module_params['show_forgot'] = 'y';
	} else {
		$module_params['show_forgot'] = 'n';
	}
}
