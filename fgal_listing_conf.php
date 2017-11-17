<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to err & die if called directly.
//smarty is not there - we need setup
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

// Fill the display configuration array
$fgal_listing_conf = [
	'id' => ['name' => tra('ID')],
	'type' => ['name' => tra('Type'), 'key' => 'show_icon'],
	'name' => ['name' => tra('Name')],
	'description' => ['name' => tra('Description')],
	'size' => ['name' => tra('Size')],
	'created' => ['name' => tra('Created') . ' / ' . tra('Uploaded')],
	'lastModif' => ['name' => tra('Last modified'), 'key' => 'show_modified'],
	'creator' => ['name' => tra('Uploaded by')], //this used to be Creator but updated Nov2010
	'author' => ['name' => tra('Creator')],  //this used to be Author but updated Nov2010
	'last_user' => ['name' => tra('Last modified by')], //this used to be 'Last editor' but updated Nov2010
	'comment' => ['name' => tra('Comment')],
	'files' => ['name' => tra('Files')],
	'hits' => ['name' => tra('Hits')],
	'lastDownload' => ['name' => tra('Last download')],
	'lockedby' => ['name' => tra('Locked by'), 'icon' => 'lock'],
	'backlinks' => ['name' => tra('Backlinks')],
	'deleteAfter' => ['name' => tra('Delete after')],
	'share' => ['name' => tra('Share')],
	'source' => ['name' => tra('Source')],
];

if (isset($section) && $section == 'admin') {
	foreach ($fgal_listing_conf as $k => $v) {
		$fgal_listing_conf_admin[$k . '_admin'] = $v;
	}
}
foreach ($fgal_listing_conf as $k => $v) {
	if ($k == 'type') {
		$show_k = 'icon';
	} elseif ($k == 'lastModif') {
		$show_k = 'modified';
	} else {
		$show_k = $k;
	}

	if (isset($_REQUEST['fgal_list_' . $k])) {
		$fgal_listing_conf[$k]['value'] = $_REQUEST['fgal_list_' . $k];
	} elseif (isset($gal_info) && isset($gal_info['show_' . $show_k])) {
		$fgal_listing_conf[$k]['value'] = $gal_info['show_' . $show_k];
	} else {
		if (isset($prefs['fgal_list_' . $k])) {
			$fgal_listing_conf[$k]['value'] = $prefs['fgal_list_' . $k];
		}
	}
}
// Do not show "Locked by" info if the gallery is not lockable
if (isset($gal_info) && isset($gal_info['galleryId']) && isset($gal_info['lockable']) && $gal_info['lockable'] != 'y') {
	$fgal_listing_conf['lockedby']['value'] = 'n';
}

$smarty = TikiLib::lib('smarty');
$smarty->assign_by_ref('fgal_listing_conf', $fgal_listing_conf);

if (isset($section) && $section == 'admin') {
	foreach ($fgal_listing_conf_admin as $k => $v) {
		if (isset($prefs['fgal_list_' . $k])) {
			$fgal_listing_conf_admin[$k]['value'] = $prefs['fgal_list_' . $k];
		}
	}
	$smarty->assign_by_ref('fgal_listing_conf_admin', $fgal_listing_conf_admin);
}

$fgal_options = [
	'show_explorer' => ['name' => tra('Explorer')],
	'show_path' => ['name' => tra('Path')],
	'show_slideshow' => ['name' => tra('Slideshow')],
	'icon_fileId' => ['name' => tra('Gallery icon')],
];

if (! array_key_exists('view', get_defined_vars())) {
	if (isset($_REQUEST['view'])) {
		$view = $_REQUEST['view'];
	} else {
		$view = null;
	}
}
if ($view == 'admin') {
	$fgal_options['show_explorer'] = 'n';
	$fgal_options['show_path'] = 'n';
	$fgal_options['show_slideshow'] = 'n';
	$fgal_options['default_view'] = 'list';
	$fgal_options['icon_fileId'] = '';
} else {
	foreach ($fgal_options as $k_gal => $v) {
		// Validate that option exists.
		if (! isset($fgal_options[$k_gal])) {
			continue;
		}

		$k_prefs = 'fgal_' . $k_gal;

		if (isset($_REQUEST['edit_mode'])) {
			// We are in the edit file gallery page
			$fgal_options[$k_gal]['value'] = isset($gal_info[$k_gal]) ? $gal_info[$k_gal] : null;
		} else {
			// normal gallery view
			$fgal_options[$k_gal]['value'] = ( isset($gal_info) && isset($gal_info[$k_gal]) ) ? $gal_info[$k_gal] : isset($prefs[$k_prefs]) ? $prefs[$k_prefs] : null;
		}
	}
}

$smarty->assign_by_ref('fgal_options', $fgal_options);
