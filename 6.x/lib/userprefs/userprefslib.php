<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class UserPrefsLib extends TikiLib
{

	function set_user_avatar($user, $type, $avatarLibName, $avatarName, $avatarSize, $avatarType, $avatarData) {
		global $prefs, $userlib, $tikidomainslash;
		$query = "update `users_users` set `avatarType` = ?, `avatarLibName` = ?, `avatarName` = ?, `avatarSize` = ?, `avatarFileType` = ?, `avatarData` = ?  where `login`=?";
		$result = $this->query($query,array($type,$avatarLibName,$avatarName,($avatarSize?$avatarSize:NULL),$avatarType,$avatarData,$user));
		if ($prefs['feature_intertiki'] == 'y' && !empty($prefs['feature_intertiki_mymaster']) && $prefs['feature_intertiki_import_preferences'] == 'y') { //send to the master
			$userlib->interSendUserInfo($prefs['interlist'][$prefs['feature_intertiki_mymaster']], $user);
		}

		$image = 'temp/public/'.$tikidomainslash.'avatar_' . $user . '.*';
		foreach( glob( $image ) as $file ) {
			unlink($file);
		}
	}

	function get_user_avatar_img($user) {
		$query = "select * from `users_users` where `login`=?";
		$result = $this->query($query,array($user));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

	function set_file_gallery_image($u, $filename, $size, $type, $data) {
		global $prefs, $tikilib;
		global $filegallib;
		if (!is_object($filegallib)) {
				require_once( 'lib/filegals/filegallib.php' );
		}
		if (!$prefs["user_picture_gallery_id"]) {
			return false;
		}
		if ($user_image_id = $tikilib->get_user_preference($u, 'user_fg_image_id')) {
			$didFileReplace = false;
			$gal_info = $tikilib->get_file_gallery($prefs["user_picture_gallery_id"]);
			$filegallib->replace_file($user_image_id, $u, $u, $filename, $data, $size, $type, $u, '', '', $gal_info, $didFileReplace);	
		} else {
			$user_image_id = $filegallib->insert_file($prefs["user_picture_gallery_id"], $u, $u, $filename, $data, $size, $type, $u, '', '', '');
			$tikilib->set_user_preference($u, 'user_fg_image_id', $user_image_id);
		}
		return $user_image_id;
	}
	
	function remove_file_gallery_image($u) {
		global $prefs, $tikilib;
		global $filegallib;
		if (!is_object($filegallib)) {
				require_once( 'lib/filegals/filegallib.php' );
		}
		if ($user_image_id = $tikilib->get_user_preference($u, 'user_fg_image_id')) {
			$file_info = $filegallib->get_file_info($user_image_id, false, false);
			$filegallib->remove_file($file_info, '', true); 
			$tikilib->set_user_preference($u, 'user_fg_image_id', '');
			return true;
		} else {
			return false;
		}
	}
	
	function get_user_picture_id($u) {
		global $tikilib;
		return $tikilib->get_user_preference($u, 'user_fg_image_id');		
	}
	
	function get_userprefs($user) {
		$query = "select * from `tiki_user_preferences` where `user`=?";
		$result = $this->query($query,array($user));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		return $ret;
	}
	
	function get_userdistance($usersrc,$userdst) {
		if ($usersrc == $userdst)
			return null;
		$user1=$this->get_userprefs($usersrc);
		$user2=$this->get_userprefs($userdst);

		for ($i=0, $icount_user1 = count($user1); $i < $icount_user1; $i++) {
			if ($user1[$i]["prefName"] == "lat") $u1lat = $user1[$i]["value"];
			if ($user1[$i]["prefName"] == "lon") $u1lon = $user1[$i]["value"];
		}
		for ($i=0, $icount_user2 = count($user2); $i < $icount_user2; $i++) {
			if ($user2[$i]["prefName"] == "lat") $u2lat = $user2[$i]["value"];
			if ($user2[$i]["prefName"] == "lon") $u2lon = $user2[$i]["value"];
		}
		if (isset($u1lat) && isset($u1lon) &&isset($u2lat) && isset($u2lon) ) {
			$distance=$this->distance($u1lat, $u1lon, $u2lat, $u2lon);
		  return (round($distance, 3));
		} else {
			return(NULL);
		}
	}
}
$userprefslib = new UserPrefsLib;
