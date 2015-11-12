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
$access->check_feature(array('feature_file_galleries', 'feature_file_galleries_batch'));
//get_strings tra('Directory batch')
// Now check permissions to access this page
$access->check_permission('tiki_p_batch_upload_file_dir');

$auto_query_args = array( 'galleryId' );

// check directory path
if (!isset($prefs['fgal_batch_dir']) or !is_dir($prefs['fgal_batch_dir'])) {
	$msg = tra("Incorrect directory chosen for batch upload of files.") . "<br />";
	if ($tiki_p_admin == 'y') {
		$msg.= tra("Please setup that dir on ") . '<a href="tiki-admin.php?page=fgal">' . tra('File Galleries Configuration Panel') . '</a>.';
	} else {
		$msg.= tra("Please contact the website administrator.");
	}
	$smarty->assign('msg', $msg);
	$smarty->display("error.tpl");
	die;
} else {
	$filedir = $prefs['fgal_batch_dir'];
}
// We need a galleryId
if (!isset($_REQUEST['galleryId'])) {
	$_REQUEST['galleryId'] = 0;
	$podCastGallery = false;
} else {
	$gal_info = $filegallib->get_file_gallery($_REQUEST["galleryId"]);
	$podCastGallery = $filegallib->isPodCastGallery($_REQUEST["galleryId"], $gal_info);
}
$smarty->assign('filedir', $filedir);
$a_file = $filestring = $feedback = array();
$a_path = array();
$disallowed_types = array(
	'.php',
	'php3',
	'php4',
	'phtml',
	'phps',
	'.py',
	'.pl',
	'.sh',
	'php~'
); // list of filetypes you DO NOT want to show

/**
 * recursively get all files from all subdirectories
 *
 * @param $dir
 * @return array
 * @throws Exception
 */
function batchUploadDirContent($dir)
{
	global $disallowed_types;

	$files = [];

	if (false === $allfile = scandir($dir)) {
		throw new Exception(tra("Invalid directory name"));
	}

	foreach ($allfile as $filefile) {
		if ('.' === $filefile{0}) {
			continue;
		}

		if (is_dir($dir . "/" . $filefile)) {
			$files = array_merge(batchUploadDirContent($dir . DIRECTORY_SEPARATOR . $filefile), $files);

		} elseif (!in_array(strtolower(substr($filefile, -(strlen($filefile) - strrpos($filefile, ".")))), $disallowed_types)) {
			$files[] =  $dir . DIRECTORY_SEPARATOR . $filefile;
		}
	}
	return $files;
}

/**
 * build a complete list of all files in $prefs['fgal_batch_dir'] including all necessary file info
 *
 * @throws Exception
 */
function batchUploadFileList()
{

	global $filedir;
	global $filestring;
	// get root dir
	$filedir = rtrim($filedir, '/');

	$files = batchUploadDirContent($filedir);

	$totfile = count($files); // total file number
	$totalsize = 0;
	// build file data array
	foreach ($files as $file) {

		// get file information
		$filesize = @filesize($file);
		$totalsize+= $filesize;

		$filestring[] = [
			'file' => $file,
			'size' => $filesize,
			'ext' => strtolower(substr($file, -(strlen($file) - 1 - strrpos($file, ".")))),
			'writable' => is_writable($file),
		];
	}
	$smarty = TikiLib::lib('smarty');
	$smarty->assign('totfile', $totfile);
	$smarty->assign('totalsize', $totalsize);
	$smarty->assign('filestring', $filestring);
}

if (isset($_REQUEST["batch_upload"]) and isset($_REQUEST['files']) and is_array($_REQUEST['files'])) {

	@ini_set('max_execution_time', 0); // will not work if safe_mode is on

	// default is: file names from request
	$fileArray = $_REQUEST['files'];
	$totfiles = count($fileArray);

	// for subdirToSubgal we need all existing dir galleries for the current gallery
	$subgals = array();
	if (isset($_REQUEST["subdirTosubgal"])) {
		$subgals = $filegallib->get_subgalleries(0, 9999, "name_asc", '', $_REQUEST["galleryId"]);
	}

	// cycle through all files to upload
	foreach ($fileArray as $x => $file) {

		//add meadata
		$metadata = $filegallib->extractMetadataJson($file);

		$path_parts = pathinfo($file);
		$ext = strtolower($path_parts["extension"]);
		include_once ('lib/mime/mimetypes.php');
		global $mimetypes;
		$type = $mimetypes["$ext"];
		$filesize = @filesize($file);

		$result = $filegallib->handle_batch_upload(
			$_REQUEST['galleryId'],
			array(
				'source' => $file,
				'size' => $filesize,
				'type' => $type,
				'name' => $path_parts['basename'],
			),
			$ext
		);

		if (isset($result['error'])) {
			$feedback[] = '<span class="text-danger">' . tr('Upload was not successful for "%0"', $path_parts['basename']) . '<br>(' . $result['error'] . ')</span>';
		} else {
			// check which gallery to upload to
			$tmpGalId = (int)$_REQUEST["galleryId"];
			// if subToDesc is set, set description:
			if (isset($_REQUEST["subToDesc"])) {
				// get last subdir 'last' from 'some/path/last'
				$tmpDesc = preg_replace('/.*([^\/]*)\/([^\/]+)$/U', '$1', $file);
			} else {
				$tmpDesc = '';
			}
			// remove possible path from filename
			$name = $path_parts['basename'];

			$fileId = $filegallib->insert_file(
				$tmpGalId, $name, $tmpDesc, $name, $result['data'], $filesize, $type,
				$user, $result['fhash'], null, null, null, null, null, null, $metadata
			);
			if ($fileId) {
				$feedback[] = tra('Upload was successful') . ': ' . $name;
				@unlink($file);	// seems to return false sometimes even if the file was deleted
				if (!file_exists($file)) {
					$feedback[] = sprintf(tra('File %s removed from Batch directory.'), $file);
				} else {
					$feedback[] = '<span class="text-danger">' . sprintf(tra('Impossible to remove file %s from Batch directory.'), $file) . '</span>';
				}
			}
		}
	}
}

try {
	batchUploadFileList();
} catch (Exception $e) {
	TikiLib::lib('errorreport')->report($e->getMessage());
}

$smarty->assign('feedback', $feedback);
if (isset($_REQUEST["galleryId"])) {
	$smarty->assign_by_ref('galleryId', $_REQUEST["galleryId"]);
	$smarty->assign('permAddGallery', 'n');
	if ($tiki_p_admin_file_galleries == 'y' || $userlib->object_has_permission($user, $_REQUEST["galleryId"], 'image gallery', 'tiki_p_create_file_galleries')) {
		$smarty->assign('permAddGallery', 'y');
	}
} else {
	$smarty->assign('galleryId', '');
}
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
