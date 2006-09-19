<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_rss.php,v 1.16 2006-09-19 16:33:08 ohertel Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


$feeds=array('articles','directories','image_galleries','file_galleries','image_gallery','file_gallery', 'wiki','blogs','blog','forum','forums','mapfiles','tracker', 'trackers','calendar');
if (isset($_REQUEST["rss"])) {
	check_ticket('admin-inc-rss');

	foreach($feeds as $feed){
		$smarty->assign('max_rss_'.$feed, htmlentities(trim($_REQUEST['max_rss_'.$feed])));
		$tikilib->set_preference('max_rss_'.$feed, htmlentities(trim($_REQUEST['max_rss_'.$feed])));
		$smarty->assign('title_rss_'.$feed, htmlentities(trim($_REQUEST['title_rss_'.$feed])));
		$tikilib->set_preference('title_rss_'.$feed, htmlentities(trim($_REQUEST["title_rss_".$feed])));
		$smarty->assign('desc_rss_'.$feed, htmlentities(trim($_REQUEST['desc_rss_'.$feed])));
		$tikilib->set_preference('desc_rss_'.$feed, htmlentities(trim($_REQUEST["desc_rss_".$feed])));

		if (isset($_REQUEST["rss_".$feed]) && $_REQUEST["rss_".$feed] == "on") {
		  $tikilib->set_preference("rss_".$feed, 'y');
		  $smarty->assign('rss_'.$feed, 'y');
		} else {
		  $tikilib->set_preference("rss_".$feed, 'n');
		  $smarty->assign('rss_'.$feed, 'n');
		}
	}
	$tikilib->set_preference('rssfeed_default_version', $_REQUEST["rssfeed_default_version"]);
	$smarty->assign("rssfeed_default_version", $_REQUEST["rssfeed_default_version"]);
	$tikilib->set_preference('rssfeed_language', $_REQUEST["rssfeed_language"]);
	$smarty->assign("rssfeed_language", $_REQUEST["rssfeed_language"]);
	$tikilib->set_preference('rssfeed_editor', $_REQUEST["rssfeed_editor"]);
	$smarty->assign("rssfeed_editor", $_REQUEST["rssfeed_editor"]);
	$tikilib->set_preference('rssfeed_webmaster', $_REQUEST["rssfeed_webmaster"]);
	$smarty->assign("rssfeed_webmaster", $_REQUEST["rssfeed_webmaster"]);
	$tikilib->set_preference('rss_cache_time', (int) $_REQUEST["rss_cache_time"]);
	$smarty->assign("rss_cache_time", (int) $_REQUEST["rss_cache_time"]);
} else {
	foreach($feeds as $feed){
		$smarty->assign("max_rss_".$feed, $tikilib->get_preference("max_rss_".$feed, 10));
		$smarty->assign("title_rss_".$feed, $tikilib->get_preference("title_rss_".$feed));
		$smarty->assign("desc_rss_".$feed, $tikilib->get_preference("desc_rss_".$feed));
	}
	$smarty->assign("rssfeed_default_version", $tikilib->get_preference("rssfeed_default_version","2"));
	$smarty->assign("rssfeed_language", $tikilib->get_preference("rssfeed_language","en-us"));
	$smarty->assign("rssfeed_editor", $tikilib->get_preference("rssfeed_editor",""));
	$smarty->assign("rssfeed_webmaster", $tikilib->get_preference("rssfeed_webmaster",""));
	$smarty->assign("rss_cache_time", $tikilib->get_preference("rss_cache_time",0));
}
ask_ticket('admin-inc-rss');
?>
