<?php

//Tikiwiki Opinion-Network by Giotto (m--zsolt@freemail.hu)

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
	
	if( !$user ) {
		$smarty->assign('msg', tra("You must be logged in to use this feature"));
		$smarty->display("error.tpl");
		die;
	}
	
	if ( $feature_opnet !='y' ) {
		$smarty->assign( 'msg', tra( "This feature is disabled" ) );
		$smarty->display( "error.tpl" );
	}
	
	if ( $tiki_p_opnet != 'y') {
		$smarty->assign( 'msg', tra( "You do not have permission to use this feature" ) );
		$smarty->display( "error.tpl" );
		die;
	}
	
	if ( !isset( $opnetlib ) ) {
		$opnetlib = new OpnetLib( $dbTiki );
	}
	
	$userlist = $opnetlib->getUserList();
	$formtypes = $opnetlib->getAvailableFormTypes('id','asc');
	$friendlist = $opnetlib->getFriendList($user);

// we process the form

	if ( isset( $_REQUEST["parentform"] ) ) {
	// with this line we ensure that after sending the first form, the formtype selection remains the same
		$smarty->assign( 'parentform', $_REQUEST["parentform"] );		
	} else {
	// with this line we ensure that after sending the first form, the formtype selection remains the same
		$smarty->assign( 'parentform',  $formtypes[0]["id"] );
	}

// we use this to determine if we can display the middle part of the page
// the huge condition means: the user selected a person from the friend list OR the user list, but only from one of them
	if ( isset( $_REQUEST["youcango"] ) && 
	   ( ($_REQUEST["friendselect" ] != "none" || $_REQUEST["userselect"] != "none") && 
	   !( $_REQUEST["friendselect" ] != "none" && $_REQUEST["userselect"] != "none" ) ) 
	   ) {
		if ( $_REQUEST["friendselect" ] != "none" ) {
			$about_who = $_REQUEST["friendselect" ];
		} else {
			$about_who = $_REQUEST["userselect" ];
		}
		$smarty->assign( 'youcango', $_REQUEST["youcango"] );
		$formname = $opnetlib->getFormName( $_REQUEST["parentform"] );
		$smarty->assign( 'formname', $formname );
		$smarty->assign( 'username', $about_who );
		$filledform = $opnetlib->getFilledForm_byUserNamesAndFormType( $user, $about_who, $_REQUEST["parentform"] );
		
		// we pass variables of the first form which we need to the second form in hidden form fields
		$smarty->assign( 'about_who', $about_who );
		$smarty->assign( 'whichform',  $_REQUEST["parentform"] );
			
		if ( $filledform == false ) {
		// if there was now stored form, than we put out an empty one with the sliders centered		
			$filledform = $opnetlib->getQuestions( $_REQUEST["parentform"], 'id', 'asc' );
			for ( $i=0; each($filledform); $i++ ) {
				$filledform[ $i ][ "slider" ] = $opnetlib->putSlider( "slider$i", "sliderinput$i", 50 );
			}
						
		} else {
		// if we had a stored form, then we adjust the sliders
			for ( $i=0; each($filledform); $i++ ) {
				$filledform[ $i ][ "slider" ] = $opnetlib->putSlider( "slider$i", "sliderinput$i", $filledform[ $i ][ 'value' ] );
			}
		}

		$smarty->assign( 'question', $filledform );

	}

// we have to store the form
	if ( isset( $_REQUEST["store"] ) ) {
	
	// first we store the slider values
		$result = array();
		for ( $i=0; isset( $_REQUEST["slider$i"."value"] ); $i++ ) {
			$result[] = $_REQUEST["slider$i"."value"];
		}
	// then we get the passed variables from the hidden fields
		$about_who = $_REQUEST["about_who"];
		$whichform = $_REQUEST["whichform"];
		$smarty->assign( 'parentform', $whichform );
	// now we have everything to call the function that stores the answers	
		$opnetlib->storeAnswers( $user, $about_who, $whichform, $result );	
	}
	
// this will make the list of the users at the bottom of the page	
	if ( isset( $_REQUEST["parentform"] ) ) {
		$bottomlist = $opnetlib->getObjectOfTheFormUsers( $user, $_REQUEST["parentform"] );
	} elseif ( isset( $_REQUEST["whichform"] ) ) {
		$bottomlist = $opnetlib->getObjectOfTheFormUsers( $user, $_REQUEST["whichform"] );
	} else {
		$bottomlist = $opnetlib->getObjectOfTheFormUsers( $user, $formtypes[0]["id"] );
	}

// we set the template variables and display the template
	$smarty->assign( 'bottomuser', $bottomlist );
	$smarty->assign('available_formtype',$formtypes);
	$smarty->assign('boxtitle',tra("Opinion-Network"));	
	$smarty->assign_by_ref( 'friend', $friendlist[ "data" ] );
	$smarty->assign_by_ref( 'user', $userlist[ "data" ] );

	$smarty->display( "tiki-opnet.tpl" );


/*
	Additional TO-DO list:
	  - add $feature_opnet='y' to the tiki-setup.php
	  - add $tiki_p_opnet='y' to the tiki-setup.php
*/


?>