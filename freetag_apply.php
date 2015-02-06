<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to err & die if called directly.
//smarty is not there - we need setup
require_once('tiki-setup.php');	
$access = TikiLib::lib('access');
$access->check_script($_SERVER["SCRIPT_NAME"], basename(__FILE__));

global $prefs;
global $tiki_p_freetags_tag;

if ($prefs['feature_freetags'] == 'y' and $tiki_p_freetags_tag == 'y') {

	$freetaglib = TikiLib::lib('freetag');

	if (isset($_REQUEST['freetag_string'])) {	 		 
		$tag_string = $_REQUEST['freetag_string'];
	} else {
		$tag_string = '';
	}	 

	global $user;

	if (!isset($cat_desc)) $cat_desc = '';
	if (!isset($cat_name)) $cat_name = '';
	if (!isset($cat_href)) $cat_href = '';
	if (!isset($cat_lang)) $cat_lang = null;

	$freetaglib->add_object($cat_type, $cat_objid, FALSE, $cat_desc, $cat_name, $cat_href);	
	$freetaglib->update_tags($user, $cat_objid, $cat_type, $tag_string, false, $cat_lang);
	require_once 'lib/search/refresh-functions.php';
	refresh_index($cat_type, $cat_objid);
}
