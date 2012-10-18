<?php

// group/owner of file
function get_ownership_groupname($filename) {
	if (file_exists($filename)) {
		$group=posix_getgrgid(filegroup($filename));
		$groupname=$group["name"];
	} else {
		$groupname="no group";
	}
	return $groupname;
}

// user/owner of file
function get_ownership_username($filename) {
	if (file_exists($filename)) {
		$user=posix_getpwuid(fileowner($filename));
		$username=$user["name"];
	} else {
		$username="no user";
	}
	return $username;
}

// all permission data by reference
function get_perm_data($filename,&$username,&$groupname,&$perms_asc,&$perms_oct) {
	$username=get_ownership_username($filename);
	$groupname=get_ownership_groupname($filename);
	$perms_asc=get_perms_ascii($filename);
	$perms_oct=get_perms_octal($filename);
}

// permissions of file
function get_perms_ascii($filename) {
	if (file_exists($filename)) {
		$perms = fileperms($filename);
		if (($perms & 0xC000) == 0xC000) {
			// Socket
			$perm_string = 's';
		} elseif (($perms & 0xA000) == 0xA000) {
			// Symbolic Link
			$perm_string = 'l';
		} elseif (($perms & 0x8000) == 0x8000) {
			// Regular
			$perm_string = '-';
		} elseif (($perms & 0x6000) == 0x6000) {
			// Block special
			$perm_string = 'b';
		} elseif (($perms & 0x4000) == 0x4000) {
			// Directory
			$perm_string = 'd';
		} elseif (($perms & 0x2000) == 0x2000) {
			// Character special
			$perm_string = 'c';
		} elseif (($perms & 0x1000) == 0x1000) {
			// FIFO pipe
			$perm_string = 'p';
		} else {
			// Unknown
			$perm_string = 'u';
		}
		// Owner
		$perm_string .= (($perms & 0x0100) ? 'r' : '-');
		$perm_string .= (($perms & 0x0080) ? 'w' : '-');
		$perm_string .= (($perms & 0x0040) ?
	            (($perms & 0x0800) ? 's' : 'x' ) :
        	    (($perms & 0x0800) ? 'S' : '-'));
		// Group
		$perm_string .= (($perms & 0x0020) ? 'r' : '-');
		$perm_string .= (($perms & 0x0010) ? 'w' : '-');
		$perm_string .= (($perms & 0x0008) ?
	            (($perms & 0x0400) ? 's' : 'x' ) :
        	    (($perms & 0x0400) ? 'S' : '-'));
		// World
		$perm_string .= (($perms & 0x0004) ? 'r' : '-');
		$perm_string .= (($perms & 0x0002) ? 'w' : '-');
		$perm_string .= (($perms & 0x0001) ?
        	    (($perms & 0x0200) ? 't' : 'x' ) :
        	    (($perms & 0x0200) ? 'T' : '-'));
	} else {
		$perm_string="no access";
	}
return $perm_string;
}

function get_perms_octal($filename) {
	if (file_exists($filename)) {
		$perms_oct=substr(sprintf('%o', fileperms($filename)), -3);
	} else {
		$perms_oct="999";
	}
	return $perms_oct;
}
?>
