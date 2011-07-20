<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'file_galleries';
require_once ('tiki-setup.php');
include_once ('lib/filegals/filegallib.php');
$access->check_feature(array('feature_file_galleries', 'feature_file_galleries_batch'));

// Now check permissions to access this page
$access->check_permission('tiki_p_batch_upload_file_dir');

// check directory path
if (!isset($prefs['fgal_batch_dir']) or !is_dir($prefs['fgal_batch_dir'])) {
	$msg = tra("Incorrect directory chosen for batch upload of files.") . "<br />";
	if ($tiki_p_admin == 'y') {
		$msg.= tra("Please setup that dir on ") . '<a href="tiki-admin.php?page=fgal">' . tra('File Galleries Admin Panel') . '</a>.';
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
// recursively get all files from all subdirectories
function getDirContent($sub) {
	global $disallowed_types;
	global $a_file;
	global $a_path;
	global $filedir, $smarty;

	$tmp = rtrim($filedir . '/' . $sub, '/');

	if (false === $allfile = scandir($tmp)) {
		$msg = tra("Invalid directory name");
		$smarty->assign('msg', $msg);
		$smarty->display("error.tpl");
		die;
	}

	foreach($allfile as $filefile) {
		if ('.' === $filefile{0}) {
			continue;
		}

		if (is_dir($tmp . "/" . $filefile)) {
			if ((substr($sub, -1) != "/") && (substr($sub, -1) != "\\")) {
				$sub.= '/';
			}
			getDirContent($sub . $filefile);
		} elseif (!in_array(strtolower(substr($filefile, -(strlen($filefile) - strrpos($filefile, ".")))) , $disallowed_types)) {
			$a_file[] = $filefile;
			$a_path[] = $sub;
		}
	}
}
// build a complete list of all files on filesystem including all necessary file info
function buildFileList() {
	global $a_file;
	global $a_path;
	global $filedir, $smarty;
	global $filestring;
	getDirContent('');
	$totfile = count($a_file); // total file number
	$totalsize = 0;
	// build file data array
	foreach ($a_file as $x => $file) {
		$path = $a_path[$x];

		// get root dir
		$filedir = rtrim($filedir, '/');

		$tmp = $filedir;
		// add any subdir names
		if ($path <> "") {
			$tmp.= $path;
		}
		// get file information
		$filesize = @filesize($tmp . '/' . $file);
		$filestring[$x][0] = $file;
		if ($path) {
			$filestring[$x][0] = $path . '/' . $file;
		}
		$filestring[$x][1] = $filesize;
		// type is string after last dot
		$tmp = strtolower(substr($file, -(strlen($file) - 1 - strrpos($file, "."))));
		$filestring[$x][2] = $tmp;
		$totalsize+= $filesize;
	}
	$smarty->assign('totfile', $totfile);
	$smarty->assign('totalsize', $totalsize);
	$smarty->assign('filestring', $filestring);
}

if (isset($_REQUEST["batch_upload"]) and isset($_REQUEST['files']) and is_array($_REQUEST['files'])) {
	// default is: file names from request
	$fileArray = $_REQUEST['files'];
	$totfiles = count($fileArray);

	// if ALL is given, get all the files from the filesystem (stored in $a_file[] already)
	if ($totfiles == 1) {
		if ($fileArray[0] == "ALL") {
			getDirContent('');
			$fileArray = $a_file;
			$filePathArray = $a_path;
			$totfiles = count($fileArray);
		}
	}
	// for subdirToSubgal we need all existing sub galleries for the current gallery
	$subgals = array();
	if (isset($_REQUEST["subdirTosubgal"])) {
		$subgals = $filegallib->get_subgalleries(0, 9999, "name_asc", '', $_REQUEST["galleryId"]);
	}

	// cycle through all files to upload
	foreach ($fileArray as $x => $file) {
		if (!isset($filePathArray[$x])) {
			$path = '';
		} else if ($filePathArray[$x] != "") {
			$path = $filePathArray[$x] . '/';
		} else {
			// if there is a path in file name, move it to the path array
			if (strrpos($file, "/") > 0) {
				$path = substr($file, 0, strrpos($file, "/") + 1);
				$file = substr($file, strrpos($file, "/") + 1);
			}
		}

		$filepath = $filedir . $path . $file;
		$filesize = @filesize($filepath);

		$path_parts = pathinfo($filepath);
		$ext = strtolower($path_parts["extension"]);
		include_once ('lib/mime/mimetypes.php');
		$type = $mimetypes["$ext"];

		$result = $filegallib->handle_batch_upload($_REQUEST['galleryId'], array(
			'source' => $filepath,
			'size' => $filesize,
			'type' => $type,
			'name' => $path_parts['basename'],
		), $ext);

		if (isset($result['error'])) {
			$feedback[] = "!!!" . tr('Upload was not successful for %0 (%1)', $path_parts['basename'], $result['error']);
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
			$file = preg_replace('/.*([^\/]*)$/U', '$1', $file);
			$name = $file;
			// remove extension from name field
			if (isset($_REQUEST["removeExt"])) {
				$name = substr($name, 0, strrpos($name, "."));
			}
			$fileId = $filegallib->insert_file($tmpGalId, $name, $tmpDesc, $file, $result['data'], $filesize, $type, $user, $result['fhash']);
			if ($fileId) {
				$feedback[] = tra('Upload was successful') . ': ' . $name;
				if (@unlink($filepath)) {
					$feedback[] = sprintf(tra('File %s removed from Batch directory.') , $name);
				} else {
					$feedback[] = "!!! " . sprintf(tra('Impossible to remove file %s from Batch directory.') , $name);
				}
			}
		}
	}
}

$a_file = array();
$a_path = array();
buildFileList();
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
include_once ('tiki-section_options.php');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template
$smarty->assign('mid', 'tiki-batch_upload_files.tpl');
$smarty->display("tiki.tpl");
