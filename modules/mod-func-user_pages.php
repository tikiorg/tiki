<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_user_pages_info() {
	return array(
		'name' => tra('User Pages'),
		'description' => tra('Displays to registered users the specified number of wiki pages which they were the last to edit.'),
		'prefs' => array( 'feature_wiki' ),
		'params' => array(),
		'common_params' => array("rows", "nonums")
	);
}

function module_user_pages( $mod_reference, $module_params ) {
	global $tikilib, $smarty, $user;
	if ($user) {
		$ranking = $tikilib->get_user_pages($user, $mod_reference["rows"]);
		
		$smarty->assign('modUserPages', $ranking);
		$smarty->assign('tpl_module_title', tra("My Pages"));
	}
}
