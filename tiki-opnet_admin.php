<?php

//Tikiwiki Opinion-Network (admin) by Giotto (m--zsolt@freemail.hu)

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//include part

	require_once( 'tiki-setup.php' );
	include_once( 'lib/opinion_network/opnetlib.php' );
	
	
// ** this have to be moved to tiki-setup.php 
	$feature_opnet='y';
	$tiki_p_opnet='y';

//initalization part
	
	if ( $feature_opnet !='y' ) {
		$smarty->assign( 'msg', tra( "This feature is disabled" ) );
		$smarty->display( "error.tpl" );
	}
	
	if ( $tiki_p_opnet != 'y') {
		$smarty->assign( 'msg', tra( "You dont have permission to use this feature" ) );
		$smarty->display( "error.tpl" );
		die;
	}
	
	if ( !isset( $opnetlib ) ) {
		$opnetlib = new OpnetLib( $dbTiki );
	}
	
//here we process the form data
	
// add a new form section
	if ( isset( $_REQUEST["addformtype"] ) ) {
		$opnetlib->addFormType( $_REQUEST["addformtype"] );
	}
	
// add a new question section
	if ( isset( $_REQUEST["question_str"] ) && isset( $_REQUEST["parentform"] ) ) {
		$opnetlib->addQuestion( $_REQUEST["question_str"], $_REQUEST["parentform"] );
		$smarty->assign( 'parentform', $_REQUEST["parentform"] ); 	
	}
	
// delete a form section
	if ( isset( $_REQUEST["formdelete"] ) && isset( $_REQUEST["parentform"] ) ) {
		$opnetlib->deleteForm( $_REQUEST["parentform"] );
	}

	$formtypes = $opnetlib->getAvailableFormTypes( 'id', 'asc' );
		
	$smarty->assign( 'available_formtype',$formtypes );
	$smarty->display( "tiki-opnet_admin.tpl" );

/*
	Additional TO-DO list:
	  - add $feature_opnet='y' to the tiki-setup.php
	  - add $tiki_p_opnet='y' to the tiki-setup.php
*/


?>