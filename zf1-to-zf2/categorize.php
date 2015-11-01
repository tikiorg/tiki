<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');
$access = TikiLib::lib('access');
$access->check_script($_SERVER["SCRIPT_NAME"], basename(__FILE__));
$smarty = TikiLib::lib('smarty');

global $prefs;

$catobjperms = Perms::get(array( 'type' => $cat_type, 'object' => $cat_objid ));

if ($prefs['feature_categories'] == 'y' && $catobjperms->modify_object_categories ) {
	$categlib = TikiLib::lib('categ');

	if (isset($_REQUEST['import']) and isset($_REQUEST['categories'])) {
		$_REQUEST["cat_categories"] = explode(',', $_REQUEST['categories']);
		$_REQUEST["cat_categorize"] = 'on';
	}

	if ( !isset($_REQUEST["cat_categorize"]) || $_REQUEST["cat_categorize"] != 'on' ) {
		$_REQUEST['cat_categories'] = NULL;
	}
	$categlib->update_object_categories(isset($_REQUEST['cat_categories'])?$_REQUEST['cat_categories']:'', $cat_objid, $cat_type, $cat_desc, $cat_name, $cat_href, $_REQUEST['cat_managed']);
}
