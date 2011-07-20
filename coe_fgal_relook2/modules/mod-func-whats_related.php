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

function module_whats_related_info() {
	return array(
		'name' => tra('Related Items'),
		'description' => tra('Lists objects which share a category with the viewed object.'),
		'prefs' => array(),
		'params' => array()
	);
}

function module_whats_related( $mod_reference, $module_params ) {
	global $smarty;
	global $categlib; require_once ('lib/categories/categlib.php');
	
	$WhatsRelated=$categlib->get_link_related($_SERVER["REQUEST_URI"]);
	$smarty->assign_by_ref('WhatsRelated', $WhatsRelated);
}
