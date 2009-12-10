<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-listpages.php,v 1.54.2.9 2008-03-10 20:15:22 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'wiki page';
$section_class = "wiki_page manage";	// This will be body class instead of $section
require_once('tiki-setup.php');
require_once('lib/ajax/ajaxlib.php');

$auto_query_args = array('initial','maxRecords','sort_mode','find','lang','langOrphan', 'findfilter_orphan');

$smarty->assign('headtitle',tra('Pages'));

$access->check_feature( array( 'feature_wiki', 'feature_listPages' ) );
$access->check_permission( 'tiki_p_view' );

/* mass-remove: 
   the checkboxes are sent as the array $_REQUEST["checked[]"], values are the wiki-PageNames, 
   e.g. $_REQUEST["checked"][3]="HomePage"
   $_REQUEST["submit_mult"] holds the value of the "with selected do..."-option list
   we look if any page's checkbox is on and if remove_pages is selected.
   then we check permission to delete pages.
   if so, we call histlib's method remove_all_versions for all the checked pages.
*/
if ( !empty($_REQUEST['submit_mult']) && isset($_REQUEST["checked"]) ) {
	$action = $_REQUEST['submit_mult'];
	check_ticket('list-pages');

	switch ( $action ) {

		case 'remove_pages':
			// Now check permissions to remove the selected pages
			$access->check_permission( 'tiki_p_remove' );

			foreach ( $_REQUEST["checked"] as $check )
				$tikilib->remove_all_versions($check);
			break;
			
		case 'print_pages':
			$access->check_feature( 'feature_wiki_multiprint' );

			foreach ( $_REQUEST["checked"] as $check ) {
				$access->check_page_exists($check);
				// Now check permissions to access this page
				if (!$tikilib->user_has_perm_on_object($user, $check, 'wiki page', 'tiki_p_view')) {
					$access->display_error( $check, tra("Permission denied you cannot view this page"), '403' );
				}
				$page_info = $tikilib->get_page_info($check);
				$page_info['parsed'] = $tikilib->parse_data($page_info['data']);
				$page_info['h'] = 1;
				$multiprint_pages[] = $page_info;
			}
			break;

		case 'unlock_pages':
			$access->check_feature( 'feature_wiki_usrlock' );
			global $wikilib; include_once('lib/wiki/wikilib.php');
			foreach ($_REQUEST["checked"] as $check) {
				$info = $tikilib->get_page_info($check);
				if ($info['flag'] == 'L' && ($tiki_p_admin_wiki == 'y' || ($user && ($user == $info['lockedby']) || (!$info['lockedby'] && $user == $info['user'])))) {
					$wikilib->unlock_page($check);
					}	
			}
			break;
		case 'lock_pages':
			$access->check_feature( 'feature_wiki_usrlock' );
			global $wikilib; include_once('lib/wiki/wikilib.php');
			foreach ($_REQUEST["checked"] as $check) {
				$info = $tikilib->get_page_info($check);
				if ($info['flag'] != 'L' && ($tiki_p_admin_wiki == 'y' || $tikilib->user_has_perm_on_object($user, $check, 'wiki page', 'tiki_p_lock', 'tiki_p_edit_categorized'))) {
					$wikilib->lock_page($check);
					}	
			}
			break;
	case 'zip':
		if ($tiki_p_admin == 'y') {
			include_once('lib/wiki/xmllib.php');
			$xmllib = new XmlLib();
			$zipFile = 'dump/xml.zip';
			$config['debug'] = false;
			if ($xmllib->export_pages($_REQUEST['checked'], null, $zipFile, $config)) {
				if (!$config['debug']) {
					header("location: $zipFile");
					die;
				}
			} else {
				$smarty->assign('error', $xmllib->get_error());
			}
		}
		break;
	}
}

