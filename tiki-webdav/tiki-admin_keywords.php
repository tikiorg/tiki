<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');

$access->check_feature( 'wiki_keywords' );
$access->check_permission( 'tiki_p_admin_wiki' );

function set_keywords( $page, $keywords="" ) {
	global $tikilib;
	
	$query = "UPDATE `tiki_pages` SET `keywords`=? WHERE `pageName`=? LIMIT 1";
	$bindvars = array( $keywords, $page );
	$result = $tikilib->query( $query, $bindvars );
	
	if ( !$result )
		return false;
		
	return true;
}

function get_keywords( $page ) {
	global $tikilib;
	return $tikilib->get_page_info( $page );
}

function get_all_keywords($limit=0, $offset=0, $page="") {
	global $tikilib;
	$query = "FROM `tiki_pages` WHERE `keywords` IS NOT NULL and `keywords` <> '' ";	
	if ( $page ) {
		$query .= " and `pageName` LIKE ?";
		$bindvars = array( "%$page%" );	
	} else {
		$bindvars = array();
	}
	
	$ret = array(
		'pages' => $tikilib->fetchAll( "SELECT `keywords`, `pageName` as page " .  $query, $bindvars, $limit, $offset ),
		'cant' => $tikilib->getOne( 'SELECT COUNT(*) ' . $query, $bindvars ),
	);
	
	return $ret;
}

//Init variables for limit and offset
$limit = $prefs['maxRecords'];
$offset = 0;

//Check for offset, see if it's a multiple of the limit
//This is done to stop arbitrary offsets being entered
$offset = (int)$_REQUEST['offset'];

if ( ( isset( $_REQUEST['save_keywords'] ) && isset( $_REQUEST['new_keywords'] ) && isset( $_REQUEST['page'] ) ) || ( isset( $_REQUEST['remove_keywords'] ) && isset( $_REQUEST['page'] ) ) ) {
	ask_ticket('admin_keywords');
	//Set page and new_keywords var for both remove_keywords and 
	//save_keywords actions at the same time
	( isset( $_REQUEST['page'] ) ) ? $page = $_REQUEST['page'] : $page = $_REQUEST['page'];
	( isset( $_REQUEST['new_keywords'] ) ) ? $new_keywords = $_REQUEST['new_keywords'] : $new_keywords = "";
		
	$update = set_keywords($page, $new_keywords);
	
	( $update ) ? $smarty->assign( 'keywords_updated', 'y' ) : $smarty->assign( 'keywords_updated', 'n' ); 
	$smarty->assign( 'keywords_updated_on', $_REQUEST['page'] );
}

if ( isset( $_REQUEST['page'] ) && !$_REQUEST['remove_keywords']  ) {
	$page_keywords = get_keywords($_REQUEST['page']);
	
	$smarty->assign('edit_keywords', $page_keywords['keywords'] );
	$smarty->assign('edit_keywords_page', $page_keywords['pageName'] );
	$smarty->assign('edit_on', 'y');
}

if ( isset( $_REQUEST['q'] ) && !$_REQUEST['remove_keywords'] && !$_REQUEST['save_keywords'] ) {
	$existing_keywords = get_all_keywords($limit, $offset, $_REQUEST['q']);
	$smarty->assign( 'search_on', 'y' );
	$smarty->assign( 'search_cant', $existing_keywords['cant'] );
}

if ( !isset( $existing_keywords['cant'] ) ) {
	$existing_keywords = get_all_keywords($limit, $offset);
}

if ( $existing_keywords['cant'] > 0 ) {

	$smarty->assign( 'existing_keywords', $existing_keywords['pages'] );
	
	$pages_cant = ceil( $existing_keywords['cant'] / $limit );
	$smarty->assign( 'pages_cant', $pages_cant );
	$smarty->assign( 'offset', $offset );
}	

$smarty->assign( 'mid', 'tiki-admin_keywords.tpl' );
$smarty->display( 'tiki.tpl' );
