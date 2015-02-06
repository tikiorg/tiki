<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'file_galleries';
require_once ('tiki-setup.php');
$filegallib = TikiLib::lib('filegal');

$access->check_feature('feature_file_galleries');

if (empty($_REQUEST['fileId']) || !($fileInfo = $filegallib->get_file_info($_REQUEST['fileId']))) {
	$smarty->assign('msg', tra("Incorrect param"));
	$smarty->display('error.tpl');
	die;
}

$gal_info = $filegallib->get_file_gallery($fileInfo['galleryId']);

$tikilib->get_perm_object($fileInfo['galleryId'], 'file gallery', $gal_info, true);

if (!($tiki_p_admin_file_galleries == 'y' || $tiki_p_view_file_gallery == 'y')) {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to edit this file"));
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
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("You do not have permission to remove files from this gallery"));
		$smarty->display("error.tpl");
		die;
	}
	$access->check_authenticity(($removeInfo['archiveId']? tra('Remove archive: '): tra('Remove file gallery: ')) . (!empty($removeInfo['name'])?$removeInfo['name'].' - ':'').$removeInfo['filename']);
	$filegallib->remove_file($removeInfo, $gal_info);
}
if (isset($_REQUEST['delsel_x']) && !empty($_REQUEST['file'])) {
	check_ticket('list-archives');
	foreach (array_values($_REQUEST['file']) as $fileId) {
		if (!($removeInfo = $filegallib->get_file_info($fileId))) {
			$smarty->assign('msg', tra("Incorrect param"));
			$smarty->display('error.tpl');
			die;
		}		
		$filegallib->remove_file($removeInfo, $gal_info);		
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

$gal_info = array_merge($filegallib->default_file_gallery(), $gal_info);
$smarty->assign_by_ref('gal_info', $gal_info);

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
