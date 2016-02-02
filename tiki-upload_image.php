<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'galleries';
require_once ('tiki-setup.php');
$categlib = TikiLib::lib('categ');
$imagegallib = TikiLib::lib('imagegal');

$access->check_feature('feature_galleries');

if ($tiki_p_upload_images != 'y' and !$tikilib->user_has_perm_on_object($user, $_REQUEST["galleryId"], "image gallery", "tiki_p_upload_images")) {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to upload images"));
	$smarty->display("error.tpl");
	die;
}

$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1 = str_replace("tiki-upload_image", "tiki-browse_image", $foo["path"]);
$foo2 = str_replace("tiki-upload_image", "show_image", $foo["path"]);
$smarty->assign('url_browse', $tikilib->httpPrefix() . $foo1);
$smarty->assign('url_show', $foo2);
$smarty->assign('show', 'n');
unset($data);
// Process an upload here
if (isset($_REQUEST["upload"])) {
	check_ticket('upload-image');
	// Check here if it is an upload or an URL
	$tikilib->get_perm_object($_REQUEST["galleryId"], 'image gallery');
	if ($tiki_p_admin_galleries == 'y') {
		$tiki_p_view_image_gallery = 'y';
		$tiki_p_upload_images = 'y';
		$tiki_p_create_galleries = 'y';
	}
	$access->check_permission('tiki_p_upload_images');

	$gal_info = $imagegallib->get_gallery($_REQUEST["galleryId"]);
	if ($gal_info["thumbSizeX"] == 0) $gal_info["thumbSizeX"] = 80;
	if ($gal_info["thumbSizeY"] == 0) $gal_info["thumbSizeY"] = 80;
	// Check the user to be admin or owner or the gallery is public
	if ($tiki_p_admin_galleries != 'y' && (!$user || $user != $gal_info["user"]) && $gal_info["public"] != 'y') {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("You have permission to upload images but not to this gallery"));
		$smarty->display("error.tpl");
		die;
	}
	$error_msg = '';
	if (empty($user) && $prefs['feature_antibot'] == 'y' && !$captchalib->validate()) {
		$error_msg = $captchalib->getErrors();
		$smarty->assign('errortype', 'no_redirect_login');
	}
	if (!empty($_REQUEST["url"])) {
		// check URL. avoid uploading local files!
		if (!preg_match('#http[s]?://#i', $_REQUEST["url"])) {
			$_REQUEST["url"] = 'http://' . $_REQUEST["url"];
		}
		$data = $tikilib->httprequest($_REQUEST["url"]);
		if ($data) {
			// Get the image from a URL
			if (@getimagesize($_REQUEST["url"])) { // that's not nice. reads the image twice.
				// I'll have to add some functionality in imagegalslib
				// remember me if i forget that. redflo
				$url_info = parse_url($_REQUEST["url"]);
				$pinfo = pathinfo($url_info["path"]);
				$type = "image/" . $pinfo["extension"];
				$filename = $pinfo["basename"];
				$size = strlen($data);
			} else {
				$error_msg = tra("Cannot get image from URL");
				$smarty->assign('errortype', 'no_redirect_login');
			}
		} else {
			$error_msg = tra("That is not an image (or you have php < 4.0.5)");
			$smarty->assign('errortype', 'no_redirect_login');
		}
	} else {
		// We process here file uploads
		if (isset($_FILES['userfile1']) && !empty($_FILES['userfile1']['name'])) {
			if (is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
				if (!empty($prefs['gal_match_regex'])) {
					if (!preg_match('/' . $prefs['gal_match_regex'] . '/', $_FILES['userfile1']['name'], $reqs)) {
						$smarty->assign('msg', tra('Invalid imagename (using filters for filenames)'));
						$smarty->assign('errortype', 'no_redirect_login');
						$smarty->display("error.tpl");
						die;
					}
				}
				if (!empty($prefs['gal_nmatch_regex'])) {
					if (preg_match('/' . $prefs['gal_nmatch_regex'] . '/', $_FILES['userfile1']['name'], $reqs)) {
						$smarty->assign('msg', tra('Invalid imagename (using filters for filenames)'));
						$smarty->assign('errortype', 'no_redirect_login');
						$smarty->display("error.tpl");
						die;
					}
				}
				$type = $_FILES['userfile1']['type'];
				$size = $_FILES['userfile1']['size'];
				$filename = $_FILES['userfile1']['name'];
				// Check for a zip file.....
				if (substr($filename, strlen($filename) - 3) == 'zip') {
					if ($tiki_p_batch_upload_images == 'y') {
						if ($imagegallib->process_batch_image_upload($_REQUEST["galleryId"], $_FILES['userfile1']['tmp_name'], $user) == 0) {
							$smarty->assign('msg', tra('Error processing zipped image package'));
							$smarty->assign('errortype', 'no_redirect_login');
							$smarty->display("error.tpl");
							die;
						}
						header("location: tiki-browse_gallery.php?galleryId=" . $_REQUEST["galleryId"]);
						die();
					} else {
						$smarty->assign('msg', tra('No permission to upload zipped image packages'));
						$smarty->display("error.tpl");
						die;
					}
				}
				$file_name = $_FILES['userfile1']['name'];
				$file_tmp_name = $_FILES['userfile1']['tmp_name'];
				$tmp_dest = $prefs['tmpDir'] . '/' . $file_name . '.tmp'; // add .tmp to not overwrite existing files (like index.php)
				if (!move_uploaded_file($file_tmp_name, $tmp_dest)) {
					if ($tiki_p_admin == 'y') {
						$smarty->assign('msg', tra('Errors detected').'. '.tra('Check that these paths exist and are writable by the web server').': '.$file_tmp_name.' '.$tmp_dest);
					} else {
						$smarty->assign('msg', tra('Errors detected'));
					}
					$smarty->assign('errortype', 'no_redirect_login');
					$smarty->display("error.tpl");
					die();
				}
				$fp = fopen($tmp_dest, "rb");
				$data = fread($fp, filesize($tmp_dest));
				fclose($fp);
				$imginfo = @getimagesize($tmp_dest);
				unlink($tmp_dest);
				if (!$data || !$imginfo) { // Not in Image format
					$error_msg = tra('The uploaded file is not recognized as a image');
					$smarty->assign('errortype', 'no_redirect_login');
				}
			} else {
				$error_msg = $tikilib->uploaded_file_error($_FILES['userfile1']['error']);
				if (!empty($error_msg)) {
					$smarty->assign('errortype', 'no_redirect_login');
				}
			}
		}
	}
	$up_thumb = 0;
	// If the thumbnail was uploaded
	if (isset($_FILES['userfile2']) && !empty($_FILES['userfile2']['name'])) {
		$thumb_data = $imagegallib->get_one_image_from_disk('userfile2');
		if (isset($thumb_data['msg'])) {
			$error_msg = $thumb_data['msg'];
			$smarty->assign('errortype', 'no_redirect_login');
		}
		$up_thumb = 1;
	}
	if ($error_msg) {
		$smarty->assign('msg', $error_msg);
		$smarty->display("error.tpl");
		die;
	}
	if (isset($_REQUEST["name"]) && !empty($_REQUEST["name"])) {
		$name = $_REQUEST["name"];
	} elseif (isset($filename)) {
		$name = $filename;
	} else {
		$name = "";
	}
	$lat = NULL;
	$lon = NULL;
	if (isset($data)) {
		if (!$up_thumb) {
			if (function_exists("ImageCreateFromString") && (!strstr($type, "gif"))) {
				if ($img = @imagecreatefromstring($data)) {
					$size_x = imagesx($img);
					$size_y = imagesy($img);
					if ($size_x > $size_y) $tscale = ((int)$size_x / $gal_info["thumbSizeX"]);
					else $tscale = ((int)$size_y / $gal_info["thumbSizeY"]);
					$tw = ((int)($size_x / $tscale));
					$ty = ((int)($size_y / $tscale));
					if (chkgd2()) {
						$t = imagecreatetruecolor($tw, $ty);
						imagecopyresampled($t, $img, 0, 0, 0, 0, $tw, $ty, $size_x, $size_y);
					} else {
						$t = imagecreate($tw, $ty);
						$imagegallib->ImageCopyResampleBicubic($t, $img, 0, 0, 0, 0, $tw, $ty, $size_x, $size_y);
					}
					// CHECK IF THIS TEMP IS WRITEABLE OR CHANGE THE PATH TO A WRITEABLE DIRECTORY
					//$tmpfname = 'temp.jpg';
					$tmpfname = tempnam($prefs['tmpDir'], "TMPIMG");
					imagejpeg($t, $tmpfname);
					// Now read the information
					$fp = fopen($tmpfname, "rb");
					$t_data = fread($fp, filesize($tmpfname));
					fclose($fp);
					unlink($tmpfname);
					//$t_pinfo = pathinfo($tmpfname);
					//$t_type = $t_pinfo["extension"];
					$t_type = 'image/jpg'; // . $t_type;
					$imageId = $imagegallib->insert_image($_REQUEST["galleryId"], $name, $_REQUEST["description"], $filename, $type, $data, $size, $size_x, $size_y, $user, $t_data, $t_type, $lat, $lon, $gal_info);
				} else { // Not in Image format
					$smarty->assign('msg', tra('The uploaded file is not recognized as a image'));
					$smarty->display('error.tpl');
					die;
				}
			} else {
				$tmpfname = '';
				$imageId = $imagegallib->insert_image($_REQUEST["galleryId"], $name, $_REQUEST["description"], $filename, $type, $data, $size, $imginfo[0], $imginfo[1], $user, '', '', $lat, $lon, $gal_info);
			}
		} else {
			if (function_exists("ImageCreateFromString") && (!strstr($type, "gif"))) {
				if ($img = @imagecreatefromstring($data)) {
					$size_x = imagesx($img);
					$size_y = imagesy($img);
				} else {
					// Not in Image format
					$smarty->assign('msg', tra('The uploaded file is not recognized as a image'));
					$smarty->display('error.tpl');
					die;
				}
			} else {
				$size_x = $imginfo[0];
				$size_y = $imginfo[1];
			}
			$imageId = $imagegallib->insert_image($_REQUEST["galleryId"], $name, $_REQUEST["description"], $filename, $type, $data, $size, $size_x, $size_y, $user, $thumb_data, $thumb_data['filetype'], $lat, $lon, $gal_info);
		}
		if (!$imageId) {
			$smarty->assign('msg', tra('Upload failed'));
			$smarty->display("error.tpl");
			die;
		}
		$smarty->assign_by_ref('imageId', $imageId);
		// Now that the image was inserted we can display the image here.
		$smarty->assign('show', 'y');
		$smarty->assign_by_ref('tmpfname', $tmpfname);
		$smarty->assign_by_ref('fname', $_REQUEST["url"]);
		// Finally categorise it
		$cat_type = 'image';
		$cat_objid = $imageId;
		$cat_desc = substr($_REQUEST["description"], 0, 200);
		$cat_name = $name;
		$cat_href = $foo1 . "?imageId=" . $cat_objid;
		include_once ("categorize.php");
	}
}
$batchRes = array();
for ($i = 3; $i <= 8; $i++) {
	if (isset($_FILES["userfile$i"]) && !empty($_FILES["userfile$i"]['name'])) {
		$batchRes[] = $imagegallib->get_one_image_from_disk("userfile$i", $_REQUEST['galleryId'], isset($_REQUEST['name']) ? $_REQUEST['name'] : '', $_REQUEST['description'], $gal_info);
	}
}
if (count($batchRes)) $smarty->assign_by_ref('batchRes', $batchRes);
// Get the list of galleries to display the select box in the template
if (isset($_REQUEST["galleryId"])) {
	$smarty->assign_by_ref('galleryId', $_REQUEST["galleryId"]);
} else {
	$smarty->assign('galleryId', '');
}
if ($tiki_p_admin_galleries != 'y') {
	$galleries = $imagegallib->list_visible_galleries(0, -1, 'lastModif_desc', $user, '');
} else {
	$galleries = $imagegallib->list_galleries(0, -1, 'lastModif_desc', $user, '');
}
$temp_max = count($galleries["data"]);
for ($i = 0; $i < $temp_max; $i++) {
	if ($userlib->object_has_one_permission($galleries["data"][$i]["galleryId"], 'image gallery')) {
		$galleries["data"][$i]["individual"] = 'y';
		if ($userlib->object_has_permission($user, $galleries["data"][$i]["galleryId"], 'image gallery', 'tiki_p_view_image_gallery')) {
			$galleries["data"][$i]["individual_tiki_p_view_image_gallery"] = 'y';
		} else {
			$galleries["data"][$i]["individual_tiki_p_view_image_gallery"] = 'n';
		}
		if ($userlib->object_has_permission($user, $galleries["data"][$i]["galleryId"], 'image gallery', 'tiki_p_upload_images')) {
			$galleries["data"][$i]["individual_tiki_p_upload_images"] = 'y';
		} else {
			$galleries["data"][$i]["individual_tiki_p_upload_images"] = 'n';
		}
		if ($userlib->object_has_permission($user, $galleries["data"][$i]["galleryId"], 'image gallery', 'tiki_p_create_galleries')) {
			$galleries["data"][$i]["individual_tiki_p_create_galleries"] = 'y';
		} else {
			$galleries["data"][$i]["individual_tiki_p_create_galleries"] = 'n';
		}
		if ($tiki_p_admin == 'y' || $userlib->object_has_permission($user, $galleries["data"][$i]["galleryId"], 'image gallery', 'tiki_p_admin_galleries')) {
			$galleries["data"][$i]["individual_tiki_p_create_galleries"] = 'y';
			$galleries["data"][$i]["individual_tiki_p_upload_images"] = 'y';
			$galleries["data"][$i]["individual_tiki_p_view_image_gallery"] = 'y';
		}
	} else {
		$galleries["data"][$i]["individual"] = 'n';
	}
}
$smarty->assign_by_ref('galleries', $galleries["data"]);
$cat_type = 'image';
$cat_objid = '0';
include_once ("categorize_list.php");
include ('lib/filegals/max_upload_size.php');
include_once ('tiki-section_options.php');
ask_ticket('upload-image');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('mid', 'tiki-upload_image.tpl');
$smarty->display("tiki.tpl");
