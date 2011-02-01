<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'file_galleries';
$isUpload = false;

if ( isset($_GET['upload']) or isset($_REQUEST['upload']) ) {
	$isUpload = true;
	unset($_GET['upload']);
	unset($_REQUEST['upload']);
}

require_once ('tiki-setup.php');
if ($prefs['feature_categories'] == 'y') {
	include_once ('lib/categories/categlib.php');
}

$access->check_feature('feature_file_galleries');

include_once ('lib/filegals/filegallib.php');
if ($prefs['feature_groupalert'] == 'y') {
	include_once ('lib/groupalert/groupalertlib.php');
}
@ini_set('max_execution_time', 0); //will not work in safe_mode is on
$auto_query_args = array('galleryId', 'fileId', 'filegals_manager', 'view', 'simpleMode');
function print_progress($msg) {
	global $prefs;
	if ($prefs['javascript_enabled'] == 'y') {
		echo $msg;
		ob_flush();
	}
}

function print_msg($msg, $id) {
	global $prefs;
	if ($prefs['javascript_enabled'] == 'y') {
		echo "<script type='text/javascript'><!--//--><![CDATA[//><!--\n";
		echo "parent.progress('$id','" . htmlentities($msg, ENT_QUOTES, "UTF-8") . "')\n";
		echo "//--><!]]></script>\n";
		ob_flush();
	}
}

$requestGalleryId = null;
if ( isset( $_REQUEST['galleryId'] ) && ! is_array( $_REQUEST['galleryId'] ) ) {
	$requestGalleryId = $_REQUEST['galleryId'];
	$_REQUEST['galleryId'] = array( $requestGalleryId );
}

$fileInfo = null;
$fileId = null;
if ( ! empty( $_REQUEST['fileId'] ) ) {
	$fileId = $_REQUEST['fileId'];

	if ( ! ( $fileInfo = $filegallib->get_file_info( $fileId ) ) ) {
		$smarty->assign('msg', tra("Incorrect param"));
		$smarty->display('error.tpl');
		die;
	}
	if (empty($_REQUEST['galleryId'][0])) {
		$_REQUEST['galleryId'][0] = $fileInfo['galleryId'];
	} elseif ($_REQUEST['galleryId'][0] != $fileInfo['galleryId']) {
		$smarty->assign('msg', tra("Could not find the file requested"));
		$smarty->display('error.tpl');
		die;
	}
}

if (isset($_REQUEST['galleryId'][0])) {
	$gal_info = $tikilib->get_file_gallery((int)$_REQUEST['galleryId'][0]);
	$tikilib->get_perm_object($_REQUEST['galleryId'][0], 'file gallery', $gal_info, true);
	$smarty->assign_by_ref('gal_info', $gal_info);
}

if ( empty( $fileId ) && $tiki_p_upload_files != 'y' && $tiki_p_admin_file_galleries != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied"));
	$smarty->display('error.tpl');
	die;
}
if (isset($_REQUEST['galleryId'][1])) {
	foreach($_REQUEST['galleryId'] as $i => $gal) {
		if (!$i) continue;
		// TODO get the good gal_info
		$perms = $tikilib->get_perm_object($_REQUEST['galleryId'][$i], 'file gallery', $gal_info, false);
		$access->check_permission('tiki_p_upload_files');
	}
}
if ( ! empty( $fileId ) ) {
	if (!empty($fileInfo['lockedby']) && $fileInfo['lockedby'] != $user && $tiki_p_admin_file_galleries != 'y') { // if locked must be the locker
		$smarty->assign('msg', tra(sprintf('The file is locked by %s', $fileInfo['lockedby'])));
		$smarty->display('error.tpl');
		die;
	}
	if (!((!empty($user) && ($user == $fileInfo['user'] || $user == $fileInfo['lockedby'])) || $tiki_p_edit_gallery_file == 'y')) { // must be the owner or the locker or have the perms
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("You do not have permission to edit this file"));
		$smarty->display('error.tpl');
		die;
	}
	if ($gal_info['backlinkPerms'] == 'y' && $filegallib->hasOnlyPrivateBacklinks( $fileId ) ) {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("You do not have permission to edit this file"));
		$smarty->display('error.tpl');
		die;
	}		
	if (isset($_REQUEST['lockedby']) && $fileInfo['lockedby'] != $_REQUEST['lockedby']) {
		if (empty($fileInfo['lockedby'])) {
			$smarty->assign('msg', tra(sprintf('The file has been unlocked meanwhile')));
		} else {
			$smarty->assign('msg', tra(sprintf('The file is locked by %s', $fileInfo['lockedby'])));
		}
		$smarty->display('error.tpl');
		die;
	}
	if ($gal_info['lockable'] == 'y' && empty($fileInfo['lockedby']) && $tiki_p_admin_file_galleries != 'y') {
		$smarty->assign('msg', tra('You must lock the file before editing it'));
		$smarty->display('error.tpl');
		die;
	}
}

