<?php

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
}

$userprefslib = new UserPrefsLib($dbTiki);

?>
