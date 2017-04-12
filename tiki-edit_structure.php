<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'wiki page';
$auto_query_args = array('page_ref_id');
require_once ('tiki-setup.php');

$structlib = TikiLib::lib('struct');
$access->check_feature(array('feature_wiki','feature_wiki_structure'));
if (!isset($_REQUEST["page_ref_id"])) {
	$smarty->assign('msg', tra("No structure indicated"));
	$smarty->display("error.tpl");
	die;
}

$page_info = $structlib->s_get_page_info($_REQUEST["page_ref_id"]);

$access->check_permission('tiki_p_view', tra('View this wiki page'), 'wiki page', $page_info['pageName']);

if (isset($_REQUEST['move_to'])) {
	check_ticket('edit-structure');
	$structlib->move_to_structure($_REQUEST['page_ref_id'], $_REQUEST['structure_id'], $_REQUEST['begin']);
}

$structure_info = $structlib->s_get_structure_info($_REQUEST["page_ref_id"]);

$smarty->assign('page_ref_id', $_REQUEST["page_ref_id"]);
$smarty->assign('structure_id', $structure_info["page_ref_id"]);
$smarty->assign('structure_name', $structure_info["pageName"]);

$perms = Perms::get((array('type' => 'wiki page', 'object' => $structure_info["pageName"])));
$tikilib->get_perm_object($structure_info["pageName"], 'wiki page', $page_info);	// global perms still needed for logic in categorize.tpl

if ( ! $perms->view ) {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra('You do not have permission to view this page.'));
	$smarty->display("error.tpl");
	die;
}

if ($perms->edit_structures) {
	if ($prefs['lock_wiki_structures'] === 'y') {
		$lockedby = TikiLib::lib('attribute')->get_attribute('wiki structure', $structure_info['pageName'], 'tiki.object.lock');
		if ($lockedby && $lockedby === $user && $perms->lock_structures || ! $lockedby || $perms->admin_structures) {
			$editable = 'y';
		} else {
			$editable = 'n';
		}

	} else {
		$editable = 'y';
	}
} else {
	$editable = 'n';
}
$smarty->assign('editable', $editable);
	

$alert_categorized = array();
$alert_in_st = array();
$alert_to_remove_cats = array();
$alert_to_remove_extra_cats = array();

// start security hardened section
if ($editable === 'y') {
	$smarty->assign('remove', 'n');
	
	if (isset($_REQUEST["remove"])) {
		check_ticket('edit-structure');
		$smarty->assign('remove', 'y');
		$remove_info = $structlib->s_get_page_info($_REQUEST["remove"]);
	  	$structs = $structlib->get_page_structures($remove_info['pageName'], $structure);
		//If page is member of more than one structure, do not give option to remove page
		$single_struct = (count($structs) == 1);
		if ($single_struct && $perms->remove) {
			$smarty->assign('page_removable', 'y');
		} else {
			$smarty->assign('page_removable', 'n');
		}
		$smarty->assign('removepage', $_REQUEST["remove"]);
		$smarty->assign('removePageName', $remove_info["pageName"]);
	}
	
	if (isset($_REQUEST["rremove"])) {
		$access->check_authenticity();
		$structlib->s_remove_page($_REQUEST["rremove"], false, empty($_REQUEST['page'])? '': $_REQUEST['page']);
		$_REQUEST["page_ref_id"] = $page_info["parent_id"];
	}
	# TODO : Case where the index page of the structure is removed seems to be unexpected, leaving a corrupted structure
	if (isset($_REQUEST["sremove"])) {
		$access->check_authenticity();
		$page = $page_info["pageName"];
		$delete = $tikilib->user_has_perm_on_object($user, $page_info['pageName'], 'wiki page', 'tiki_p_remove');
		$structlib->s_remove_page($_REQUEST["sremove"], $delete, empty($_REQUEST['page'])? '': $_REQUEST['page']);
		$_REQUEST["page_ref_id"] = $page_info["parent_id"];
	}
	
	if ($prefs['feature_user_watches'] == 'y' && $tiki_p_watch_structure == 'y' && $user && !empty($_REQUEST['watch_object']) && !empty($_REQUEST['watch_action'])) {
		check_ticket('edit-structure');
		if ($_REQUEST['watch_action'] == 'add' && !empty($_REQUEST['page'])) {
			$tikilib->add_user_watch($user, 'structure_changed', $_REQUEST['watch_object'], 'structure', $page, "tiki-index.php?page_ref_id=".$_REQUEST['watch_object']);
		} elseif ($_REQUEST['watch_action'] == 'remove') {
			$tikilib->remove_user_watch($user, 'structure_changed', $_REQUEST['watch_object'], 'structure');
		}
	}
	
	if (!isset($structure_info) or !isset($page_info) ) {
		$smarty->assign('msg', tra("Invalid structure_id or page_ref_id"));
	
		$smarty->display("error.tpl");
		die;
	}
	
	$smarty->assign('alert_exists', 'n');
	if (isset($_REQUEST["create"])) {
		check_ticket('edit-structure');
		if (isset($_REQUEST["pageAlias"])) {
			$structlib->set_page_alias($_REQUEST["page_ref_id"], $_REQUEST["pageAlias"]);
		}
	  
		$after = null;
		if (isset($_REQUEST['after_ref_id'])) {
			$after = $_REQUEST['after_ref_id'];
		}
		if (!(empty($_REQUEST['name']))) {
			if ($tikilib->page_exists($_REQUEST["name"])) {
				$smarty->assign('alert_exists', 'y');
			}
			$structlib->s_create_page($_REQUEST['page_ref_id'], $after, $_REQUEST['name'], '', $structure_info['page_ref_id']);
			$userlib->copy_object_permissions($page_info["pageName"], $_REQUEST["name"], 'wiki page');
		} elseif (!empty($_REQUEST['name2'])) {
			foreach ($_REQUEST['name2'] as $name) {
				$new_page_ref_id = $structlib->s_create_page($_REQUEST['page_ref_id'], $after, $name, '', $structure_info['page_ref_id']);
	      	$after = $new_page_ref_id;      
			}	
		}

		if ($prefs['feature_wiki_categorize_structure'] == 'y') {
			$categlib = TikiLib::lib('categ');
			$pages_added = array();
			if (!(empty($_REQUEST['name']))) {
				$pages_added[] = $_REQUEST['name'];
			} elseif (!empty($_REQUEST['name2'])) {
	  			foreach ($_REQUEST['name2'] as $name) {
					$pages_added[] = $name;
	  			}
			}
			$cat_type = 'wiki page';
			foreach ($pages_added as $name) {
				$structlib->categorizeNewStructurePage($name, $structure_info);
			}
		}
	}
	
	if (isset($_REQUEST["move_node"])) {
		if ($_REQUEST["move_node"] == '1') {
			$structlib->promote_node($_REQUEST["page_ref_id"]);
		} elseif ($_REQUEST["move_node"] == '2') {
			$structlib->move_before_previous_node($_REQUEST["page_ref_id"]);
		} elseif ($_REQUEST["move_node"] == '3') {
			$structlib->move_after_next_node($_REQUEST["page_ref_id"]);
		} elseif ($_REQUEST["move_node"] == '4') {
			$structlib->demote_node($_REQUEST["page_ref_id"]);
		}
	}

} // end of security hardening

