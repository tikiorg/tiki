<?php

ini_set( 'display_errors', 'on' );
error_reporting(E_ALL);

if( isset($page) && !empty($page) ) {
	global $semanticlib;
	require_once( 'lib/wiki/semanticlib.php' );

	$smarty->assign( 'show_semantic_links_module', true );

	$smarty->assign( 'msl_page', $msl_page = $page );

	$smarty->assign( 'msl_relations', $semanticlib->getRelationList( $msl_page ) );
}


