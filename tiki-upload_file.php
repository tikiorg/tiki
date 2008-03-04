<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-upload_file.php,v 1.65.2.3 2008-03-04 14:41:48 sept_7 Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
include_once ('lib/categories/categlib.php');
include_once ('lib/filegals/filegallib.php');
@ini_set('max_execution_time', 0); //will not work in safe_mode is on

if ($prefs['feature_file_galleries'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_file_galleries");
	$smarty->display("error.tpl");
	die;
}

if (!empty($_REQUEST['fileId'])) {
	if (!($fileInfo = $filegallib->get_file_info($_REQUEST['fileId']))) {
		$smarty->assign('msg', tra("Incorrect param"));
		$smarty->display('error.tpl');
		die;
	}
	if (empty($_REQUEST['galleryId'])) {
		$_REQUEST['galleryId'] = $fileInfo['galleryId'];
	} elseif ($_REQUEST['galleryId'] != $fileInfo['galleryId']) {
		$smarty->assign('msg', tra("Could not find the file requested"));
		$smarty->display('error.tpl');
		die;
	}
	$gal_info = $tikilib->get_file_gallery((int)$_REQUEST["galleryId"]);
}	

$tikilib->get_perm_object($_REQUEST["galleryId"], 'file gallery', $gal_info, true);

if (!empty($_REQUEST['galleryId'])) { // perms of the gallery can overwrite general perms
	$smarty->assign('individual', 'n');
	if ($userlib->object_has_one_permission($_REQUEST["galleryId"], 'file gallery')) {
		$smarty->assign('individual', 'y');
		if ($tiki_p_admin != 'y') {
			// Now get all the permissions that are set for this type of permissions 'file gallery'
			$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'file galleries');
			foreach ($perms["data"] as $perm) {
				$permName = $perm["permName"];
				if ($userlib->object_has_permission($user, $_REQUEST["galleryId"], 'file gallery', $permName)) {
					$$permName = 'y';
					$smarty->assign("$permName", 'y');
				} else {
					$$permName = 'n';
					$smarty->assign("$permName", 'n');
				}
			}
		}
	}
	if ($tiki_p_admin_file_galleries == 'y') {
		$tiki_p_upload_files = 'y';
		$tiki_p_edit_gallery_file = 'y';
	}
}

if (!empty($_REQUEST['galleryId']) && empty($_REQUEST['fileId']) && $tiki_p_upload_files != 'y' && $tiki_p_admin_file_galleries != 'y') {
	$smarty->assign('msg', tra("Permission denied you can upload files but not to this file gallery"));
	$smarty->display('error.tpl');
	die;
}
if (!empty($_REQUEST['fileId'])) {
	if (!empty($fileInfo['lockedby']) && $fileInfo['lockedby'] != $user && $tiki_p_admin_file_galleries != 'y') { // if locked must be the locker
		$smarty->assign('msg', tra(sprintf('The file is locked by %s', $fileInfo['lockedby'])));
		$smarty->display('error.tpl');
		die;
	}
	if (!((!empty($user) && ($user == $fileInfo['user'] || $user == $fileInfo['lockedby'])) || $tiki_p_edit_gallery_file == 'y')) {// must be the owner or the locker or have the perms
		$smarty->assign('msg', tra("Permission denied you can edit this file"));
		$smarty->display('error.tpl');
		die;
	}
}

	

$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1 = str_replace("tiki-upload_file", "tiki-download_file", $foo["path"]);
$smarty->assign('url_browse', $tikilib->httpPrefix(). $foo1);
$url_browse = $tikilib->httpPrefix(). $foo1;

// create direct download path for podcasts
$podcast_url = str_replace("tiki-upload_file.php", "", $foo["path"]);
$podcast_url = $tikilib->httpPrefix().$podcast_url.$prefs['fgal_podcast_dir'];

if (!isset($_REQUEST["description"]))
	$_REQUEST["description"] = '';