if ( ! empty($multiprint_pages) ) {

	$smarty->assign('print_page', 'y');
	$smarty->assign_by_ref('pages', $multiprint_pages);

	// disallow robots to index page:
	$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

	// Display the template
	$smarty->display("tiki-print_multi_pages.tpl");

} else {

	// This script can receive the thresold
	// for the information as the number of
	// days to get in the log 1,3,4,etc
	// it will default to 1 recovering information for today
	if (isset($_REQUEST['maxRecords'])) {
		$maxRecords = $_REQUEST['maxRecords'];
	} else {
		$maxRecords = $maxRecords;	
	}
	
	
	if (!isset($_REQUEST["sort_mode"])) {
		$sort_mode = $prefs['wiki_list_sortorder'].'_'.$prefs['wiki_list_sortdirection'];
	} else {
		$sort_mode = $_REQUEST["sort_mode"];
	}
	
	$smarty->assign_by_ref('sort_mode', $sort_mode);
	
	// If offset is set use it if not then use offset =0
	// use the maxRecords php variable to set the limit
	// if sortMode is not set then use lastModif_desc
	if (!isset($_REQUEST["offset"])) {
		$offset = 0;
	} else {
		$offset = $_REQUEST["offset"];
	}
	
	$smarty->assign_by_ref('offset', $offset);
	
	if (!empty($_REQUEST["find"])) {
		$find = strip_tags($_REQUEST["find"]);
	} else {
		if (!empty($_REQUEST["q"])) {
			$find = strip_tags($_REQUEST["q"]);
		} else {
			$find = '';
		}
	}
	$smarty->assign('find', $find);
	
	$filter = '';
	if (!empty($_REQUEST['lang'])) {
		$filter['lang'] = $_REQUEST['lang'];
		$smarty->assign_by_ref('find_lang', $_REQUEST['lang']);
	}
	if (!empty($_REQUEST['langOrphan'])) {
		$filter['langOrphan'] = $_REQUEST['langOrphan'];
		$smarty->assign_by_ref('find_langOrphan', $_REQUEST['langOrphan']);
	}
	if ($prefs['feature_categories'] == 'y' && !empty($_REQUEST['categId'])) {
		$filter['categId'] = $_REQUEST['categId'];
		$smarty->assign_by_ref('find_categId', $_REQUEST['categId']);
	}
	if ($prefs['feature_categories'] == 'y' && !empty($_REQUEST['category'])) {
		global $categlib; include_once ('lib/categories/categlib.php');
		$filter['categId'] = $categlib->get_category_id($_REQUEST['category']);
		$smarty->assign_by_ref('find_categId', $filter['categId']);	
	}

	if (isset($_REQUEST["initial"])) {
		$initial = $_REQUEST["initial"];
	} else {
		$initial = '';
	}
	$smarty->assign('initial', $initial);
	
	if (isset($_REQUEST["exact_match"])) {
		$exact_match = true;
		$smarty->assign('exact_match', 'y');
	} else {
		$exact_match = false;
		$smarty->assign('exact_match', 'n');
	}                 
	
	// Get a list of last changes to the Wiki database
	//   $listpages_orphans must not be initialized here because it can already have received a value from another script
	if (!isset($listpages_orphans)) {
		$listpages_orphans = false;
	}
	$listpages = $tikilib->list_pages($offset, $maxRecords, $sort_mode, $find, $initial, $exact_match, false, true, $listpages_orphans, $filter);

	// Only show the 'Actions' column if the user can do at least one action on one of the listed pages
	$show_actions = 'n';
	$actions_perms = array('tiki_p_edit', 'tiki_p_wiki_view_history', 'tiki_p_assign_perm_wiki_page', 'tiki_p_remove');
	foreach ( $actions_perms as $p ) {
		foreach ( $listpages['data'] as $i ) {
			if ( $i['perms'][$p] == 'y' ) {
				$show_actions = 'y';
				break 2;
			}
		}
	}
	$smarty->assign('show_actions', $show_actions);


	// If there're more records then assign next_offset
	$cant_pages = ceil($listpages["cant"] / $maxRecords);
	$smarty->assign_by_ref('cant_pages', $cant_pages);
	$smarty->assign('actual_page', 1 + ($offset / $maxRecords));
	
	if ($listpages["cant"] > ($offset + $maxRecords)) $smarty->assign('next_offset', $offset + $maxRecords);
	else $smarty->assign('next_offset', -1);
	
	// If offset is > 0 then prev_offset
	if ($offset > 0) $smarty->assign('prev_offset', $offset - $maxRecords);
	else $smarty->assign('prev_offset', -1);
	
	if ($prefs['feature_categories'] == 'y') {
		global $categlib; include_once ('lib/categories/categlib.php');
		$categories = $categlib->get_all_categories_respect_perms($user, 'tiki_p_view_categories');
		$smarty->assign_by_ref('categories', $categories);
		if ((isset($prefs['wiki_list_categories']) && $prefs['wiki_list_categories'] == 'y') || (isset($prefs['wiki_list_categories_path']) && $prefs['wiki_list_categories_path'] == 'y')) {
			foreach ($listpages['data'] as $i=>$check) {
				$cats = $categlib->get_object_categories('wiki page',$check['pageName']);
				foreach ($cats as $cat) {
					if ($userlib->user_has_perm_on_object($user, $cat, 'category', 'tiki_p_view_categories')) {
						$listpages['data'][$i]['categpath'][] = $cp = $categlib->get_category_path_string($cat);
						if ($s = strrchr($cp, ':'))
							$listpages['data'][$i]['categname'][] = substr($s, 1);
						else
							$listpages['data'][$i]['categname'][] = $cp;
					}
				}
			}
		}
	}
	if ($prefs['feature_multilingual'] == 'y') {
        $languages = array();
        $languages = $tikilib->list_languages(false, 'y');
        $smarty->assign_by_ref('languages', $languages);
	}

	$smarty->assign_by_ref('listpages', $listpages["data"]);
	$smarty->assign_by_ref('cant', $listpages['cant']);
	
	ask_ticket('list-pages');
	
	include_once ('tiki-section_options.php');
	
	// disallow robots to index page:
	$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

	if( $access->is_serializable_request() ) {
		
		if( isset( $_REQUEST['listonly'] ) && ($prefs['feature_mootools'] == 'y' || ($prefs['feature_jquery'] == 'y' && $prefs['feature_jquery_autocomplete'] == 'y')) ) {
			$pages = array();
			foreach( $listpages['data'] as $page )
				$pages[] = $page['pageName'];

			$access->output_serialized( $pages );
		} else {
			$pages = array();
			require_once 'lib/wiki/wikilib.php';
			foreach( $listpages['data'] as $page ) {
				$pages[] = array(
					'page_id' => $page['page_id'],
					'page_name' => $page['pageName'],
					'url' => $wikilib->sefurl( $page['pageName'] ),
					'version' => $page['version'],
					'description' => $page['description'],
					'last_modif' => date( 'Y-m-d H:i:s', $page['lastModif'] ),
					'last_author' => $page['user'],
					'creator' => $page['creator'],
					'creation_date' => date( 'Y-m-d H:i:s', $page['created'] ),
					'lang' => $page['lang'],
				);
			}

			require_once 'lib/ointegratelib.php';
			$response = OIntegrate_Response::create( array( 'list' => $pages ), '1.0' );
			$response->addTemplate( 'smarty', 'tikiwiki', 'files/templates/listpages/smarty-tikiwiki-1.0-shortlist.txt' );
			$response->schemaDocumentation = 'http://dev.tikiwiki.org/WebserviceListpages';
			$response->send();
		}
	} else {
		// Display the template
		$smarty->assign('mid', ($listpages_orphans ? 'tiki-orphan_pages.tpl' : 'tiki-listpages.tpl') );
		$smarty->display("tiki.tpl");
	}
}
?>
