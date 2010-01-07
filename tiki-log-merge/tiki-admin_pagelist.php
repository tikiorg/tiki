<?php

require_once ('tiki-setup.php');
require_once ('lib/pagelist/pagelistlib.php');
require_once('lib/categories/categlib.php');

$access->check_feature( 'feature_pagelist' );
$access->check_permission( 'tiki_p_admin' );

//set up basic variables for processing the page
$edit_types = array( 'list', 'page');
$edit_type = $_REQUEST['edit'];
$edit_action = $_REQUEST['action'];


if ( $edit_type == 'list' ) {
	if ( $edit_action == 'add' ) {
		//define base properties of new list via the request
		$new_list = array( 
			'name' => ( isset($_REQUEST['new_list_name']) ) ? $_REQUEST['new_list_name'] : false,
			'title' => ( isset($_REQUEST['new_list_title']) ) ? $_REQUEST['new_list_title'] : false,
			'description' => ( isset($_REQUEST['new_list_description']) ) ? $_REQUEST['new_list_description'] : false 
			);
		if ( !$new_list['name'] ) {
			$smarty->assign('msg', tra('The list name must be specified'));
			$smarty->display('error.tpl');
			die;
		}
		
		//if list is a fresh add and not an update
		if ( !$_POST['old_list_name']) {
			if ( !$pagelistlib->addListType($new_list['name'], $new_list['title'], $new_list['description'])) {
				//adding list failed
				$smarty->assign('msg', tra('Sorry, your list could not be added. Please check that it doesn\'t already exist.'));
				$smarty->display('error.tpl');
				die;
			}
		} else {
			if ( !$pagelistlib->listTypeExists($_POST['old_list_name'])) {
				$smarty->assign('msg', tra('Sorry, your list could not be updated. Please check that it already exists.'));
				$smarty->display('error.tpl');
				die;				
			}
			//if list does exist, update it
			$update_list = $pagelistlib->getListType($_POST['old_list_name']);
			$pagelistlib->updateListType($new_list['name'], $new_list['title'], $new_list['description'], $update_list['name']);
		}
		
		if ( $_REQUEST['new_list_cats'] && is_array($_REQUEST['new_list_cats']) ) {
			$all_pages = array();
			//pull in pages from any categories being used to seed the list
			foreach ( $_REQUEST['new_list_cats'] as $cat_seed_id ) {
				$new_pages = $categlib->list_category_objects($cat_seed_id, 0, -1, 'name_asc', 'wiki page', '', false, false, isset($prefs['language']) ? $prefs['language'] : false);
				$all_pages = array_merge($all_pages, $new_pages['data']);
			}
			$insert_pages = array();
			foreach( $all_pages as $item )
				//reformat list into something that PageListLib class can handle
				$insert_pages[] = array( 'page' => $item['name'] );
			unset($all_pages, $new_pages);
			
			$pagelistlib->updateListItems($insert_pages, $new_list['name']);
		}
		
		//show the 'edit' version of the page
		$edit_action = 'edit';
	} elseif ( $edit_action == 'update') {
		if ( $pagelistlib->listTypeExists($_REQUEST['name']))
			$update_list = $pagelistlib->getListType($_REQUEST['name']);
		if ( $update_list ) { //if list to be updated exists	  
			$updated_items = array();
			$removed_items = array();
			foreach( $_POST['pages'] as $item_info ) {
				$updated_item = array();
				list($updated_item['page'], $updated_item['priority'], $updated_item['score'], $updated_item['remove']) = array_values($item_info);
				
				if ( $updated_item['remove'] ) {
					$removed_items[] = $updated_item['page'];
				} else {
					$updated_item['priority'] = (int)$updated_item['priority'];
					$updated_item['score'] = (float)$updated_item['score'];
					$updated_items[] = $updated_item; 
				}
			}

			if ( count($updated_items))
				$pagelistlib->updateListItems($updated_items, $update_list['name']);
			
			if ( count($removed_items))
				$pagelistlib->purgeListItemsByPage($removed_items, $update_list['name']);

			$edit_action = 'edit';
		
		} else { //if bad request, show starting screen of admin panel
			$edit_type= false;
		}
	} elseif ( $edit_action == 'page') {
		if ( $pagelistlib->listTypeExists($_REQUEST['name']))
			$add_list = $pagelistlib->getListType($_REQUEST['name']);
		
		//format new page into something the PageListClass can handle
		$new_item = array( 
			array(
				'page' => ( isset($_POST['new_item_page']) ) ? $_POST['new_item_page'] : false,
				'priority' => ( isset($_POST['new_item_priority']) ) ? $_POST['new_item_priority'] : false,
				'score' => ( isset($_POST['new_item_score']) ) ? $_POST['new_item_score'] : false 
				)
			);
		
		if ( $new_item[0]['page'] ) //if the page is defined, add it
			$pagelistlib->updateListItems($new_item, $add_list['name']);
		
		$edit_action = 'edit';
	} elseif ( $edit_action == 'remove') {
		if ( $pagelistlib->listTypeExists($_REQUEST['name']))
			$remove_list = $pagelistlib->getListType($_REQUEST['name']);
		if ( $remove_list ) {	 
			$area = 'delpagelist';
			if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
				if ( $pagelistlib->getListPagesCant($remove_list['name']))
					$pagelistlib->purgeListItemsByList($remove_list['name']);
				$pagelistlib->deleteListType($remove_list['name']);
			} else {
				key_get($area);
			}
		}
		
		$edit_type = false;	
	}

	if ( $edit_action == 'edit') {
		$params = array(
			'offset' => ( isset($_REQUEST['offset']) && (int)$_REQUEST['offset'] > 0 ) ? (int)$_REQUEST['offset'] : 0,
			'limit' => ( isset($_REQUEST['offset']) && (int)$_REQUEST['limit'] > 0 ) ? (int)$_REQUEST['limit'] : 25,
			'order' => ( isset($_REQUEST['order']) ) ? $_REQUEST['order'] : 'page_name_asc'
		);

		if ( $new_list ) {
			$edit_list = $new_list;
		} else {
			if ( $pagelistlib->listTypeExists($_REQUEST['name']))
				$edit_list = $pagelistlib->getListType($_REQUEST['name']);  
		}
		
		if ( $edit_list ) {
			$smarty->assign('edit_type', $edit_type);
			$smarty->assign('list', $edit_list);
			$smarty->assign('list_items', $pagelistlib->getListPages($edit_list['name'], $params['order'], $params['offset'], $params['limit']));
	
			//Do pagination business
			$cant_items = $pagelistlib->getListPagesCant($edit_list['name']);
			$cant_pages = ceil($cant_items / $params['limit']);
			$smarty->assign('cant_pages', $cant_pages);
			$smarty->assign('pagination_params', $params);
			$smarty->assign('prev_offset', ($params['offset'] - $params['limit']));
			$smarty->assign('next_offset', ( ($params['offset'] + $params['limit']) <= $cant_items ) ? $params['offset'] + $params['limit'] : -1 );
			$smarty->assign('actual_page', ($params['offset'] < $params['limit']) ? 1 : ceil($params['offset'] / $params['limit']) + 1);	
		} else {
			$edit_type = false;
		}
	}
}

if ( !$edit_type || !in_array($edit_type, $edit_types)) {
	$smarty->assign('lists', $pagelistlib->getAllListTypes());
	$smarty->assign('categories', $categlib->list_categs());
}

$smarty->assign('mid', 'tiki-admin_pagelist.tpl');
$smarty->display('tiki.tpl');
