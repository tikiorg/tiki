<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'file_galleries';
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
if (!empty($_REQUEST['fileId'])) {
	if (!($fileInfo = $filegallib->get_file_info($_REQUEST['fileId']))) {
		$smarty->assign('msg', tra("Incorrect param"));
		$smarty->display('error.tpl');
		die;
	}
	if (!empty($_REQUEST['galleryId']) && !is_array($_REQUEST['galleryId'])) {
		$_REQUEST['galleryId'] = array($_REQUEST['galleryId']);
	}
	if (empty($_REQUEST['galleryId'][0])) {
		$_REQUEST['galleryId'][0] = $fileInfo['galleryId'];
	} elseif ($_REQUEST['galleryId'][0] != $fileInfo['galleryId']) {
		$smarty->assign('msg', tra("Could not find the file requested"));
		$smarty->display('error.tpl');
		die;
	}
} elseif (isset($_REQUEST['galleryId']) && !is_array($_REQUEST['galleryId'])) {
	$_REQUEST['galleryId'] = array($_REQUEST['galleryId']);
}

if (isset($_REQUEST['galleryId'][0])) {
	$gal_info = $tikilib->get_file_gallery((int)$_REQUEST['galleryId'][0]);
	$tikilib->get_perm_object($_REQUEST['galleryId'][0], 'file gallery', $gal_info, true);
}

