<?php 
// $Header: /cvsroot/tikiwiki/tiki/poll_categorize.php,v 1.2 2005-01-22 22:54:52 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== FALSE) {
  //smarty is not there - we need setup
  require_once('tiki-setup.php');
  $smarty->assign('msg',tra("This script cannot be called directly"));
  $smarty->display("error.tpl");
  die;
}

global $feature_categories;
global $feature_polls;

if ($feature_categories == 'y' and $feature_polls == 'y') {
	global $categlib, $polllib;
	if (!is_object($categlib))  include_once('lib/categories/categlib.php');
	if (!is_object($polllib))  include_once('lib/polls/polllib.php');
	if (!isset($_REQUEST['poll_title'])) { $_REQUEST['poll_title'] = 'rate it!'; }

	if (isset($_REQUEST["poll_template"]) and $_REQUEST["poll_template"]) {
		$catObjectId = $categlib->is_categorized($cat_type, $cat_objid);
		if (!$catObjectId) {
			$catObjectId = $categlib->add_categorized_object($cat_type, $cat_objid, $cat_desc, $cat_name, $cat_href);
		}
		if ($polllib->has_object_polls($catObjectId)) {
			$polllib->remove_object_poll($catObjectId);	
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
?>