if (!isset($_REQUEST['author']))
	$_REQUEST['author'] = '';

$smarty->assign('show', 'n');

if (isset($_REQUEST['fileId']))
	$editFileId = $_REQUEST['fileId'];
$editFile = false;
if (!empty($editFileId)) {
	if (!empty($_REQUEST['name']))
		$fileInfo['name']=$_REQUEST['name'];
	if (!empty($_REQUEST['description']))
		$fileInfo['description']=$_REQUEST['description'];
	if (!empty($_REQUEST['user']))
		$fileInfo['user']=$_REQUEST['user'];
	if (!empty($_REQUEST['author']))
		$fileInfo['author']=$_REQUEST['author'];

	$smarty->assign_by_ref('fileInfo',$fileInfo);
	$smarty->assign('editFileId',$editFileId);
	$editFile = true;
}

if (!empty($_REQUEST['galleryId'])) {
	$gal_info = $tikilib->get_file_gallery((int)$_REQUEST["galleryId"]);
	$smarty->assign_by_ref('gal_info', $gal_info);
	$podCastGallery = $filegallib->isPodCastGallery((int)$_REQUEST["galleryId"], $gal_info);
}

// Process an upload here
if (isset($_REQUEST["upload"]) && !empty($_REQUEST['galleryId'])) {
	check_ticket('upload-file');

	$error_msg = '';

	$errors = array();
	$uploads = array();

	$didFileReplace = false;
	if (!isset($_REQUEST['comment']))
		$_REQUEST['comment'] = '';
	for ($i = 1; $i <= 6; $i++) {
		// We process here file uploads
		if (isset($_FILES["userfile$i"]) && !empty($_FILES["userfile$i"]['name'])) {
			// Were there any problems with the upload?  If so, report here.
			if (!is_uploaded_file($_FILES["userfile$i"]['tmp_name'])) {
				$errors[] = tra('Upload was not successful').': '.$tikilib->uploaded_file_error($_FILES["userfile$i"]['error']);
				continue;
			}
				

			// Check the name
			if (!empty($prefs['fgal_match_regex'])) {
				if (!preg_match('/'.$prefs['fgal_match_regex'].'/', $_FILES["userfile$i"]['name'], $reqs)) {
					$errors[] = tra('Invalid filename (using filters for filenames)'). ': ' . $_FILES["userfile$i"]['name'];
				}
			}

			if (!empty($prefs['fgal_nmatch_regex'])) {
				if (preg_match('/'.$prefs['fgal_nmatch_regex'].'/', $_FILES["userfile$i"]['name'], $reqs)) {
					$errors[] = tra('Invalid filename (using filters for filenames)'). ': ' . $_FILES["userfile$i"]['name'];
				}
			}

			$name = $_FILES["userfile$i"]['name'];

			if (isset($_REQUEST["isbatch"]) && $_REQUEST["isbatch"] == 'on' && strtolower(substr($name, strlen($name) - 3)) == 'zip') {
				if ($tiki_p_batch_upload_files == 'y') {
					$filegallib->process_batch_file_upload($_REQUEST["galleryId"], $_FILES["userfile$i"]['tmp_name'],
						$user, $_REQUEST["description"]);

				  	header ("location: tiki-list_file_gallery.php?galleryId=" . $_REQUEST["galleryId"]);					die;
				} else {
					$smarty->assign('msg', tra('No permission to upload zipped file packages'));

					$smarty->display("error.tpl");
					die;
				}
			}

			$file_name = $_FILES["userfile$i"]['name'];
			$file_tmp_name = $_FILES["userfile$i"]['tmp_name'];
			$tmp_dest = $prefs['tmpDir'] . "/" . $file_name.".tmp";
			if (!move_uploaded_file($file_tmp_name, $tmp_dest)) {
				$smarty->assign('msg', tra('Errors detected'));
				$smarty->display("error.tpl");
				die();
			}
			
			$fp = fopen($tmp_dest, "rb");

			if (!$fp) {
				$errors[] = tra('Cannot read file:').' '.$tmp_dest;
			}

			$data = '';
			$fhash = '';

			if (($prefs['fgal_use_db'] == 'n') || ($podCastGallery)) {
				$fhash = md5($name = $_FILES["userfile$i"]['name']);
				$fhash = md5(uniqid($fhash));

				// for podcast galleries add the extension so the
				// file can be called directly if name is known,
				if ($podCastGallery) {
					$path_parts = pathinfo($_FILES["userfile$i"]['name']);
					if (in_array(strtolower($path_parts["extension"]),array("m4a", "mp3", "mov", "mp4", "m4v", "pdf"))) {
						$fhash .= ".".strtolower($path_parts["extension"]);
					}
					$savedir=$prefs['fgal_podcast_dir'];
				} else {
					$savedir=$prefs['fgal_use_dir'];
				}
				@$fw = fopen($savedir . $fhash, "wb");
				if (!$fw) {
					$errors[] = tra('Cannot write to this file:').$savedir.$fhash;
				}
			}

			while (!feof($fp)) {
				if (($prefs['fgal_use_db'] == 'y') && (!$podCastGallery)) {
					$data .= fread($fp, 8192 * 16);
				} else {
					if (($data = fread($fp, 8192 * 16)) === false) {
						$errors[] = tra('Cannot read the file:').' '.$tmp_dest;
					}

					fwrite($fw, $data);
				}
			}

			fclose ($fp);
			// remove file after copying it to the right location or database
			@unlink ($tmp_dest);

			if (($prefs['fgal_use_db'] == 'n') || ($podCastGallery)) {
				fclose ($fw);

				$data = '';
			}

			$size = $_FILES["userfile$i"]['size'];
			$name = $_FILES["userfile$i"]['name'];

			$type = $_FILES["userfile$i"]['type'];

			if (preg_match('/.flv$/',$name)) { $type="video/x-flv"; }

			if (count($errors)) {
				break;
			}

			if (!$size) {
				$errors[] = tra('Warning: Empty file:').'  '.$name.'. '.tra('Please re-upload your file');
			}
			if (($prefs['fgal_use_db'] == 'y') && (!$podCastGallery)) {
				if (!isset($data) || strlen($data) < 1) {
					$errors[] = tra('Warning: Empty file:'). ' ' . $name.'. '.tra('Please re-upload your file');
				}
			}

			if (!isset($_REQUEST['name']))
				$_REQUEST['name'] = $name;
			if (empty($_REQUEST['user']))
				$_REQUEST['user'] = $user;

			$fileInfo['filename'] = $file_name;

			if (isset($data)) {
				if ($editFile) {
					$didFileReplace = true;
					$fileId = $filegallib->replace_file($editFileId, $_REQUEST["name"], $_REQUEST["description"], $name, $data, $size, $type, $_REQUEST['user'], $fhash, $_REQUEST['comment'], $gal_info, $didFileReplace, $_REQUEST['author'], $fileInfo['lastModif'], $fileInfo['lockedby']);
				}
				else
				  $fileId= $filegallib->insert_file($_REQUEST["galleryId"], $_REQUEST["name"], $_REQUEST["description"], $name, $data, $size, $type, $_REQUEST['user'], $fhash, '', $_REQUEST['author']);
				if (!$fileId) {
					$errors[] = tra('Upload was not successful. Duplicate file content'). ': ' . $name;
					if (($prefs['fgal_use_db'] == 'n') || ($podCastGallery)) {
						@unlink($savedir . $fhash);
					}
					
				}

				if (count($errors) == 0) {
					$aux['name'] = $name;
					
					$aux['size'] = $size;
					$aux['fileId'] = $fileId;
					if ($podCastGallery) {
						$aux['dllink'] = $podcast_url.$fhash;
					} else {
						$aux['dllink'] = $url_browse."?fileId=".$fileId;
					}
					$uploads[] = $aux;
					$cat_type = 'file';
					$cat_objid = $fileId;
					$cat_desc = substr($_REQUEST["description"], 0, 200);
					$cat_name =  empty($_REQUEST['name'])? $name: $_REQUEST['name'];
					$cat_href = $aux['dllink'];
					include_once ('categorize.php');
				}
			}
		}
	}

	if ($editFile && !$didFileReplace) {
	  $filegallib->replace_file($editFileId, $_REQUEST['name'], $_REQUEST['description'], $fileInfo['filename'], $fileInfo['data'], $fileInfo['filesize'], $fileInfo['filetype'], $fileInfo['user'], $fileInfo['path'], $_REQUEST['comment'], $gal_info, $didFileReplace, $_REQUEST['author'], $fileInfo['lastModif'], $fileInfo['lockedby']);
		$fileChangedMessage = tra('File update was successful').': '.$_REQUEST['name'];
		$smarty->assign('fileChangedMessage',$fileChangedMessage);
		$cat_type = 'file';
		$cat_objid = $editFileId;
		$cat_desc = substr($_REQUEST["description"], 0, 200);
		$cat_name = empty($fileInfo['name'])?$fileInfo['filename']: $fileInfo['name'];
		$cat_href = $podCastGallery?$podcast_url.$fhash: "$url_browse?fileId=".$editFileId; 
		include_once ('categorize.php');
	}

	$smarty->assign('errors', $errors);
	$smarty->assign('uploads', $uploads);
}