if (empty($_REQUEST['fileId']) && $tiki_p_upload_files != 'y' && $tiki_p_admin_file_galleries != 'y') {
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
if (!empty($_REQUEST['fileId'])) {
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
	if ($gal_info['backlinkPerms'] == 'y' && $filegallib->hasOnlyPrivateBacklinks($_REQUEST['fileId'])) {
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

$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1 = str_replace("tiki-upload_file", "tiki-download_file", $foo["path"]);
$smarty->assign('url_browse', $tikilib->httpPrefix() . $foo1);
$url_browse = $tikilib->httpPrefix() . $foo1;
// create direct download path for podcasts
$podcast_url = str_replace("tiki-upload_file.php", "", $foo["path"]);
$podcast_url = $tikilib->httpPrefix() . $podcast_url . $prefs['fgal_podcast_dir'];
if (!isset($_REQUEST["description"])) $_REQUEST["description"] = '';
if (!isset($_REQUEST['author'])) $_REQUEST['author'] = '';
if (isset($_REQUEST['hit_limit'])) $_REQUEST['hit_limit'] = (int)$_REQUEST['hit_limit'];
else $_REQUEST['hit_limit'] = 0;
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

if (isset($_REQUEST['fileId'])) {$editFileId = $_REQUEST['fileId'];} else {$editFileId = 0;}
$editFile = false;
if (!empty($editFileId)) {
	if (!empty($_REQUEST['name'][0])) $fileInfo['name'] = $_REQUEST['name'][0];
	if (!empty($_REQUEST['description'][0])) $fileInfo['description'] = $_REQUEST['description'][0];
	if (!empty($_REQUEST['user'][0])) $fileInfo['user'] = $_REQUEST['user'][0];
	if (!empty($_REQUEST['author'][0])) $fileInfo['author'] = $_REQUEST['author'][0];
	$smarty->assign_by_ref('fileInfo', $fileInfo);
	$editFile = true;
}
$smarty->assign('editFileId', $editFileId);
if (!empty($_REQUEST['galleryId'][0])) {
	//$gal_info = $tikilib->get_file_gallery((int)$_REQUEST["galleryId"]);
	$smarty->assign_by_ref('gal_info', $gal_info);
	$podCastGallery = $filegallib->isPodCastGallery((int)$_REQUEST["galleryId"][0], $gal_info);
}
if (empty($_REQUEST['returnUrl'])) {
	include ('lib/filegals/max_upload_size.php');
}

// Process an upload here
if (isset($_REQUEST["upload"])) {
	check_ticket('upload-file');
	//print_progress('<script type="text/javascript" src="lib/tiki-js.js"></script>');
	$error_msg = '';
	$errors = array();
	$uploads = array();
	$batch_job = false;
	$didFileReplace = false;
	foreach($_FILES["userfile"]["error"] as $key => $error) {
		if (empty($_REQUEST['returnUrl'])) {
			print_progress('<?xml version="1.0" encoding="UTF-8"?>');
		}
		$formId = $_REQUEST['formId'];
		$smarty->assign("FormId", $_REQUEST['formId']);
		if (empty($_REQUEST['galleryId'][$key])) {
			continue;
		}
		if (!isset($_REQUEST['comment'][$key])) {
			$_REQUEST['comment'][$key] = '';
		}
		// We process here file uploads
		if (!empty($_FILES["userfile"]["name"][$key])) {
			// Were there any problems with the upload?  If so, report here.
			if (!is_uploaded_file($_FILES["userfile"]["tmp_name"][$key])) {
				$errors[] = $_FILES['userfile']['name'][$key] . ': ' . tra('Upload was not successful') . ': ' . $tikilib->uploaded_file_error($error);
				continue;
			}
			// Check the name
			if (!empty($prefs['fgal_match_regex'])) {
				if (!preg_match('/' . $prefs['fgal_match_regex'] . '/', $_FILES["userfile"]['name'][$key])) {
					$errors[] = tra('Invalid filename (using filters for filenames)') . ': ' . $_FILES["userfile"]["name"][$key];
					continue;
				}
			}
			if (!empty($prefs['fgal_nmatch_regex'])) {
				if (preg_match('/' . $prefs['fgal_nmatch_regex'] . '/', $_FILES["userfile"]["name"][$key])) {
					$errors[] = tra('Invalid filename (using filters for filenames)') . ': ' . $_FILES["userfile"]["name"][$key];
					continue;
				}
			}
			$name = $_FILES["userfile"]["name"][$key];
			if (isset($_REQUEST["isbatch"][$key]) && $_REQUEST["isbatch"][$key] == 'on' && strtolower(substr($name, strlen($name) - 3)) == 'zip') {
				if ($tiki_p_batch_upload_files == 'y') {
					if (!$filegallib->process_batch_file_upload($_REQUEST["galleryId"][$key], $_FILES["userfile"]['tmp_name'][$key], $user, isset($_REQUEST["description"][$key]) ? $_REQUEST["description"][$key] : '', $errors)) {
						continue;
					}
					$batch_job = true;
					$batch_job_galleryId = $_REQUEST["galleryId"][$key];
					print_msg(tra('Batch file processed') . " $name", $formId);
					continue;
				} else {
					$errors[] = tra('No permission to upload zipped file packages');
					continue;
				}
			}
			if (!$filegallib->checkQuota($_FILES['userfile']['size'][$key], $_REQUEST['galleryId'][$key], $error)) {
				$errors[] = $error;
				continue;
			}

			$size = $_FILES["userfile"]['size'][$key];
			$type = $_FILES["userfile"]['type'][$key];
			$name = stripslashes($_FILES["userfile"]['name'][$key]);

			$file_name = $_FILES["userfile"]["name"][$key];
			$file_tmp_name = $_FILES["userfile"]["tmp_name"][$key];
			$tmp_dest = $prefs['tmpDir'] . "/" . $file_name . ".tmp";

			// Handle per gallery image size limits
			$ratio = 0;
			list(,$subtype)=explode('/',strtolower($type));
			if ($subtype == "pjpeg")	// supress ie non-mime type (ie sends non standard mime-type for jpeg)
			{
				$subtype = "jpeg";
				$type = "image/jpeg";
			}
			// If it's an image format we can handle and gallery has limits on image sizes
			if ( (in_array($subtype, array("jpg","jpeg","gif","png","bmp","wbmp"))) 
				&& ( ($gal_info["image_max_size_x"]) || ($gal_info["image_max_size_y"]) && (!$podCastGallery) ) ) {
				$image_size_info = getimagesize($file_tmp_name);
				$image_x = $image_size_info[0];
				$image_y = $image_size_info[1];
				if($gal_info["image_max_size_x"]) {
					$rx=$image_x/$gal_info["image_max_size_x"];
				}else{
					$rx=0;
				}
				if($gal_info["image_max_size_y"]) {
					$ry=$image_y/$gal_info["image_max_size_y"];
				}else{
					$ry=0;
				}
				$ratio=max($rx,$ry);
				if($ratio>1) {	// Resizing will occur
					$image_new_x=$image_x/$ratio;
					$image_new_y=$image_y/$ratio;
					$resized_file = $tmp_dest;
					$image_resized_p = imagecreatetruecolor($image_new_x, $image_new_y);
					switch($subtype) {
						case "gif":
							$image_p = imagecreatefromgif($file_tmp_name);
							break;
						case "png":
							$image_p = imagecreatefrompng($file_tmp_name);
							break;
						case "bmp":
						case "wbmp":
							$image_p = imagecreatefromwbmp($file_tmp_name);
							break;
						case "jpg":
						case "jpeg":
							$image_p = imagecreatefromjpeg($file_tmp_name);
							break;
					}
					if(!imagecopyresampled($image_resized_p, $image_p, 0, 0, 0, 0, $image_new_x, $image_new_y, $image_x, $image_y)) {
						$errors[] = tra('Cannot resize the file:') . ' ' . $resized_file;
					}
					switch($subtype) {
						case "gif":
							if (!imagegif($image_resized_p, $resized_file)){
								$errors[] = tra('Cannot write the file:') . ' ' . $resized_file;
							}
							break;
						case "png":
							if (!imagepng($image_resized_p, $resized_file)){
								$errors[] = tra('Cannot write the file:') . ' ' . $resized_file;
							}
							break;
						case "bmp":
						case "wbmp":
							if (!image2wbmp($image_resized_p, $resized_file)){
								$errors[] = tra('Cannot write the file:') . ' ' . $resized_file;
							}
							break;
						case "jpg":
						case "jpeg":
							if (!imagejpeg($image_resized_p, $resized_file)){
								$errors[] = tra('Cannot write the file:') . ' ' . $resized_file;
							}
							break;
					}
					unlink($image_p);
					$feedback_message = sprintf(tra('Image was reduced: %s x %s -> %s x %s'),$image_x, $image_y, (int)$image_new_x, (int)$image_new_y);
					$size = filesize($resized_file);
				}
			}

			if ($ratio <=1) {
				// No resizing
				if (!move_uploaded_file($file_tmp_name, $tmp_dest)) {
					if ($tiki_p_admin == 'y') {
						$errors[] = tra('Errors detected').'. '.tra('Check that these paths exist and are writable by the web server').': '.$file_tmp_name.' '.$tmp_dest;
						continue;
					}	else	{
						$errors[] = tra('Errors detected');
						continue;
					}
				}
			}

			$fp = fopen($tmp_dest, "rb");
			if (!$fp) {
				$errors[] = tra('Cannot read the file:') . ' ' . $tmp_dest;
			}
			$data = '';
			$fhash = '';
			$extension = '';
			if (($prefs['fgal_use_db'] == 'n') || ($podCastGallery)) {
				$fhash = md5($name = $_FILES["userfile"]['name'][$key]);
				$extension = '';
				// for podcast galleries add the extension so the
				// file can be called directly if name is known,
				if ($podCastGallery) {
					$path_parts = pathinfo($_FILES["userfile"]['name'][$key]);
					if (in_array(strtolower($path_parts['extension']), array('m4a', 'mp3', 'mov', 'mp4', 'm4v', 'pdf', 'flv', 'swf'))) {
						$extension = '.' . strtolower($path_parts['extension']);
					} else {
						$errors[] = tra('Incorrect file extension:').$path_parts['extension'];
					}
					$savedir = $prefs['fgal_podcast_dir'];
				} else {
					$savedir = $prefs['fgal_use_dir'];
				}
				do {
					$fhash = md5(uniqid($fhash));
				}
				while (file_exists($savedir . $fhash . $extension));
				@$fw = fopen($savedir . $fhash . $extension, "wb");
				if (!$fw) {
					$errors[] = tra('Cannot write to this file:') . $savedir . $fhash;
				}
			}
			while (!feof($fp)) {
				if (($prefs['fgal_use_db'] == 'y') && (!$podCastGallery)) {
					$data.= fread($fp, 8192 * 16);
				} else {
					if (($data = fread($fp, 8192 * 16)) === false) {
						$errors[] = tra('Cannot read the file:') . ' ' . $tmp_dest;
					}
					if (fwrite($fw, $data) === false) {
						$errors[] = tra('Cannot write to this file:') . $savedir . $fhash;
					}
				}
			}
			fclose($fp);
			// remove file after copying it to the right location or database
			@unlink($tmp_dest);
			if (($prefs['fgal_use_db'] == 'n') || ($podCastGallery)) {
				fclose($fw);
				$data = '';
			}

			if (preg_match('/.flv$/', $name)) {
				$type = "video/x-flv";
			}
			if (count($errors)) {
				continue;
			}
			if (!$size) {
				$errors[] = tra('Warning: Empty file:') . '  ' . $name . '. ' . tra('Please re-upload your file');
			}
			if (($prefs['fgal_use_db'] == 'y') && (!$podCastGallery)) {
				if (!isset($data) || strlen($data) < 1) {
					$errors[] = tra('Warning: Empty file:') . ' ' . $name . '. ' . tra('Please re-upload your file');
				}
			}

			if (empty($_REQUEST['name'][$key])) $_REQUEST['name'][$key] = $name;
			if (empty($_REQUEST['user'][$key])) $_REQUEST['user'][$key] = $user;
			if (!isset($_REQUEST['description'][$key])) $_REQUEST['description'][$key] = '';
			if (empty($_REQUEST['author'][$key])) $_REQUEST['author'][$key] = $user;
			if (empty($_REQUEST['deleteAfter'][$key]) || empty($_REQUEST['deleteAfter_unit'][$key])) {
				$deleteAfter = null;
			} else {
				$deleteAfter = $_REQUEST['deleteAfter'][$key]*$_REQUEST['deleteAfter_unit'][$key];
			}

			$fileInfo['filename'] = $file_name;
			if (isset($data)) {
				if ($editFile) {
					$didFileReplace = true;
					$fileId = $filegallib->replace_file($editFileId, $_REQUEST["name"][$key], $_REQUEST["description"][$key], $name, $data, $size, $type, $_REQUEST['user'][$key], $fhash . $extension, $_REQUEST['comment'][$key], $gal_info, $didFileReplace, $_REQUEST['author'][$key], $fileInfo['lastModif'], $fileInfo['lockedby'], $deleteAfter);
					if ($prefs['fgal_limit_hits_per_file'] == 'y') {
						$filegallib->set_download_limit($editFileId, $_REQUEST['hit_limit'][$key]);
					}
				} else {
					$fileId = $filegallib->insert_file($_REQUEST["galleryId"][$key], $_REQUEST["name"][$key], $_REQUEST["description"][$key], $name, $data, $size, $type, $_REQUEST['user'][$key], $fhash . $extension, '', $_REQUEST['author'][$key], '', '', $deleteAfter);
				}
				if (!$fileId) {
					$errors[] = tra('Upload was not successful. Duplicate file content') . ': ' . $name;
					if (($prefs['fgal_use_db'] == 'n') || ($podCastGallery)) {
						@unlink($savedir . $fhash);
					}
				}
				if ($prefs['fgal_limit_hits_per_file'] == 'y') {
					$filegallib->set_download_limit($fileId, $_REQUEST['hit_limit'][$key]);
				}
				if (count($errors) == 0) {
					$aux['name'] = $name;
					$aux['size'] = $size;
					$aux['fileId'] = $fileId;
					if ($podCastGallery) {
						$aux['dllink'] = $podcast_url . $fhash . $extension . '&amp;thumbnail=y';
					} else {
						$aux['dllink'] = $url_browse . "?fileId=" . $fileId;
					}
					$uploads[] = $aux;
					$cat_type = 'file';
					$cat_objid = $fileId;
					$cat_desc = substr($_REQUEST["description"][$key], 0, 200);
					$cat_name = empty($_REQUEST['name'][$key]) ? $name : $_REQUEST['name'][$key];
					$cat_href = $aux['dllink'];
					$cat_object_exists = (bool) $fileId;
					if ($prefs['feature_groupalert'] == 'y' && isset($_REQUEST['listtoalert'])) {
						$groupalertlib->Notify($_REQUEST['listtoalert'], "tiki-download_file.php?fileId=" . $fileId);
					}
					include_once ('categorize.php');
					// Print progress
					if (empty($_REQUEST['returnUrl']) && $prefs['javascript_enabled'] == 'y') {
						if (!empty($_REQUEST['filegals_manager'])) {
							$smarty->assign('filegals_manager', $_REQUEST['filegals_manager']);
						}
						$smarty->assign("name", $aux['name']);
						$smarty->assign("size", $aux['size']);
						$smarty->assign("fileId", $aux['fileId']);
						$smarty->assign("dllink", $aux['dllink']);
						$smarty->assign("nextFormId", $_REQUEST['formId'] + 1);
						$smarty->assign("feedback_message",$feedback_message);
						$syntax = $filegallib->getWikiSyntax($_REQUEST["galleryId"][$key]);
						$syntax = $tikilib->process_fgal_syntax($syntax, $aux);
						$smarty->assign('syntax', $syntax);
						print_progress($smarty->fetch("tiki-upload_file_progress.tpl"));
					}
				}
			}
		}
	}
	if (empty($_REQUEST['returnUrl']) && count($errors)) {
		foreach($errors as $error) {
			print_msg($error, $formId);
		}
	}
	if ($editFile && !$didFileReplace) {
		if (empty($_REQUEST['deleteAfter']) || empty($_REQUEST['deleteAfter_unit'])) {
			$deleteAfter = null;
		} else {
			$deleteAfter = $_REQUEST['deleteAfter']*$_REQUEST['deleteAfter_unit'];
		}
		$filegallib->replace_file($editFileId, $_REQUEST['name'][0], $_REQUEST['description'][0], $fileInfo['filename'], $fileInfo['data'], $fileInfo['filesize'], $fileInfo['filetype'], $fileInfo['user'], $fileInfo['path'], $_REQUEST['comment'][0], $gal_info, $didFileReplace, $_REQUEST['author'][0], $fileInfo['lastModif'], $fileInfo['lockedby'], $deleteAfter);
		$fileChangedMessage = tra('File update was successful') . ': ' . $_REQUEST['name'];
		$smarty->assign('fileChangedMessage', $fileChangedMessage);
		$cat_type = 'file';
		$cat_objid = $editFileId;
		$cat_desc = substr($_REQUEST["description"][0], 0, 200);
		$cat_name = empty($fileInfo['name']) ? $fileInfo['filename'] : $fileInfo['name'];
		$cat_href = $podCastGallery ? $podcast_url . $fhash : "$url_browse?fileId=" . $editFileId;
		$cat_object_exists = (bool) $cat_objid;
		if ($prefs['fgal_limit_hits_per_file'] == 'y') {
			$filegallib->set_download_limit($editFileId, $_REQUEST['hit_limit'][0]);
		}
		include_once ('categorize.php');
	}
	$smarty->assign('errors', $errors);
	$smarty->assign('uploads', $uploads);
	if ($batch_job and count($errors) == 0) {
		header("location: tiki-list_file_gallery.php?galleryId=" . $batch_job_galleryId);
		die;
	}
	if (!empty($editFileId) and count($errors) == 0) {
		header("location: tiki-list_file_gallery.php?galleryId=" . $_REQUEST["galleryId"][0]);
		die;
	}
	if (!empty($_REQUEST['returnUrl'])) {
		if (!empty($errors)) {
			$smarty->assign('msg', implode($errors, '<br />'));
			$smarty->display('error.tpl');
			die;
		}
		header('location: '.$_REQUEST['returnUrl']);
		die;
	}
} else {
	$smarty->assign('errors', array());
	$smarty->assign('uploads', array());
}
// Get the list of galleries to display the select box in the template
if (isset($_REQUEST['galleryId']) && is_numeric($_REQUEST['galleryId'])) {
	$smarty->assign('galleryId', $_REQUEST["galleryId"]);
} elseif (isset($_REQUEST["galleryId"][0])) {
	$smarty->assign('galleryId', $_REQUEST["galleryId"][0]);
} else {
	$smarty->assign('galleryId', '');
}
if (empty($_REQUEST['fileId'])) {
	global $cachelib;
	include_once ('lib/cache/cachelib.php');
	$cacheName = $filegallib->get_all_galleries_cache_name($user);
	$cacheType = $filegallib->get_all_galleries_cache_type();
	if (!$galleries = $cachelib->getSerialized($cacheName, $cacheType)) {
		$galleries = $filegallib->list_file_galleries(0, -1, 'name_asc', $user, '', $prefs['fgal_root_id'], false, true, false, false, false, true, false);
		$cachelib->cacheItem($cacheName, serialize($galleries), $cacheType);
	}
	$galleries['data'] = Perms::filter(array('type' => 'file gallery'), 'object', $galleries['data'], array('object'=>'id'), 'upload_files');
	$smarty->assign_by_ref('galleries', $galleries["data"]);
}
if ($prefs['fgal_limit_hits_per_file'] == 'y') {
	$smarty->assign('hit_limit', $filegallib->get_download_limit($_REQUEST['fileId']));
}
$cat_type = 'file';
$cat_objid = empty($_REQUEST['fileId']) ? 0 : $_REQUEST['fileId'];
$cat_object_exists = (bool) $cat_objid;
include_once ('categorize_list.php');
include_once ('tiki-section_options.php');
ask_ticket('upload-file');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template
if ($prefs['javascript_enabled'] != 'y' or !isset($_REQUEST["upload"])) {
	$smarty->assign('mid', 'tiki-upload_file.tpl');
	if (!empty($_REQUEST['filegals_manager'])) {
		$smarty->assign('filegals_manager', $_REQUEST['filegals_manager']);
		$smarty->display("tiki_full.tpl");
	} else {
		$smarty->display("tiki.tpl");
	}
}
