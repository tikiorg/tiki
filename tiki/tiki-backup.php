<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-backup.php,v 1.11 2004-03-28 07:32:23 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/backups/backupslib.php');

// Check for admin permission
if ($tiki_p_admin != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["generate"])) {
	check_ticket('backup');
	$filename = md5($tikilib->genPass()). '.sql';

	$backuplib->backup_database("backups/$tikidomain$filename");
}

$smarty->assign('restore', 'n');

if (isset($_REQUEST["restore"])) {
	check_ticket('backup');
	$smarty->assign('restore', 'y');

	$smarty->assign('restorefile', basename($_REQUEST["restore"]));
}

if (isset($_REQUEST["rrestore"])) {
	check_ticket('backup');
	$backuplib->restore_database("backups/$tikidomain" . basename($_REQUEST["rrestore"]));
}

if (isset($_REQUEST["remove"])) {
	check_ticket('backup');
	$filename = "backups/$tikidomain" . basename($_REQUEST["remove"]);

	unlink ($filename);
}

if (isset($_REQUEST["upload"])) {
	check_ticket('backup');
	if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
		$fp = fopen($_FILES['userfile1']['tmp_name'], "r");

		$fw = fopen("backups/$tikidomain" . $_FILES['userfile1']['name'], "w");

		while (!feof($fp)) {
			$data = fread($fp, 4096);

			fwrite($fw, $data);
		}

		fclose ($fp);
		fclose ($fw);
		unlink ($_FILES['userfile1']['tmp_name']);
	} else {
		$smarty->assign('msg', tra("Upload failed"));

		$smarty->display("error.tpl");
		die;
	}
}

// Get all the files listed in the backups directory
// And put them in an array with the filemtime of
// each file activated
$backups = array();
$h = opendir("backups/$tikidomain");

while ($file = readdir($h)) {
	if (strstr($file, "sql")) {
		$row["filename"] = $file;

		$row["created"] = filemtime("backups/$tikidomain$file");
		$row["size"] = filesize("backups/$tikidomain$file") / 1000000;
		$backups[] = $row;
	}
}

closedir ($h);
$smarty->assign_by_ref('backups', $backups);
$smarty->assign('tikidomain', $tikidomain);
ask_ticket('backup');

$smarty->assign('mid', 'tiki-backup.tpl');
$smarty->display("tiki.tpl");

?>