// Get the list of galleries to display the select box in the template
if (isset($_REQUEST["galleryId"])) {
	$smarty->assign_by_ref('galleryId', $_REQUEST["galleryId"]);
} else {
	$smarty->assign('galleryId', '');
}

if (empty($_REQUEST['fileId'])) {
	if ($tiki_p_admin_file_galleries != 'y') {
		$galleries = $tikilib->list_visible_file_galleries(0, -1, 'name_asc', $user, '');
	} else {
		$galleries = $filegallib->list_file_galleries(0, -1, 'name_asc', $user, '');
	}
	$temp_max = count($galleries["data"]);
	for ($i = 0; $i < $temp_max; $i++) {
		if ($userlib->object_has_one_permission($galleries["data"][$i]["galleryId"], 'file gallery')) {
			$galleries["data"][$i]["individual"] = 'y';
			if ($userlib->object_has_permission($user, $galleries["data"][$i]["galleryId"], 'file gallery', 'tiki_p_upload_files')) {
				$galleries["data"][$i]["individual_tiki_p_upload_files"] = 'y';
			} else {
				$galleries["data"][$i]["individual_tiki_p_upload_files"] = 'n';
			}
			if ($tiki_p_admin == 'y' || $userlib->object_has_permission($user, $galleries["data"][$i]["galleryId"], 'file gallery',	'tiki_p_admin_file_galleries')) {
				$galleries["data"][$i]["individual_tiki_p_upload_files"] = 'y';
			}
		} else {
			$galleries["data"][$i]["individual"] = 'n';
		}
	}
	$smarty->assign_by_ref('galleries', $galleries["data"]);
}

if ($tiki_p_admin_file_galleries == 'y' || $tiki_p_admin == 'y') {
	$users = $tikilib->list_users(0, -1, 'login_asc');
	$smarty->assign_by_ref('users', $users['data']);
}

$cat_type = 'file';
$cat_objid = empty($_REQUEST['fileId'])? 0: $_REQUEST['fileId'];
include_once('categorize_list.php');

$section = 'file_galleries';
include_once ('tiki-section_options.php');

ask_ticket('upload-file');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid','tiki-upload_file.tpl');
if (isset($_REQUEST['filegals_manager'])) {
	$smarty->assign('filegals_manager','y');
	$smarty->display("tiki-print.tpl");
}  else {
	$smarty->display("tiki.tpl");
}

?>
