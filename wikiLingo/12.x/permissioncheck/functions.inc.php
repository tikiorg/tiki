<?php

function check_file($filename,$filecontent)
{
	$dummy = 'foo';
}

function check_file_delete($filename)
{
	$delete_permission = unlink($filename);
	return $delete_permission;
}

function check_file_exists($filename)
{
	$exists_permission = file_exists($filename);
	return $exists_permission;
}

function check_file_read($filename)
{
	$testname = $filename;
	$read_permission = true;
	$fileout = fopen($testname, 'r') or $read_permission = false;
	if ( $read_permission ) {
		$dummy = 'foo';
		//$dummy = fgets($fileout);
		fclose($fileout);
	} else {
		$dummy = 'bar';
	}
	return $read_permission;
}

function check_file_rename($oldfilename,$newfilename)
{
	$rename_permission = rename($oldfilename, $newfilename);
	return $rename_permission;
}

function check_file_write($filename,$filecontent)
{
	$testname = $filename;
	$testcontent = $filecontent;
	$write_permission = true;
	$fileout = fopen($testname, 'w') or $write_permission = false;
	if ( $write_permission ) {
		fwrite($fileout, $testcontent);
		fclose($fileout);
	} else {
		$dummy = 'foobar';
	}
	return $write_permission;
}

// replace template names with CSS class names
function color_classes_perm_asc($filename,&$perms_asc,&$css_class_writable)
{
	if ( is_writable($filename) ) {
		$perms_asc = str_replace('WPERM', 'writeyes', $perms_asc);
		$css_class_writable = 'writeyes';
	} else {
		$perms_asc = str_replace('WPERM', 'writeno', $perms_asc);
		$css_class_writable = 'writeno';
	}
	$css_class_writable = 'noclass';
	if ( is_readable($filename) ) {
		$perms_asc = str_replace('RPERM', 'readyes', $perms_asc);
	} else {
		$perms_asc = str_replace('RPERM', 'readno', $perms_asc);
	}
}

// group/owner of file
function get_ownership_groupname($filename)
{
	if (file_exists($filename)) {
		$group = posix_getgrgid(filegroup($filename));
		$groupname = $group['name'];
	} else {
		$groupname = 'no group';
	}
	return $groupname;
}

// user/owner of file
function get_ownership_username($filename)
{
    if (function_exists('posix_getpwuid')) {
        if (file_exists($filename)) {
            $user = posix_getpwuid(fileowner($filename));
            $username = $user['name'];
        } else {
            $username = 'no user';
        }
    } else {
        die('no posix extension');	// TODO (better)
    }
	return $username;
}

// page url
function get_page_url($filename)
{
	$page_basename = 'http';
	if ( isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on" ) {
		$page_basename .= 's';
	}
	$page_basename .= '://';
	$page_basename .= $_SERVER["SERVER_NAME"];
	$page_basename .= dirname($_SERVER['PHP_SELF']);
	$page_basename .= '/' . $filename;

	return $page_basename;
}

// file or path url without Tiki root path equal to document root necessarily
// subdir 'permissioncheck' or $perm_check_subdir must be direct child of Tiki root
function get_page_url_clean($filename)
{
	$page_basename = 'http';
	if ( $_SERVER["HTTPS"] == "on" ) {
		$page_basename .= 's';
	}
	$page_basename .= '://';
	$page_basename .= $_SERVER["SERVER_NAME"];
	$tmp_path = dirname($_SERVER['PHP_SELF']);
	$perm_check_subdir = 'permissioncheck';
//	$tiki_path = str_replace("/$perm_check_subdir",'/',$tmp_path);
	// previous one does not work in cases where 'permissioncheck' is already
	// subdir in path to Tiki, e.g. /foo/permissioncheck/tiki/
	//
	$tiki_path = preg_replace("/\/$perm_check_subdir$/", '/', $tmp_path);
	// quick 'n dirty, does not work if Tiki path != document root
	//$tiki_path = '/'
	$page_basename .= $tiki_path . $filename;

	return $page_basename;
}

