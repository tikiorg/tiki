<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @return array
 */
function module_comm_received_objects_info()
{
	return array(
		'name' => tra('Received Objects'),
		'description' => tra('Displays the number of pages received (via Communications).'),
		'prefs' => array("feature_comm"),
		'documentation' => 'Module comm_received_objects',
		'params' => array()
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_comm_received_objects($mod_reference, $module_params)
{
	$tikilib = TikiLib::lib('tiki');
	$smarty = TikiLib::lib('smarty');
	$ranking = $tikilib->list_received_pages(0, -1, 'pageName_asc');
	
	$smarty->assign('modReceivedPages', $ranking["cant"]);
}
