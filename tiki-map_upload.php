<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$access->check_feature('feature_maps');
$access->check_permission('tiki_p_map_edit');
if (!is_dir($prefs['map_path'])) {
	$smarty->assign('msg', tra('Please create a directory named ' . $prefs['map_path'] . ' to hold your map files.'));
	$smarty->display('error.tpl');
	die;
}
$DSEP = DIRECTORY_SEPARATOR;
$max_file_size = ini_get("upload_max_filesize");
$smarty->assign('max_file_size', $max_file_size);
// checks to ensure no variable contains .. or ~ in file name or directory name
if (isset($_REQUEST["file"])) {
	$_REQUEST["file"] = preg_replace('~\.\.~', '', $_REQUEST["file"]);
	$_REQUEST["file"] = preg_replace('~\~~', '', $_REQUEST["file"]);
	$_REQUEST["file"] = preg_replace('~/+~', '/', $_REQUEST["file"]);
}
if (isset($_REQUEST["directory"])) {
	$_REQUEST["directory"] = preg_replace('~\.\.~', '', $_REQUEST["directory"]);
	$_REQUEST["directory"] = preg_replace('~\~~', '', $_REQUEST["directory"]);
	$_REQUEST["directory"] = preg_replace('~/+~', '/', $_REQUEST["directory"]);
}
if (isset($_REQUEST["dir"])) {
	$_REQUEST["dir"] = preg_replace('~\~~', '', $_REQUEST["dir"]);
	$_REQUEST["dir"] = preg_replace('~/+~', '/', $_REQUEST["dir"]);
	$directory_path = inpath($prefs['map_path'] . $_REQUEST["dir"], $prefs['map_path'] . "data");
	if ($directory_path) {
		$dir = $DSEP . "data" . substr($directory_path, strlen($prefs['map_path']) + 4);
	} else {
		$dir = $DSEP . "data";
		$directory_path = $prefs['map_path'] . $DSEP . "data";
	}
	$basedir = dirname($_REQUEST["dir"]);
	if (substr($dir, 0, 5) != $DSEP . "data") {
		$directory_path = $prefs['map_path'] . $DSEP . "data";
		$dir = $DSEP . "data";
		$basedir = $DSEP;
	}
} else {
	$directory_path = $prefs['map_path'] . $DSEP . "data";
	$dir = $DSEP . "data";
	$basedir = $DSEP;
}
$smarty->assign('dir', $dir);
$smarty->assign('basedir', $basedir);
//Do we have a file to upload?
if (isset($_REQUEST["upload"])) {
	for ($i = 1; $i <= 6; $i++) {
		if (isset($_FILES["userfile$i"]) && is_uploaded_file($_FILES["userfile$i"]['tmp_name'])) {
			if (!@move_uploaded_file($_FILES["userfile$i"]['tmp_name'], $directory_path . $DSEP . $_FILES["userfile$i"]['name'])) {
				$smarty->assign('msg', tra("Could not upload the file"));
				$smarty->display("error.tpl");
				die;
			}
		}
	}
}
//Do we have a file to delete?
if (isset($_REQUEST["action"]) && isset($_REQUEST["file"])) {
	if ($_REQUEST["action"] == "delete") {
		$access->check_authenticity();
		if (is_file($directory_path . $DSEP . $_REQUEST["file"]) && !preg_match("/^\./", $_REQUEST["file"])) {
			$access->check_permission('tiki_p_map_delete');
			unlink($directory_path . $DSEP . $_REQUEST["file"]);
		} else {
			$smarty->assign('msg', tra("File not found"));
			$smarty->display("error.tpl");
			die;
		}
	}
}
//Do we have a directory to create or delete?
if (isset($_REQUEST["action"]) && isset($_REQUEST["directory"])) {
	if ($_REQUEST["action"] == "createdir") {
		if (!preg_match("/\./", $_REQUEST["directory"])) {
			$access->check_permission('tiki_p_map_create');
			if (!@mkdir($directory_path . $DSEP . $_REQUEST["directory"])) {
				$smarty->assign('msg', tra("The Directory is not empty"));
				$smarty->display("error.tpl");
				die;
			}
		} else {
			$smarty->assign('msg', tra("Invalid directory name"));
			$smarty->display("error.tpl");
			die;
		}
	}
	if ($_REQUEST["action"] == "deldir") {
		$access->check_authenticity();
		if (!preg_match("/^\./", $_REQUEST["directory"]) || !preg_match("/\.\//", $_REQUEST["directory"])) {
			$access->check_permission('tiki_p_map_delete');
			if (!@rmdir($directory_path . $DSEP . $_REQUEST["directory"])) {
				$smarty->assign('msg', tra("The Directory is not empty"));
				$smarty->display("error.tpl");
				die;
			}
		}
	}
}
//Do we have an index to create?
if (isset($_REQUEST["action"]) && isset($_REQUEST["indexfile"]) && isset($_REQUEST["filestoindex"])) {
	if ($_REQUEST["action"] == "createindex") {
		$access->check_permission('tiki_p_map_create');
		if (preg_match("/\.\//", $_REQUEST["indexfile"]) || !preg_match("/\.shp/", $_REQUEST["indexfile"])) {
			$smarty->assign('msg', tra("Invalid file name"));
			$smarty->display("error.tpl");
			die;
		}
		if (preg_match("/\.\//", $_REQUEST["filestoindex"])) {
			$smarty->assign('msg', tra("Invalid files to index"));
			$smarty->display("error.tpl");
			die;
		}
		if (!isset($prefs['gdaltindex'])) {
			$smarty->assign('msg', tra("I do not know where is gdaltindex. Set correctly the Map feature"));
			$smarty->display("error.tpl");
			die;
		}
		$indexfile = inpath(dirname($directory_path . $DSEP . $_REQUEST["indexfile"]), $directory_path);
		$filestoindex = inpath(dirname($directory_path . $DSEP . $_REQUEST["filestoindex"]), $directory_path);
		if ($indexfile && $filestoindex && is_file($prefs['gdaltindex'])) {
			$indexfile = escapeshellarg($indexfile . $DSEP . basename($_REQUEST["indexfile"]));
			$filestoindex = escapeshellarg($filestoindex . $DSEP . basename($_REQUEST["filestoindex"]));
			$command = $prefs['gdaltindex'] . " " . $indexfile . " " . $filestoindex;
			$return = shell_exec($command);
			if ($return <> 0) {
				$smarty->assign('msg', tra("I could not create the index file"));
				$smarty->display("error.tpl");
				die;
			}
		} else {
			$smarty->assign('msg', tra("I could not create the index file"));
			$smarty->display("error.tpl");
			die;
		}
	}
}
// Get layers from the layers directory
$files = array();
$dirs = array();
if (is_dir($directory_path)) {
	$h = opendir($directory_path);
	while (($file = readdir($h)) !== false) {
		// Ignore hidden files
		if (!preg_match("/^(\.|CVS)/", $file)) {
			// Put dirs in $dirs[] and files in $files[]
			if (is_dir($directory_path . $DSEP . $file)) {
				$dirs[] = $file;
			} else {
				$files[] = $file;
			}
		}
	}
	closedir($h);
}
// if $dirs[] exists, sort it and print all elements in it.
if (is_array($dirs)) {
	sort($dirs);
}
// if $files[] exists, sort it and print all elements in it.
if (is_array($files)) {
	sort($files);
}
$smarty->assign('files', $files);
$smarty->assign('dirs', $dirs);
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Get templates from the templates/modules directori
$smarty->assign('mid', 'map/tiki-map_upload.tpl');
$smarty->display("tiki.tpl");
