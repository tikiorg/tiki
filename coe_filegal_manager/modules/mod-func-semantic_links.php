<?php

//this script may only be included - so its better to die if called directly.
if ( basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__) ) {
  header("location: index.php");
  exit;
}

function module_semantic_links_info() {
	return array(
		'name' => tra('Semantic links'),
		'description' => tra('Lists the relationships known for the Wiki page displayed. For each relation type contained in the page, it lists all the pages it links to or gets linked from.'),
		'prefs' => array( 'feature_semantic' ),
		'params' => array()
	);
}

function module_semantic_links( $mod_reference, $module_params ) {
	global $page, $smarty;
	$smarty->assign( 'show_semantic_links_module', false );

	if( isset($page) && !empty($page) ) {
		global $semanticlib;
		require_once( 'lib/wiki/semanticlib.php' );
	
		$smarty->assign( 'show_semantic_links_module', true );
	
		$smarty->assign( 'msl_page', $msl_page = $page );
	
		$smarty->assign( 'msl_relations', $semanticlib->getRelationList( $msl_page ) );
		$smarty->clear_assign('tpl_module_title');
	}
}