$smarty->assign('show', 'n');
if (!empty($_REQUEST['galleryId'][0]) && $prefs['feature_groupalert'] == 'y') {
	$groupforalert = $groupalertlib->GetGroup('file gallery', (int)$_REQUEST['galleryId'][0]);
	if ($groupforalert != '') {
		$showeachuser = $groupalertlib->GetShowEachUser('file gallery', (int)$_REQUEST['galleryId'][0], $groupforalert);
		$listusertoalert = $userlib->get_users(0, -1, 'login_asc', '', '', false, $groupforalert, '');
		$smarty->assign_by_ref('listusertoalert', $listusertoalert['data']);
	}
	$smarty->assign_by_ref('groupforalert', $groupforalert);
	$smarty->assign_by_ref('showeachuser', $showeachuser);
}

if (empty($_REQUEST['returnUrl'])) {
	include ('lib/filegals/max_upload_size.php');
}

// Process an upload here
if ( $isUpload ) {
	check_ticket('upload-file');

	$optionalRequestParams = array(
		'fileId',
		'name',
		'user',
		'description',
		'author',
		'comment',
		'returnUrl',
		'isbatch',
		'deleteAfter',
		'deleteAfter_unit',
		'hit_limit',
		'listtoalert'
	);

	$uploadParams = array(
		'fileInfo' => $fileInfo,
		'galleryId' => $_REQUEST['galleryId'],
		'formId' => $_REQUEST['formId']
	);

	foreach ( $optionalRequestParams as $p ) {
		if ( isset( $_REQUEST[ $p ] ) ) {
			$uploadParams[ $p ] = $_REQUEST[ $p ];
		}
	}

	if ( $fileInfo = $filegallib->actionHandler( 'uploadFile', $uploadParams ) ) {
		$fileId = $fileInfo['fileId'];
	}
}

$smarty->assign_by_ref('fileInfo', $fileInfo);
$smarty->assign('editFileId', (int) $fileId);

// Get the list of galleries to display the select box in the template
$smarty->assign( 'galleryId', empty( $_REQUEST['galleryId'][0] ) ? '' : $_REQUEST['galleryId'][0] );

if ( empty( $fileId ) ) {
	$galleries = $filegallib->getSubGalleries( $requestGalleryId, true, 'upload_files' );
	$smarty->assign_by_ref('galleries', $galleries["data"]);
	$smarty->assign( 'treeRootId', $galleries['parentId'] );
}

if ( $prefs['fgal_limit_hits_per_file'] == 'y' ) {
	$smarty->assign( 'hit_limit', $filegallib->get_download_limit( $fileId ) );
}

$cat_type = 'file';
$cat_objid = (int) $fileId;
$cat_object_exists = (bool) $cat_objid;
include_once ('categorize_list.php');

include_once ('tiki-section_options.php');

ask_ticket('upload-file');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
if ( $prefs['javascript_enabled'] != 'y' or ! $isUpload ) {
	$smarty->assign('mid', 'tiki-upload_file.tpl');
	if ( ! empty( $_REQUEST['filegals_manager'] ) ) {
		$smarty->display("tiki_full.tpl");
	} else {
		$smarty->display("tiki.tpl");
	}
}
