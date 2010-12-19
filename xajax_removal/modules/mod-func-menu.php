<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mod-func-login_box.php 26808 2010-04-28 12:30:41Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_menu_info() {
	return array(
		'name' => tra('Menu'),
		'description' => tra('Horizonatal or vertical menu.'),
		'prefs' => array(),
		'params' => array(
			'id' => array(
				'name' => tra('Menu Id'),
				'required' => true,
				'description' => tra('Id from tiki-admin_menus.php'),
				'filter' => 'int',
			),
			'type' => array(
				'name' => tra('Type'),
				'description' => tra('Direction for menu: horiz or vert (default vert)'),
			),
			'css' => array(
				'name' => tra('CSS/Superfish'),
				'description' => tra('Use CSS Superfish menu. y|n (default y)'),
				'filter' => 'alpha',
			),
		)
	);
}

function module_menu( $mod_reference, &$module_params ) {
	$module_params['css'] = empty($module_params['css']) ? 'y' : $module_params['css'];
}
