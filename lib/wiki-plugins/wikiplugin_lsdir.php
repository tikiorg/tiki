<?php
/*
 * $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_lsdir.php,v 1.1 2004-07-02 18:39:53 teedog Exp $
 *
 * TikiWiki LSDIR plugin: lists files in a directory
 * 
 * Syntax:
 * 
 *  {LSDIR([dir=>/dirpath/],[urlprefix=>prefix],[sortby=>name|atime|ctime|mtime|size],[sortmode=>asc|desc],[filter=>search_text],[limit=>#])}
 *  {LSDIR}
 * 
 */

function wikiplugin_lsdir_help() {
	return tra("Lists files in a directory").":<br />~np~{LSDIR(dir=>/dirpath/,urlprefix=>http://localhost/,sort=>name,filter=>.ext,limit=>5)}{LSDIR}~/np~";
}

function wikiplugin_lsdir($data, $params) {
	global $tikilib;
	$dir = '.';
	$urlprefix = NULL;
	$sort = 'name';
	$sortmode = 'asc';
	$filter = NULL;
	$limit = 0;
	$tmp_array = array();
	$ret = '';

	extract ($params);
	
	if ($limit>0) {
		$count = 0;
	} else {
		$count = -1;
	}
	
	// fileatime, filectime, filemtime are PHP functions
	if ($sort == 'atime') {
		$getkey = 'fileatime';
	} elseif ($sort == 'ctime') {
		$getkey = 'filectime';
	} elseif ($sort == 'mtime') {
		$getkey = 'filemtime';
	} elseif ($sort == 'size') {
		$getkey = 'filesize';
	}

	$dh = opendir($dir);
	while ($file = readdir($dh)) {
		if (empty($filter) || strpos($file,$filter)) {
			//Don't list subdirectories
			if (!is_dir("$dir/$file")) {
				if ($sort == 'name') {
					$key = "$file";
				} else {
					$key = $getkey("$dir/$file");
				}
				$tmp_array["$key"] = "$file";
			}
		}
	}
	closedir($dh);
	
	if ($sortmode == 'asc') {
		ksort($tmp_array);
	} elseif ($sortmode == 'desc') {
		krsort($tmp_array);
	}
	
	foreach($tmp_array as $filename) {
		if ($count >= $limit) {
			break 1;
		}
		if (!empty($urlprefix)) {
			$ret .= "<a href='$urlprefix$filename' class='wiki'>$filename</a><br />";
		} else {
			$ret .= "$filename<br />";
		}
		if ($limit>0) {
			$count++;
		}		
	}
	
	return $ret;

}

?>