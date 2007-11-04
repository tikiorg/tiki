<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-file_archives.php,v 1.9.2.1 2007-11-04 22:08:04 nyloth Exp $

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
	foreach (array_values($_REQUEST['file'])as $fileId) {
		if (!($removeInfo = $filegallib->get_file_info($fileId))) {
			$smarty->assign('msg', tra("Incorrect param"));
			$smarty->display('error.tpl');
			die;
		}		
		$filegallib->remove_file($removeInfo, $user, $gal_info);		
	}
}

if (!isset($_REQUEST['file_sort_mode']))
	$_REQUEST['file_sort_mode'] = 'created_desc';
$smarty->assign_by_ref('file_sort_mode', $_REQUEST['file_sort_mode']);
if (!isset($_REQUEST['file_find']))
	$_REQUEST['file_find'] = '';
$smarty->assign('file_find', $_REQUEST['file_find']);
if (!isset($_REQUEST['file_offset']))
	$_REQUEST['file_offset'] = 0;
$smarty->assign_by_ref('file_offset', $_REQUEST['file_offset']);

$files = $filegallib->get_archives($_REQUEST['fileId'], $_REQUEST['file_offset'], $maxRecords, $_REQUEST['file_sort_mode'], $_REQUEST['file_find']);
$smarty->assign_by_ref('files', $files['data']);
$file[] = $fileInfo;
$smarty->assign_by_ref('file',$file); 

$cant_pages = ceil($files['cant'] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign_by_ref('maxRecords', $maxRecords);
$smarty->assign('actual_page', 1 + ($file_offset / $maxRecords));
if ($files["cant"] > ($file_offset + $maxRecords)) {
	$smarty->assign('file_next_offset', $file_offset + $maxRecords);
} else {
	$smarty->assign('file_next_offset', -1);
}
if ($file_offset > 0) {
	$smarty->assign('file_prev_offset', $file_offset - $maxRecords);
} else {
	$smarty->assign('file_prev_offset', -1);
}

$smarty->assign_by_ref('file_info', $fileInfo);
$gal_info['show_checked'] = 'n';
$gal_info['show_created'] = 'n';
$gal_info['show_modified'] = 'y';
$gal_info['show_comment'] = 'y';
$smarty->assign('file_gal_info', $gal_info);
unset($gal_info['show_checked']);
$gal_info['show_comment'] = 'y';
$gal_info['show_description'] = 'n';
$gal_info['show_created'] = 'n';
$gal_info['show_modified'] = 'y';
$gal_info['show_lockedby'] = 'n';
$smarty->assign_by_ref('gal_info', $gal_info);

$section = 'file_galleries';
include_once ('tiki-section_options.php');

if ($prefs['feature_theme_control'] == 'y') {
	$cat_type = 'file gallery';
	$cat_objid = $_REQUEST["galleryId"];
	include ('tiki-tc.php');
}
ask_ticket('list-archives');

$smarty->assign('mid', 'tiki-file_archives.tpl');
$smarty->display("tiki.tpl");

?>
