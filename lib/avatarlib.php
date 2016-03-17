<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: socialnetworkslib.php 56812 2015-11-28 07:57:47Z rjsmelo $

// this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}


/**
 * This class bundles several avatar-related functions
 *
 * todo: It would be good to get existing avatar functionality and bring it into this lib.
 *
 * @author patrick-proulx
 * @since 15.0
 */
class AvatarLib extends TikiLib
{

	/**
	 * sets the avatar from a given image file's URL
	 *
	 * @return string	URL for the current page
	 */
	function set_avatar_from_url($url, $userwatch="", $name="")
	{
		global $user, $prefs;

		$access = TikiLib::lib('access');
		$access->check_feature('feature_userPreferences');
		$access->check_user($user);

		$userprefslib = TikiLib::lib('userprefs');
		$imagegallib = TikiLib::lib('imagegal');

		if (empty($userwatch)) {
			$userwatch = $user;
		}

		$data = file_get_contents($url);
		list($iwidth, $iheight, $itype, $iattr) = getimagesize($url);
		$itype = image_type_to_mime_type($itype);

		// Get proper file size of image
		$imgdata = get_headers($url, true);
		if (isset($imgdata['Content-Length'])) {
			# Return file size
			$size = (int) $imgdata['Content-Length'];
		}

		// Store full-size file gallery image if that is required
		if ($prefs["user_store_file_gallery_picture"] == 'y') {
			$fgImageId = $userprefslib->set_file_gallery_image($userwatch, $name, $size, $itype, $data);
		}

		// Store small avatar
		if ($prefs['user_small_avatar_size']) {
			$avsize = $prefs['user_small_avatar_size'];
		} else {
			$avsize = "45"; //default
		}

		if (($iwidth == $avsize and $iheight <= $avsize) || ($iwidth <= $avsize and $iheight == $avsize)) {
			$userprefslib->set_user_avatar($userwatch, 'u', '', $name, $size, $itype, $data);
		} else {
			if (function_exists("ImageCreateFromString") && (!strstr($type, "gif"))) {
				$img = imagecreatefromstring($data);
				$size_x = imagesx($img);
				$size_y = imagesy($img);
				/* if the square crop is set, crop the image before resizing */
				if ($prefs['user_small_avatar_square_crop']){
					$crop_size = min ($size_x, $size_y);
					$offset_x = ($size_x - $crop_size)/2;
					$offset_y = ($size_y - $crop_size)/2;
					$crop_array = array('x' =>$offset_x , 'y' => $offset_y, 'width' => $crop_size, 'height'=> $crop_size);
					$img = imagecrop($img,$crop_array);
					$size_x = $size_y = $crop_size;
				}
				if ($size_x > $size_y) $tscale = ((int)$size_x / $avsize);
				else $tscale = ((int)$size_y / $avsize);
				$tw = ((int)($size_x / $tscale));
				$ty = ((int)($size_y / $tscale));
				if ($tw > $size_x) $tw = $size_x;
				if ($ty > $size_y) $ty = $size_y;
				if (chkgd2()) {
					$t = imagecreatetruecolor($tw, $ty);
					imagecopyresampled($t, $img, 0, 0, 0, 0, $tw, $ty, $size_x, $size_y);
				} else {
					$t = imagecreate($tw, $ty);
					$imagegallib->ImageCopyResampleBicubic($t, $img, 0, 0, 0, 0, $tw, $ty, $size_x, $size_y);
				}
				// CHECK IF THIS TEMP IS WRITEABLE OR CHANGE THE PATH TO A WRITEABLE DIRECTORY
				$tmpfname = tempnam($prefs['tmpDir'], "TMPIMG");
				imagejpeg($t, $tmpfname);
				// Now read the information
				$fp = fopen($tmpfname, "rb");
				$t_data = fread($fp, filesize($tmpfname));
				fclose($fp);
				unlink($tmpfname);
				$t_type = 'image/jpeg';
				$userprefslib->set_user_avatar($userwatch, 'u', '', $name, $size, $t_type, $t_data);
			} else {
				$userprefslib->set_user_avatar($userwatch, 'u', '', $name, $size, $type, $data);
			}
		}
		TikiLib::events()->trigger('tiki.user.avatar',
			array(
				'type' => 'user',
				'object' => $userwatch,
				'user' => $userwatch,
			)
		);
	}
}