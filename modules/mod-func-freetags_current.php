<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_freetags_current_info() {
	return array(
		'name' => tra('Freetags Wiki'),
		'description' => tra('Displays current freetags on wiki pages and enables to add tags if permissions allow.'),
		'prefs' => array( 'feature_freetags' ),
		'params' => array()
	);
}

function module_freetags_current( $mod_reference, $module_params ) {
	global $user, $page, $smarty;
	global $freetaglib;

	include_once 'lib/freetag/freetaglib.php';

	$objectperms = Perms::get( array( 'type' => 'wiki page', 'object' => $page ) );
	if( ! empty( $page ) && $objectperms->view ) {
		if ( $objectperms->edit && $objectperms->freetags_tag )
		{
			if ( isset( $_POST['mod_add_tags'] ) ) {
				$freetaglib->tag_object( $user, $page, 'wiki page', $_POST['tags'] );
				header( "Location: {$_SERVER['REQUEST_URI']}" );
				exit;
			}
			$smarty->assign('addFreetags', "y");
			$canTag = true;
		} else {
			$canTag = false;
		}

		$smarty->assign('tpl_module_title', tra('Freetags'));

		$currenttags = $freetaglib->get_tags_on_object( $page, 'wiki page' );
		if( count( $currenttags['data'] ) || $canTag ) {
			$smarty->assign('modFreetagsCurrent', $currenttags);
		}
	}
}
