<?php
// $Id: /cvsroot/tikiwiki/tiki/tiki-file_archives.php,v 1.9.2.2 2008-03-03 20:16:13 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/filegals/filegallib.php');

if ($prefs['feature_file_galleries'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_file_galleries");
	$smarty->display("error.tpl");
	die;
}

if (empty($_REQUEST['fileId']) || !($fileInfo = $filegallib->get_file_info($_REQUEST['fileId']))) {
	$smarty->assign('msg', tra("Incorrect param"));
	$smarty->display('error.tpl');
	die;
}

$gal_info = $tikilib->get_file_gallery($fileInfo['galleryId']);

$tikilib->get_perm_object($fileInfo['galleryId'], 'file gallery', $gal_info, true);

if (!($tiki_p_admin_file_galleries == 'y' || $tiki_p_view_file_gallery == 'y')) {
	$smarty->assign('msg', tra("Permission denied you cannot edit this file"));
	$smarty->display("error.tpl");
	die;
}

$auto_query_args = array('fileId','offset','find','sort_mode','filegals_manager','maxRecords');

if (!empty($_REQUEST['remove'])) {
	check_ticket('list-archives');
	if (!($removeInfo = $filegallib->get_file_info($_REQUEST['remove']))) {
		$smarty->assign('msg', tra("Incorrect param"));
		$smarty->display('error.tpl');
		die;
	}		
	if (!($tiki_p_admin_file_galleries == 'y' || ($user && ($user == $gal_info['user'] || $user == $removeInfo['user'])))) {
		$smarty->assign('msg', tra("Permission denied you cannot remove files from this gallery"));
		$smarty->display("error.tpl");
		die;
	}
	$area = 'delfile';
	if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		key_check($area);
		$filegallib->remove_file($removeInfo, $user, $gal_info);
	} else {
		key_get($area, ($removeInfo['archiveId']? tra('Remove archive: '): tra('Remove file gallery: ')).(!empty($removeInfo['name'])?$removeInfo['name'].' - ':'').$removeInfo['filename']);
	}
}
if (isset($_REQUEST['delsel_x']) && !empty($_REQUEST['file'])) {
	check_ticket('list-archives');
	foreach (array_values($_REQUEST['file']) as $fileId) {
		if (!($removeInfo = $filegallib->get_file_info($fileId))) {
			$smarty->assign('msg', tra("Incorrect param"));
			$smarty->display('error.tpl');
			die;
		}		
		$filegallib->remove_file($removeInfo, $user, $gal_info);		
	}
}

// Set display config
if ( ! isset($_REQUEST['maxRecords']) || $_REQUEST['maxRecords'] <= 0 ) {
	$_REQUEST['maxRecords'] = $prefs['maxRecords'];
}
$smarty->assign_by_ref('maxRecords', $_REQUEST['maxRecords']);


if ( ! isset($_REQUEST['offset'])) $_REQUEST['offset'] = 0;
$smarty->assign_by_ref('offset', $_REQUEST['offset']);

if ( ! isset($_REQUEST['sort_mode']) ) $_REQUEST['sort_mode'] = 'lastmodif_desc';
$smarty->assign_by_ref('sort_mode', $_REQUEST['sort_mode']);

if ( ! isset($_REQUEST['find']) ) $_REQUEST['find'] = '';
$smarty->assign('find', $_REQUEST['find']);

$files = $filegallib->get_archives($_REQUEST['fileId'], $_REQUEST['offset'], $_REQUEST['maxRecords'], $_REQUEST['sort_mode'], $_REQUEST['find']);

$file = array($fileInfo);
$smarty->assign_by_ref('files', $files['data']);
$smarty->assign_by_ref('file', $file); 
$smarty->assign_by_ref('cant', $files['cant']);
$smarty->assign_by_ref('file_info', $fileInfo);

$gal_info = array_merge($gal_info, array(
	'show_id' => 'n',
	'show_icon' => 'y',
	'show_name' => 'f',
	'show_description' => 'o',
	'show_size' => 'o',
	'show_created' => 'o',
	'show_modified' => 'y',
	'show_creator' => 'o',
	'show_author' => 'o',
	'show_last_user' => 'o',
	'show_comment' => 'y',
	'show_files' => 'n',
	'show_hits' => 'n',
	'show_lockedby' => 'n',
	'show_checked' => 'y',
	'show_userlink' => 'y',
//	'show_checked' = 'n'
));
$smarty->assign_by_ref('gal_info', $gal_info);

$section = 'file_galleries';
include_once ('tiki-section_options.php');

if ($prefs['feature_theme_control'] == 'y') {
	$cat_type = 'file gallery';
	$cat_objid = $_REQUEST["galleryId"];
	include ('tiki-tc.php');
}
ask_ticket('list-archives');

// Get listing display config
include_once('fgal_listing_conf.php');

$smarty->assign('mid', 'tiki-file_archives.tpl');
$smarty->display("tiki.tpl");

?>
