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
$filegalbatchlib = TikiLib::lib('filegalbatch');

// We need a galleryId
if (empty($_REQUEST['galleryId'])) {
	$_REQUEST['galleryId'] = $prefs['fgal_root_id'];
}

// get gallery's perms
$gal_info = $filegallib->get_file_gallery($_REQUEST["galleryId"]);
$tikilib->get_perm_object($_REQUEST['galleryId'], 'file gallery', $gal_info);	// needs info to get special user gallery perms etc

$access->check_feature(array('feature_file_galleries', 'feature_file_galleries_batch'));
//get_strings tra('Directory batch')
// Now check permissions to access this page
$access->check_permission('tiki_p_batch_upload_file_dir');

$auto_query_args = array( 'galleryId' );

// check directory path
if (empty($prefs['fgal_batch_dir']) or !is_dir($prefs['fgal_batch_dir'])) {
	$msg = tra("Incorrect directory chosen for batch upload of files.") . "<br />";
	if ($tiki_p_admin == 'y') {
		$msg.= tra("Please setup that dir on ") . '<a href="tiki-admin.php?page=fgal">' . tra('File Galleries Configuration Panel') . '</a>.';
	} else {
		$msg.= tra("Please contact the website administrator.");
	}
	$smarty->assign('msg', $msg);
	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST['batch_upload']) and isset($_REQUEST['files']) and is_array($_REQUEST['files'])) {

	@ini_set('max_execution_time', 0); // will not work if safe_mode is on

	// default is: file names from request
	$feedback = $filegalbatchlib->processBatchUpload($_REQUEST['files'], (int) $_REQUEST['galleryId'],
			[
				'subToDesc' => isset($_REQUEST['subToDesc']),
				'subdirToSubgal' => isset($_REQUEST['subdirToSubgal']),
				'createSubgals' => isset($_REQUEST['createSubgals']),
				'subdirIntegerToSubgalId' => isset($_REQUEST['subdirIntegerToSubgalId']),
			]
	);

} else {
	$feedback = [];
}

try {
	$filelist = $filegalbatchlib->batchUploadFileList();
	$smarty->assign('filelist', $filelist);

} catch (Exception $e) {
	TikiLib::lib('errorreport')->report($e->getMessage());
}

$smarty->assign('feedback', $feedback);

$smarty->assign('galleryId', $_REQUEST["galleryId"]);

$galleries = $filegallib->list_file_galleries(0, -1, 'name_asc', $user, '', $prefs['fgal_root_id']);
$temp_max = count($galleries["data"]);
for ($i = 0; $i < $temp_max; $i++) {
	if ($userlib->object_has_one_permission($galleries["data"][$i]["galleryId"], 'file gallery')) {
		$galleries["data"][$i]["individual"] = 'y';
		$galleries["data"][$i]["individual_tiki_p_batch_upload_file_dir"] = 'n';
		if ($tiki_p_admin == 'y' || $userlib->object_has_permission($user, $galleries["data"][$i]["galleryId"], 'file gallery', 'tiki_p_batch_upload_file_dir') || $userlib->object_has_permission($user, $galleries["data"][$i]["galleryId"], 'file gallery', 'tiki_p_admin_file_galleries')) {
			$galleries["data"][$i]["individual_tiki_p_batch_upload_file_dir"] = 'y';
		}
	} else {
		$galleries["data"][$i]["individual"] = 'n';
	}
}
$smarty->assign_by_ref('galleries', $galleries["data"]);
$smarty->assign('treeRootId', $prefs['fgal_root_id']);
include_once ('tiki-section_options.php');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template
$smarty->assign('mid', 'tiki-batch_upload_files.tpl');
$smarty->display("tiki.tpl");