$page_info = $structlib->s_get_page_info($_REQUEST["page_ref_id"]);
$smarty->assign('pageName', $page_info["pageName"]);
$smarty->assign('pageAlias', $page_info["page_alias"]);
$smarty->assign('topPageAlias', $structure_info["page_alias"]);

$subpages = $structlib->s_get_pages($_REQUEST["page_ref_id"]);
$max = count($subpages);
$smarty->assign_by_ref('subpages', $subpages);
if ($max != 0) {
  $last_child = $subpages[$max - 1];
  $smarty->assign('insert_after', $last_child["page_ref_id"]);
}
if (isset($_REQUEST["find_objects"])) {
	$find_objects = $_REQUEST["find_objects"];
} else {
	$find_objects = '';
}

$smarty->assign('find_objects', $find_objects);

$filter = '';
if (!empty($_REQUEST['categId'])) {
	$filter['categId'] = $_REQUEST['categId'];
	$smarty->assign('find_categId', $_REQUEST['categId']);
} else {
	$smarty->assign('find_categId', '');
}
// Get all wiki pages for the dropdown menu
$listpages = $tikilib->list_pages(0, -1, 'pageName_asc', $find_objects, '', false, true, false, false, $filter);
$smarty->assign_by_ref('listpages', $listpages["data"]);

$structures = $structlib->list_structures(0, -1, 'pageName_asc');
$structures_filtered = array_filter($structures['data'], function ($struct) {
	return $struct['editable'] === 'y';
});
$smarty->assign_by_ref('structures', $structures_filtered);

$subtree = $structlib->get_subtree($structure_info["page_ref_id"]);
foreach ($subtree as $i=>$s) { // dammed recursivite - acn not do a left join
	if ($tikilib->user_watches($user, 'structure_changed', $s['page_ref_id'], 'structure')) {
		$subtree[$i]['event'] = true;
	}
}
$smarty->assign('subtree', $subtree);

if ($tikilib->user_watches($user, 'structure_changed', $structure_info['page_ref_id'], 'structure')) {
	$page_info['watching'] = true;
}
$smarty->assign('page_info', $page_info);


// Re-categorize
if ($editable === 'y') {
	$all_editable = 'y';
	foreach ($subtree as $k => $st) {
		if ($st['editable'] != 'y' && $k > 0) {
			$all_editable = 'n';
 			break;
		}
	}	
} else {
	$all_editable = 'n';
}
$smarty->assign('all_editable', $all_editable);

