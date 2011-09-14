<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to err & die if called directly.
//smarty is not there - we need setup
require_once('tiki-setup.php');
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

global $prefs;
if ($prefs['feature_polls'] == 'y') {
	global $categlib, $polllib;
	#echo '<div>hier</div>';
	if (!is_object($categlib))  include_once('lib/categories/categlib.php');
	if (!is_object($polllib))  include_once('lib/polls/polllib.php');
	if (!isset($_REQUEST['poll_title'])) { $_REQUEST['poll_title'] = 'rate it!'; }

	if (isset($_REQUEST["poll_template"]) and $_REQUEST["poll_template"]) {
		$catObjectId = $categlib->is_categorized($cat_type, $cat_objid);
		if (!$catObjectId) {
			$catObjectId = $categlib->add_categorized_object($cat_type, $cat_objid, $cat_desc, $cat_name, $cat_href);
		}
		if ($polllib->has_object_polls($catObjectId) && $prefs['poll_multiple_per_object'] != 'y') {
			$polllib->remove_object_poll($cat_type, $cat_objid);
		}
		$pollid = $polllib->create_poll($_REQUEST["poll_template"], $cat_objid .': '. $_REQUEST['poll_title']);
		$polllib->poll_categorize($catObjectId, $pollid, $_REQUEST['poll_title']);
	} elseif (isset($_REQUEST["olpoll"]) and $_REQUEST["olpoll"]) {
		$catObjectId = $categlib->is_categorized($cat_type, $cat_objid);
		if (!$catObjectId) {
			$catObjectId = $categlib->add_categorized_object($cat_type, $cat_objid, $cat_desc, $cat_name, $cat_href);
		}
		$olpoll = $polllib->get_poll($_REQUEST["olpoll"]);
		$polllib->poll_categorize($catObjectId,$_REQUEST["olpoll"],	$olpoll['title']);
	}
}
