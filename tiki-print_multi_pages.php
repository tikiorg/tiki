<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/structures/structlib.php');

$access->check_feature('feature_wiki_multiprint');

if (!isset($_REQUEST['printpages']) && !isset($_REQUEST['printstructures'])) {
	$smarty->assign('msg', tra("No pages indicated"));
	$smarty->display("error.tpl");
	die;
} else {
	if (isset($_REQUEST['printpages'])) {
		$printpages = unserialize(urldecode($_REQUEST['printpages']));
	} else {
		$printpages = array();
	}
	if (isset($_REQUEST['printstructures'])) {
		$printstructures = unserialize(urldecode($_REQUEST['printstructures']));
	} else {
		$printstructures = array();
	}
}
if (isset($_REQUEST["print"]) || isset($_REQUEST["display"])) {
	check_ticket('multiprint');
	// Create XMLRPC object
	$pages = array();
	foreach($printpages as $page) {
		// If the page doesn't exist then display an error
		if (!$tikilib->page_exists($page)) {
			$smarty->assign('msg', tra("Page cannot be found"));
			$smarty->display("error.tpl");
			die;
		}
		// Now check permissions to access this page
		if (!$tikilib->user_has_perm_on_object($user, $page, 'wiki page', 'tiki_p_view')) {
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra("You do not have permission to view this page."));
			$smarty->display("error.tpl");
			die;
		}
		$pages[] = $tikilib->get_page_print_info($page);
	}
	foreach($printstructures as $structureId) {
		$struct = $structlib->get_subtree($structureId);
		foreach($struct as $struct_page) {
			global $page_ref_id;
			$page_ref_id = $struct_page['page_ref_id']; //to interpret {toc}
			if ($struct_page['pos'] != '' && $struct_page['last'] == 1) continue;
			$page_info = $tikilib->get_page_print_info($struct_page['pageName']);
			$page_info['pos'] = $struct_page['pos'];
			$page_info['h'] = empty($struct_page['pos']) ? 0 : count(explode('.', $struct_page['pos']));
			$h = $page_info['h'] + 5;
			if ($prefs['feature_page_title'] == 'y') {
				++$h;
			}
			$page_info['parsed'] = preg_replace("/<(\/?)h6/i", "<\\1h$h", $page_info['parsed']);
			--$h;
			$page_info['parsed'] = preg_replace("/<(\/?)h5/i", "<\\1h$h", $page_info['parsed']);
			--$h;
			$page_info['parsed'] = preg_replace("/<(\/?)h4/i", "<\\1h$h", $page_info['parsed']);
			--$h;
			$page_info['parsed'] = preg_replace("/<(\/?)h3/i", "<\\1h$h", $page_info['parsed']);
			--$h;
			$page_info['parsed'] = preg_replace("/<(\/?)h2/i", "<\\1h$h", $page_info['parsed']);
			--$h;
			$page_info['parsed'] = preg_replace("/<(\/?)h1/i", "<\\1h$h", $page_info['parsed']);
			--$h;
			$pages[] = $page_info;
		}
	}
}

$smarty->assign_by_ref('pages', $pages);
ask_ticket('multiprint');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('print_page', 'y');
if (isset($_REQUEST['display'])) $smarty->assign('display', $_REQUEST['display']);
// Allow PDF export by installing a Mod that define an appropriate function
if (isset($_REQUEST['display']) && $_REQUEST['display'] == 'pdf') {
	// Method using 'mozilla2ps' mod
	if (file_exists('lib/mozilla2ps/mod_urltopdf.php')) {
		include_once ('lib/mozilla2ps/mod_urltopdf.php');
		mod_urltopdf();
	}
} else {
	$smarty->display("tiki-print_multi_pages.tpl");
}
