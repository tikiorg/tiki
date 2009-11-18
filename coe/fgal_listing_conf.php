<?php

// $Id: /cvsroot/tikiwiki/tiki/fgal_listing_conf.php,v 1.1.2.2 2008-03-16 00:06:53 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to err & die if called directly.
//smarty is not there - we need setup
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// Fill the display configuration array
$fgal_listing_conf = array(
	'id' => array('name' => tra('ID')),
	'type' => array('name' => tra('Type'), 'key' => 'show_icon'),
	'name' => array('name' => tra('Name')),
	'description' => array('name' => tra('Description')),
	'size' => array('name' => tra('Size')),
	'created' => array('name' => tra('Created').' / '.tra('Uploaded')),
	'lastModif' => array('name' => tra('Last Modified'), 'key' => 'show_modified'),
	'creator' => array('name' => tra('Creator')),
	'author' => array('name' => tra('Author')),
	'last_user' => array('name' => tra('Last editor')),
	'comment' => array('name' => tra('Comment')),
	'files' => array('name' => tra('Files')),
	'hits' => array('name' => tra('Hits')),
	'lockedby' => array('name' => tra('Locked by'), 'icon' => 'lock_gray')
);
foreach ( $fgal_listing_conf as $k => $v ) {

	if ( $k == 'type' ) $show_k = 'icon';
	elseif ( $k == 'lastModif' ) $show_k = 'modified';
	else $show_k = $k;

	if ( isset($_REQUEST['fgal_list_'.$k]) ) {
		$fgal_listing_conf[$k]['value'] = $_REQUEST['fgal_list_'.$k];
	} elseif ( isset($gal_info) && isset($gal_info['show_'.$show_k]) ) {
		$fgal_listing_conf[$k]['value'] = $gal_info['show_'.$show_k];
	} else {
		$fgal_listing_conf[$k]['value'] = $prefs['fgal_list_'.$k];
	}

	// Do not show "Locked by" info if the gallery is not lockable
	if ( isset($gal_info) && isset($gal_info['lockable']) && $gal_info['lockable'] != 'y' ) {
		$fgal_listing_conf['lockedby']['value'] = 'n';
	}

}
$smarty->assign_by_ref('fgal_listing_conf', $fgal_listing_conf);

$fgal_options = array(
	'show_explorer' => array('name' => tra('Explorer')),
	'show_path' => array('name' => tra('Path')),
	'show_slideshow' => array('name' => tra('Slideshow')),
	'default_view' => array('name' => tra('Default View'))
);

foreach ( $fgal_options as $k_gal => $v ) {
	$k_prefs = 'fgal_'.$k_gal;

	if ( $k_gal == 'default_view' ) {
		$fgal_options[$k_gal]['value'] = ( isset($gal_info) && isset($gal_info[$k_gal]) ) ? $gal_info[$k_gal] : $prefs[$k_prefs];
	} elseif ( !isset($_REQUEST['edit_mode']) ) {
		// We are in the file gallery admin panel
		$fgal_options[$k_gal]['value'] = $prefs[$k_prefs];
	} else {
		// We are in the edit file gallery page
		$fgal_options[$k_gal]['value'] = $gal_info[$k_gal];
	}
}

$smarty->assign_by_ref('fgal_options', $fgal_options);
