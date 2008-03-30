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
	'lastmodif' => array('name' => tra('Last Modified'), 'key' => 'show_modified'),
	'creator' => array('name' => tra('Creator')),
	'author' => array('name' => tra('Author')),
	'last_user' => array('name' => tra('Last editor')),
	'comment' => array('name' => tra('Comment')),
	'files' => array('name' => tra('Files')),
	'hits' => array('name' => tra('Hits')),
	'lockedby' => array('name' => tra('Locked by'))
);
foreach ( $fgal_listing_conf as $k => $v ) {

	if ( $k == 'type' ) $show_k = 'icon';
	elseif ( $k == 'lastmodif' ) $show_k = 'modified';
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
	'show_path' => array('name' => tra('Path'))
);

foreach ( $fgal_options as $k_gal => $v ) {
	$k_prefs = 'fgal_'.$k_gal;

	// Only check the current gallery value if the feature (in global prefs) is enabled
	$fgal_options[$k_gal]['value'] = ( $prefs[$k_prefs] == 'y' && isset($gal_info) ) ? $gal_info[$k_gal] : $prefs[$k_prefs];
}

$smarty->assign_by_ref('fgal_options', $fgal_options);
