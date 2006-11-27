<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-file_archives.php,v 1.2 2006-11-27 21:41:29 sylvieg Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/filegals/filegallib.php');

if ($feature_file_galleries != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_file_galleries");
	$smarty->display("error.tpl");
	die;
}

if (empty($_REQUEST['fileId']) || !($fileInfo = $filegallib->get_file_info($_REQUEST['fileId']))) {
	$smarty->assign('msg', tra("Incorrect param"));
	$smarty->display('error.tpl');
	die;
}

 if (!($tiki_p_admin == 'y' || $tiki_p_admin_file_galleries == 'y' || $tikilib->user_has_perm_on_object($user, $fileInfo['galleryId'], 'file gallery', 'tiki_p_view_file_gallery'))) {
	$smarty->assign('msg', tra("Permission denied you cannot edit this file"));
	$smarty->display("error.tpl");
	die;
}
$tiki_p_download_files = $tikilib->user_has_perm_on_object($user, $fileInfo['galleryId'], 'file gallery', 'tiki_p_download_files');

$gal_info = $tikilib->get_file_gallery($fileInfo['galleryId']);

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
	if ($feature_ticketlib2 != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    	key_check($area);
		$filegallib->remove_file($removeInfo, $user, $gal_info);
	} else {
		key_get($area);
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

if (!isset($_REQUEST['sort_mode']))
	$_REQUEST['sort_mode'] = 'created_desc';
$smarty->assign_by_ref('sort_mode', $_REQUEST['sort_mode']);
if (!isset($_REQUEST['find']))
	$_REQUEST['find'] = '';
$smarty->assign('find', $_REQUEST['find']);
if (!isset($_REQUEST['offset']))
	$_REQUEST["offset"] = 0;
$smarty->assign_by_ref('offset', $_REQUEST['offset']);

$files = $filegallib->get_archives($_REQUEST['fileId']);
$smarty->assign_by_ref('files', $files['data']);

$cant_pages = ceil($files['cant'] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign_by_ref('maxRecords', $maxRecords);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));
if ($files["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('file_info', $fileInfo);
$gal_info['show_comment'] = 'y';
$gal_info['show_description'] = 'n';
$gal_info['show_created'] = 'y';
$gal_info['show_modified'] = 'n';
$gal_info['show_lockedby'] = 'n';
$smarty->assign_by_ref('gal_info', $gal_info);

$section = 'file_galleries';
include_once ('tiki-section_options.php');

if ($feature_theme_control == 'y') {
	$cat_type = 'file gallery';
	$cat_objid = $_REQUEST["galleryId"];
	include ('tiki-tc.php');
}
ask_ticket('list-archives');

$smarty->assign('mid', 'tiki-file_archives.tpl');
$smarty->display("tiki.tpl");

?>
