<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_register_info()
{
	return array(
		'name' => tra('New User Registration'),
		'description' => tra('Permit anonymous visitors to create an account on the site.'),
		'prefs' => array('allowRegister'),
		'params' => array(),
	);
}

function module_register($mod_reference, $module_params)
{
	global $prefs, $smarty, $tikilib, $userlib, $reg_in_module, $registrationlib, $https_mode;
	$reg_in_module = true;
	$smarty->assign_by_ref('reg_in_module', $reg_in_module);
	include_once('tiki-register.php');
}