if (isset($_REQUEST["recategorize"]) && $prefs['feature_wiki_categorize_structure'] == 'y' && $all_editable == 'y') {
	$cat_name = $structure_info["pageName"];
	$cat_objid = $cat_name;
	$cat_href="tiki-index.php?page=" . urlencode($cat_name);
 	$cat_desc = '';
 	$cat_type='wiki page';
	include_once("categorize.php");
	$categories = array(); // needed to prevent double entering (the first time when page is being categorized in categorize.php)
	//include_once("categorize_list.php"); // needs to be up here to avoid picking up selection of cats from other existing sub-pages	
	//get array of pages in structure
	$othobjid = $structlib->s_get_structure_pages($structure_info["page_ref_id"]);	
	foreach ($othobjid as $othobjs) {	
		if ($othobjs["parent_id"] > 0) {				
			// check for page being in other structure.
			$strucs = $structlib->get_page_structures($othobjs["pageName"]);
			if (count($strucs) > 1) {
				$alert_in_st[] = $othobjs["pageName"];
			}								
			$cat_objid = $othobjs["pageName"];
			$cat_name = $cat_objid;
			$cat_href = "tiki-index.php?page=".urlencode($cat_objid);
			
			$catObjectId = $categlib->is_categorized($cat_type, $cat_objid);		
			if (!$catObjectId) {
	    		// page that is added is not categorized -> categorize it if necessary
				if (isset($_REQUEST["cat_categorize"]) && $_REQUEST["cat_categorize"] == 'on' && isset($_REQUEST["cat_categories"])) {
					$catObjectId = $categlib->add_categorized_object($cat_type, $cat_objid, $cat_desc, $cat_name, $cat_href);			
					foreach ($_REQUEST["cat_categories"] as $cat_acat) {						
						$categlib->categorize($catObjectId, $cat_acat);
					}
				}
			} else {
				// page that is added is categorized
				if (!isset($_REQUEST["cat_categories"]) || !isset($_REQUEST["cat_categorize"]) || isset($_REQUEST["cat_categorize"]) && $_REQUEST["cat_categorize"] != 'on') {
					if ($_REQUEST["cat_override"] == "on") {
						$categlib->uncategorize_object($cat_type, $cat_objid);
					} else {
						$alert_to_remove_cats[] = $cat_name;
					}
				} else {
					if ($_REQUEST["cat_override"] == "on") {
						$categlib->uncategorize_object($cat_type, $cat_objid);
						foreach ($_REQUEST["cat_categories"] as $cat_acat) {
							$catObjectId = $categlib->is_categorized($cat_type, $cat_objid);
							if (!$catObjectId) {
								// The object is not categorized  
								$catObjectId = $categlib->add_categorized_object($cat_type, $cat_objid, $cat_desc, $cat_name, $cat_href);
							}						
							$categlib->categorize($catObjectId, $cat_acat);
						}
					} else {
						$cats = $categlib->get_object_categories($cat_type, $cat_objid);						
						$numberofcats = count($cats);
						foreach ($_REQUEST["cat_categories"] as $cat_acat) {
							if (!in_array($cat_acat, $cats, true)) {
								$categlib->categorize($catObjectId, $cat_acat);
								$numberofcats += 1;							
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

if ($prefs['feature_wiki_categorize_structure'] == 'y' && $all_editable == 'y') {
	$cat_name = $structure_info["pageName"];
	$cat_objid = $cat_name;
	$cat_href="tiki-index.php?page=" . urlencode($cat_name);
 	$cat_desc = '';
 	$cat_type='wiki page'; 		
	include_once("categorize_list.php");
} elseif ($prefs['feature_categories'] == 'y') {
	$categlib = TikiLib::lib('categ');
	$categories = $categlib->getCategories();
	$smarty->assign_by_ref('categories', $categories);
}

ask_ticket('edit-structure');

include_once ('tiki-section_options.php');

if ($prefs['feature_jquery_ui'] === 'y') {
	$headerlib->add_jsfile('lib/structures/tiki-edit_structure.js');
	$headerlib->add_jsfile('vendor/jquery/plugins/nestedsortable/jquery.ui.nestedSortable.js');
	$structlib = TikiLib::lib('struct');

	$structure_id = $structure_info['structure_id'];
	if (!$structure_id) {
		$structure_id = $structure_info['page_ref_id'];
	}
	$smarty->assign('nodelist', $structlib->get_toc($structure_id, 'asc', false, false, '', 'admin', $page_info['page_ref_id'], 0, ''));
				// $page_ref_id,$order='asc',$showdesc=false,$numbering=true,$numberPrefix='',$type='plain',$page='',$maxdepth=0, $structurePageName=''
	$smarty->assign('structure_id', $structure_id);
}
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-edit_structure.tpl');
$smarty->display("tiki.tpl");
