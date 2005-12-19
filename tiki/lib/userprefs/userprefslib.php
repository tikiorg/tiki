<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class UserPrefsLib extends TikiLib {
	function UserPrefsLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to UsersPrefsLib constructor");
		}
		$this->db = $db;
	}

	function set_user_avatar($user, $type, $avatarLibName, $avatarName, $avatarSize, $avatarType, $avatarData) {
		$query = "update `users_users` set `avatarType` = ?, `avatarLibName` = ?, `avatarName` = ?, `avatarSize` = ?, `avatarFileType` = ?, `avatarData` = ?  where `login`=?";
		$result = $this->query($query,array($type,$avatarLibName,$avatarName,($avatarSize?$avatarSize:NULL),$avatarType,$avatarData,$user));
	}

	function get_user_avatar_img($user) {
		$query = "select * from `users_users` where `login`=?";
		$result = $this->query($query,array($user));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
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
		$user1=$this->get_userprefs($usersrc);
		$user2=$this->get_userprefs($userdst);

		for ($i=0;$i<count($user1);$i++) {
			if ($user1[$i]["prefName"]=="lat") $u1lat=$user1[$i]["value"];
			if ($user1[$i]["prefName"]=="lon") $u1lon=$user1[$i]["value"];
		}
		for ($i=0;$i<count($user2);$i++) {
			if ($user2[$i]["prefName"]=="lat") $u2lat=$user2[$i]["value"];
			if ($user2[$i]["prefName"]=="lon") $u2lon=$user2[$i]["value"];
		}
		if (isset($u1lat) && isset($u1lon) &&isset($u2lat) && isset($u2lon) ) {
			$distance=$this->distance($u1lat,$u1lon,$u2lat,$u2lon);
		  return (round($distance,3));
		} else {
			return(NULL);
		}
	}
}
global $dbTiki;
$userprefslib = new UserPrefsLib($dbTiki);

?>
