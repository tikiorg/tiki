<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'wiki page';
require_once ('tiki-setup.php');
include_once ('lib/structures/structlib.php');
include_once ('lib/categories/categlib.php');
include_once ("lib/ziplib.php");
$access->check_feature(array('feature_wiki', 'feature_wiki_structure'));
$access->check_permission('tiki_p_view');

// start security hardened section
if ($tiki_p_edit_structures == 'y') {
	if (isset($_REQUEST['rremove'])) {
		$structure_info = $structlib->s_get_structure_info($_REQUEST['rremove']);
		if (!$tikilib->user_has_perm_on_object($user, $structure_info["pageName"], 'wiki page', 'tiki_p_edit')) {
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra("You do not have permission to edit this page."));
			$smarty->display("error.tpl");
			die;
		}
		$access->check_authenticity();
		$structlib->s_remove_page($_REQUEST["rremove"], false, empty($_REQUEST['page']) ? '' : $_REQUEST['page']);
	}
	if (isset($_REQUEST['rremovex'])) {
		$structure_info = $structlib->s_get_structure_info($_REQUEST['rremovex']);
		if (!$tikilib->user_has_perm_on_object($user, $structure_info["pageName"], 'wiki page', 'tiki_p_edit')) {
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra("You do not have permission to edit this page."));
			$smarty->display("error.tpl");
			die;
		}
		$access->check_authenticity();
		$structlib->s_remove_page($_REQUEST["rremovex"], true, empty($_REQUEST['page']) ? '' : $_REQUEST['page']);
	}
	if (isset($_REQUEST['export'])) {
		check_ticket('admin-structures');
		$structure_info = $structlib->s_get_structure_info($_REQUEST['export']);
		if ($prefs['feature_wiki_export'] != 'y' || $tiki_p_admin_wiki != 'y' || !$tikilib->user_has_perm_on_object($user, $structure_info["pageName"], 'wiki page', 'tiki_p_view')) {
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra('You do not have permission to view this page.'));
			$smarty->display("error.tpl");
			die;
		}
		$structlib->s_export_structure($_REQUEST['export']);
	}
	if (isset($_REQUEST['zip']) && $tiki_p_admin == 'y') {
		check_ticket('admin-structures');
		include_once ('lib/wiki/xmllib.php');
		$xmllib = new XmlLib;
		$zipFile = 'dump/xml.zip';
		$config['debug'] = false;
		if ($xmllib->export_pages(null, $_REQUEST['zip'], $zipFile, $config)) {
			if (!$config['debug']) {
				header("location: $zipFile");
				die;
			}
		} else {
			$smarty->assign('error', $xmllib->get_error());
		}
	}
	if (isset($_REQUEST['export_tree'])) {
		check_ticket('admin-structures');
		$structure_info = $structlib->s_get_structure_info($_REQUEST['export_tree']);
		if (!$tikilib->user_has_perm_on_object($user, $structure_info["pageName"], 'wiki page', 'tiki_p_view')) {
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra('You do not have permission to view this page.'));
			$smarty->display("error.tpl");
			die;
		}
		header("content-type: text/plain");
		$structlib->s_export_structure_tree($_REQUEST['export_tree']);
		die;
	}
	if (isset($_REQUEST['batchaction'])) {
		check_ticket('admin-structures');
		foreach($_REQUEST['action'] as $batchid) {
			$structure_info = $structlib->s_get_structure_info($batchid);
			if (!$tikilib->user_has_perm_on_object($user, $structure_info['pageName'], 'wiki page', 'tiki_p_edit')) {
				continue;
			}
			if ($_REQUEST['batchaction'] == 'delete') {
				$structlib->s_remove_page($batchid, false, $structure_info['pageName']);
			} elseif ($_REQUEST['batchaction'] == 'delete_with_page') {
				$structlib->s_remove_page($batchid, true, $structure_info['pageName']);
			}
		}
	}
	$smarty->assign('askremove', 'n');
	if (isset($_REQUEST['remove'])) {
		check_ticket('admin-structures');
		$structure_info = $structlib->s_get_structure_info($_REQUEST['remove']);
		if (!$tikilib->user_has_perm_on_object($user, $structure_info["pageName"], 'wiki page', 'tiki_p_edit')) {
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra("You do not have permission to edit this page."));
			$smarty->display("error.tpl");
			die;
		}
		$smarty->assign('askremove', 'y');
		$smarty->assign('removename', $structure_info["pageName"]);
		$smarty->assign('remove', $_REQUEST['remove']);
	}
	$alert_in_st = array();
	$alert_categorized = array();
	$alert_to_remove_cats = array();
	$alert_to_remove_extra_cats = array();
	$cat_type = 'wiki page';
	$cat_objid = '';
	$smarty->assign('just_created', 'n');
	if (isset($_REQUEST["create"])) {
		check_ticket('admin-structures');
		if ((empty($_REQUEST['name']))) {
			$smarty->assign('msg', tra("You must specify a page name, it will be created if it doesn't exist."));
			$smarty->display("error.tpl");
			die;
		}
		//try to add a new structure
		$structure_id = $structlib->s_create_page(null, null, $_REQUEST['name'], $_REQUEST['alias'], null);
		//Cannot create a structure if a structure already exists
		if (!isset($structure_id)) {
			$smarty->assign('msg', $_REQUEST['name'] . " " . tra("page not added (Exists)"));
			$smarty->display("error.tpl");
			die;
		}
		$cat_name = $_REQUEST['name'];
		$cat_objid = $cat_name;
		$cat_href = "tiki-index.php?page=" . urlencode($cat_name);
		$cat_desc = '';
		$cat_type = 'wiki page';
		include_once ("categorize.php");
		$categories = array(); // needed to prevent double entering (the first time when page is being categorized in categorize.php)
		include_once ("categorize_list.php"); // needs to be up here to avoid picking up selection of cats from other existing sub-pages
		$smarty->assign('just_created', $structure_id);
		$smarty->assign('just_created_name', $_REQUEST['name']);
		$parents[0] = $structure_id;
		$last_pages[0] = null;
		$tree_lines = explode("\n", $_REQUEST["tree"]);
		foreach($tree_lines as $full_line) {
			$names = explode("->", $full_line);
			$line = $names[0];
			$line = rtrim($line);
			// count the depth level (leading spaces indicate it)
			$tabs = strlen($line) - strlen(ltrim($line));
			// Is there smth else 'cept spaces?
			if (strlen($line = trim($line))) {
				$parent_id = $parents[$tabs];
				if (isset($last_pages[$tabs])) {
					$last_page = $last_pages[$tabs];
				} else {
					$last_page = null;
				}
				$alias = '';
				if (!empty($names[1])) {
					$alias = $names[1];
				}
				if ($tikilib->page_exists(trim($line))) {
					$strucs = $structlib->get_page_structures(trim($line));
					if (count($strucs) > 0) {
						$alert_in_st[] = trim($line);
					}
				}
				$new_page_ref_id = $structlib->s_create_page($parent_id, $last_page, trim($line) , trim($alias), $structure_id);
				if (isset($new_page_ref_id)) {
					$parents[$tabs + 1] = $new_page_ref_id;
					$last_pages[$tabs] = $new_page_ref_id;
					$cat_name = trim($line);
					$cat_objid = $cat_name;
					$cat_href = "tiki-index.php?page=" . urlencode($cat_name);
					$catObjectId = $categlib->is_categorized($cat_type, $cat_objid);
					if ($prefs['feature_wiki_categorize_structure'] == 'y' && !$catObjectId) {
						// page that is added is not categorized -> categorize it if necessary
						if (isset($_REQUEST["cat_categorize"]) && $_REQUEST["cat_categorize"] == 'on' && isset($_REQUEST["cat_categories"])) {
							$catObjectId = $categlib->add_categorized_object($cat_type, $cat_objid, $cat_desc, $cat_name, $cat_href);
							$alert_categorized[] = $cat_name;
							foreach($_REQUEST["cat_categories"] as $cat_acat) {
								$categlib->categorize($catObjectId, $cat_acat);
							}
						}
					} elseif ($prefs['feature_wiki_categorize_structure'] == 'y') {
						// page that is added is categorized
						if (!isset($_REQUEST["cat_categories"]) || !isset($_REQUEST["cat_categorize"]) || isset($_REQUEST["cat_categorize"]) && $_REQUEST["cat_categorize"] != 'on') {
							// alert that current pages are categorized
							$alert_to_remove_cats[] = $cat_name;
						} else {
							// add categories and alert that current pages have different categories
							$cats = $categlib->get_object_categories($cat_type, $cat_objid);
							$numberofcats = count($cats);
							$alert_categorized[] = $cat_name;
							foreach($_REQUEST["cat_categories"] as $cat_acat) {
								if (!in_array($cat_acat, $cats, true)) {
									$categlib->categorize($catObjectId, $cat_acat);
									$numberofcats+= 1;
								}
							}
							if ($numberofcats > count($_REQUEST["cat_categories"])) {
								$alert_to_remove_extra_cats[] = $cat_name;
							}
						}
					}
				}
			}
		}
	}
	$smarty->assign('alert_in_st', $alert_in_st);
	$smarty->assign('alert_categorized', $alert_categorized);
	$smarty->assign('alert_to_remove_cats', $alert_to_remove_cats);
	$smarty->assign('alert_to_remove_extra_cats', $alert_to_remove_extra_cats);
} // end of security hardening
if ($prefs['feature_categories'] == 'y') {
	include_once ("categorize_list.php");
}
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'pageName_asc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
$smarty->assign_by_ref('offset', $offset);
if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign('find', $find);
$smarty->assign_by_ref('sort_mode', $sort_mode);
// default $maxRecords defined in tiki-setup.php
if (isset($_REQUEST['maxRecords'])) {
	$maxRecords = $_REQUEST['maxRecords'];
}
$filter = '';
if (!empty($_REQUEST['lang'])) {
	$filter['lang'] = $_REQUEST['lang'];
	$smarty->assign_by_ref('find_lang', $_REQUEST['lang']);
}
if (!empty($_REQUEST['categId'])) {
	$filter['categId'] = $_REQUEST['categId'];
	$smarty->assign_by_ref('find_categId', $_REQUEST['categId']);
}
if (isset($_REQUEST["exact_match"])) {
	$exact_match = true;
	$smarty->assign('exact_match', 'y');
} else {
	$exact_match = false;
	$smarty->assign('exact_match', 'n');
}
if ($prefs['feature_multilingual'] == 'y') {
	$languages = array();
	$languages = $tikilib->list_languages(false, 'y');
	$smarty->assign_by_ref('languages', $languages);
}
$channels = $structlib->list_structures($offset, $maxRecords, $sort_mode, $find, $exact_match, $filter);
$smarty->assign('cant', $channels["cant"]);
$smarty->assign_by_ref('channels', $channels["data"]);
ask_ticket('admin-structures');
include_once ('tiki-section_options.php');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Detect if we have a PDF export mod installed
$smarty->assign('pdf_export', file_exists('lib/mozilla2ps/mod_urltopdf.php') ? 'y' : 'n');
// Display the template
$smarty->assign('mid', 'tiki-admin_structures.tpl');
$smarty->display("tiki.tpl");
