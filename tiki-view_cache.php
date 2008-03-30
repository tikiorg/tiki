<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-view_cache.php,v 1.14 2007-10-12 07:55:33 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

/*
if($prefs['feature_listPages'] != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("error.tpl");
  die;  
}
*/
if (isset($_REQUEST['url'])) {
	$id = $tikilib->get_cache_id($_REQUEST['url']);

	if (!$id) {
		$smarty->assign('msg', tra("No cache information available"));

		$smarty->display("error.tpl");
		die;
	}

	$_REQUEST["cacheId"] = $id;
}

if (!isset($_REQUEST["cacheId"])) {
	$smarty->assign('msg', tra("No page indicated"));

	$smarty->display("error.tpl");
	die;
}

// Get a list of last changes to the Wiki database
$info = $tikilib->get_cache($_REQUEST["cacheId"]);
$ggcacheurl = 'http://google.com/search?q=cache:'.urlencode(strstr($info['url'],'http://'));

// test if url ends with .txt : formatting for text
if (substr($info["url"], -4, 4) == ".txt") {
	$info["data"] = "<pre>" . $info["data"] . "</pre>";
}

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('ggcacheurl', $ggcacheurl);
$smarty->assign_by_ref('info', $info);
$smarty->assign('mid', 'tiki-view_cache.tpl');
$smarty->display('tiki-view_cache.tpl');

?>