// all permission data by reference
function get_perm_data($filename, &$username, &$groupname, &$perms_asc, &$perms_oct)
{
	$username = get_ownership_username($filename);
	$groupname = get_ownership_groupname($filename);
	$perms_asc = get_perms_ascii($filename);
	$perms_oct = get_perms_octal($filename);
}

// permissions of file
function get_perms_ascii($filename)
{
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
		$perm_string .= '<span class="RPERM">' . (($perms & 0x0100) ? 'r' : '-') . '</span>';
		$perm_string .= '<span class="WPERM">' . (($perms & 0x0080) ? 'w' : '-') . '</span>';
		$perm_string .= '<span class="XPERM">' . (($perms & 0x0040) ?
	            (($perms & 0x0800) ? 's' : 'x' ) :
        	    (($perms & 0x0800) ? 'S' : '-')) . '</span>';
		// Group
		$perm_string .= '<span class="RPERM">' . (($perms & 0x0020) ? 'r' : '-') . '</span>';
		$perm_string .= '<span class="WPERM">' . (($perms & 0x0010) ? 'w' : '-') . '</span>';
		$perm_string .= '<span class="XPERM">' . (($perms & 0x0008) ?
	            (($perms & 0x0400) ? 's' : 'x' ) :
        	    (($perms & 0x0400) ? 'S' : '-')) . '</span>';
		// World
		$perm_string .= '<span class="RPERM">' . (($perms & 0x0004) ? 'r' : '-') . '</span>';
		$perm_string .= '<span class="WPERM">' . (($perms & 0x0002) ? 'w' : '-') . '</span>';
		$perm_string .= '<span class="XPERM">' . (($perms & 0x0001) ?
        	    (($perms & 0x0200) ? 't' : 'x' ) :
        	    (($perms & 0x0200) ? 'T' : '-')) . '</span>';
	} else {
		$perm_string="no access";
	}
	return $perm_string;
}

function get_perms_octal($filename)
{
	if (file_exists($filename)) {
		$perms_oct=substr(sprintf('%o', fileperms($filename)), -3);
	} else {
		$perms_oct = '999';
	}
	return $perms_oct;
}

function prepare_htaccess_password_protection($filename)
{
	$new_htaccess = $filename;
	$new_htaccess = 'new_htaccess';
//	if (file_exists($new_htaccess)) {
		//$template_htaccess = '_htaccess';
		$my_htpasswd = '.htpasswd';
		$fileout = fopen($new_htaccess, 'w') or exit('Unable to open file ' . $new_htaccess . '!');
		$my_document_root_path = $_SERVER['DOCUMENT_ROOT'];
		$my_html_path = dirname($_SERVER['PHP_SELF']);
		fwrite($fileout, 'AuthUserFile ');
		fwrite($fileout, $my_document_root_path);
		fwrite($fileout, $my_html_path);
		fwrite($fileout, '/' . $my_htpasswd . "\n");
	// early version - hardcoded output - intended to be read from template
		fwrite($fileout, 'AuthName "permissioncheck password protection"' . "\n");
		fwrite($fileout, 'AuthType Basic' . "\n");
		fwrite($fileout, '<Limit GET POST PUT>' . "\n");
		fwrite($fileout, 'require valid-user' . "\n");
		fwrite($fileout, '</Limit>' . "\n");
		//fwrite($fileout, '' . "\n");
		fwrite($fileout, '<FilesMatch "\.(bak|inc|inc\.php|lib|sh|sql|tpl)$">' . "\n");
		fwrite($fileout, 'order deny,allow' . "\n");
		fwrite($fileout, 'deny from all' . "\n");
		fwrite($fileout, '</FilesMatch>' . "\n");
		fclose($fileout);
		$success = false;
//	} else {
		$success = false;
//	}
	return $success;
}
