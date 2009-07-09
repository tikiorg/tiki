<?php

//this script may only be included - so its better to die if called directly.
if ( basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__) ) {
  header("location: index.php");
  exit;
}

ini_set( 'display_errors', 'on' );
error_reporting(E_ALL);

if( isset($page) && !empty($page) ) {
	global $smarty;
	global $semanticlib;
	require_once( 'lib/wiki/semanticlib.php' );

	$smarty->assign( 'show_semantic_links_module', true );

	$smarty->assign( 'msl_page', $msl_page = $page );

	$smarty->assign( 'msl_relations', $semanticlib->getRelationList( $msl_page ) );
}


