<?php

//Tikiwiki Opinion-Network (result) by Giotto (m--zsolt@freemail.hu)

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
	
	if ( isset( $_REQUEST[ "youcango" ] ) ) {
		$questions = $opnetlib->getQuestions( $_REQUEST[ "parentform" ], 'id', 'asc' );
		$filledform = $opnetlib->getFilledFormAverages( $user, $_REQUEST[ "parentform" ] );
		if ( !$filledform ) {
			$smarty->assign( 'message', tra("There are no forms of this type filled about you") );
		} else {
			for ( $i=0; each($filledform); $i++ ) {
					$filledform[ $i ][ "question" ] = $questions[$i]["question"];
					$filledform[ $i ][ "slider" ] = $opnetlib->putSlider( "slider$i", "sliderinput$i", $filledform[ $i ][ "AVG(`value`)" ] );
			}
			$smarty->assign( 'question', $filledform );
			$smarty->assign( 'message', "" );
		}
		$smarty->assign( 'youcango', $_REQUEST["youcango"] );
	}
	
	$formtypes = $opnetlib->getAvailableFormTypes( 'id', 'asc' );
	
	$smarty->assign( 'available_formtype',$formtypes );
	$smarty->display( "tiki-opnet_result.tpl" );

/*
	Additional TO-DO list:
	  - add $feature_opnet='y' to the tiki-setup.php
	  - add $tiki_p_opnet='y' to the tiki-setup.php
*/


?>