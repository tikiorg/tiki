<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
/*
 * Tikiwiki LSDIR plugin: lists files in a directory
 * 
 * Syntax:
 * 
 *  {LSDIR([dir=>/dirpath/],[urlprefix=>prefix],[sortby=>name|atime|ctime|mtime|size],[sortmode=>asc|desc],[filter=>search_text],[limit=>#])}
 *  {LSDIR}
 */

function wikiplugin_lsdir_help() {
	return tra("Lists files in a directory").":<br />~np~{LSDIR(dir=>/dirpath/,urlprefix=>http://localhost/,sort=>name,filter=>.ext,limit=>5)}{LSDIR}~/np~";
}

function wikiplugin_lsdir_info() {
	return array(
		'name' => tra('List Directory'),
		'documentation' => 'PluginLsDir',
		'description' => tra('Lists files in a directory'),
		'prefs' => array( 'wikiplugin_lsdir' ),
		'validate' => 'all',
		'icon' => 'pics/icons/folder_find.png',
		'params' => array(
			'dir' => array(
				'required' => true,
				'name' => tra('Directory'),
				'description' => tra('Full path to the server-local directory. Default is the document root.'),
				'default' => '',
			),
			'urlprefix' => array(
				'required' => false,
				'name' => tra('URL Prefix'),
				'description' => tra('Make the file name a link to the file by adding the url path preceding the file name. Example: http://yoursite.com/tiki/'),
				'default' => NULL
			),
			'sort' => array(
				'required' => false,
				'name' => tra('Sort Order'),
				'description' => tra('Set the sort order of the file list'),
				'default' => 'name',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('File Name'), 'value' => 'name'), 
					array('text' => tra('File Size'), 'value' => 'size'), 
					array('text' => tra('Last Access'), 'value' => 'atime'), 
					array('text' => tra('Last Metadata Change'), 'value' => 'ctime'), 
					array('text' => tra('Last Modified'), 'value' => 'mtime'), 
				)
			),
			'filter' => array(
				'required' => false,
				'name' => tra('Filter'),
				'description' => tra('Only list files with filenames that contain this filter. Example: ".jpg"'),
				'default' => NULL
			),
			'limit' => array(
				'required' => false,
				'name' => tra('Limit'),
				'description' => tra('Maximum amount of files to display. Default is no limit.'),
				'default' => 0,
			),
		),
	);
}

function wikiplugin_lsdir($data, $params) {
	global $tikilib;
//	$dir = '';
	$dir = $params['dir'];
//	$urlprefix = NULL;
	$urlprefix = $params['urlprefix'];
//	$sort = 'name';
	$sort = 'size';
	$sortmode = 'asc';
	$filter = NULL;
	$limit = 0;
	$tmp_array = array();
	$ret = '';

	extract($params, EXTR_SKIP);
	
	// make sure document_root has no trailing slash
	if (!empty($_SERVER['DOCUMENT_ROOT'])) {
		$tail = strlen($_SERVER['DOCUMENT_ROOT']) - 1;
		if (substr($_SERVER['DOCUMENT_ROOT'], $tail) == '/') {
			$pathprefix = substr($_SERVER['DOCUMENT_ROOT'], 0, $tail);
		} else {
			$pathprefix = $_SERVER['DOCUMENT_ROOT'];
		}
	}
	
	// make sure dir has starting slash
	if (!empty($dir)) {
		if (substr($dir, 0, 1) != '/') {
			$dir = '/' . $dir;
		}
	}
	
	$dir = $pathprefix . $dir;
	
	// make sure urlprefix has a trailing slash
	if (!empty($urlprefix)) {
		$tail = strlen($urlprefix) - 1;
		if (substr($urlprefix, $tail) != '/') {
			$urlprefix .= '/';
		}
	}
	
	if ($limit>0) {
		$count = 0;
	} else {
		$count = -1;
	}
	
	// fileatime, filectime, filemtime, filesize are PHP functions
	if ($sort == 'atime') {
		$getkey = 'fileatime';
	} elseif ($sort == 'ctime') {
		$getkey = 'filectime';
	} elseif ($sort == 'mtime') {
		$getkey = 'filemtime';
	} elseif ($sort == 'size') {
		$getkey = 'filesize';
	}
	
	// supress the PHP error because that causes Tiki to crash
	$dh = @opendir($dir);
	
	if (!$dh) {
		$error = "<span class='attention'><b>$dir</b> ". tra("could not be opened because it doesn't exist or permission was denied") ."</span>";
		return $error;
	}
	
	while ($file = readdir($dh)) {
		if (empty($filter) || stristr($file,$filter)) {
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
