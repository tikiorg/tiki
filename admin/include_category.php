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
if (!empty($_REQUEST['assignWikiCategories']) && $prefs['category_defaults'] && $access->ticketMatch()) {
	$categlib = TikiLib::lib('categ');
	$maxRecords = 100;
	// The outer loop attempts to limit memory usage by fetching pages gradually.
	for ($offset = 0; $pages = $tikilib->list_pages($offset, $maxRecords), !empty($pages['data']); $offset += $maxRecords) {
		foreach ($pages['data'] as $page) {
			$categories = $categlib->get_object_categories('wiki page', $page['pageName']);
			$page['href'] = "tiki-index.php?page=" . urlencode($page['pageName']);
			$categlib->update_object_categories($categories, $page['pageName'], 'wiki page', $page['description'], $page['pageName'], $page['href']);
		}
	}
	add_feedback('category_defaults', tr('category defaults'), 2);
}