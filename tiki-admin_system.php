<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_system.php,v 1.2 2003-12-16 14:42:59 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');

function du($location) {
	if (!$location or !is_dir($location)) return 0;
	$total = 0;
	$all = opendir($location);
	while ($file = readdir($all)) {
		if (is_dir($location.'/'.$file) and $file <> ".." and $file <> ".") {
			$total += du($location.'/'.$file);
			unset($file);
		} elseif (!is_dir($location.'/'.$file)) {
			$stats = stat($location.'/'.$file);
			$total += $stats['size'];
			unset($file);
		}
	}
	closedir($all);
	unset($all);
	return $total;
}

function erase_all_dir($path) {
	if ($dir = opendir($path)) {
		while (false !== ($file = readdir($dir))) {
			if (substr($file,0,1) == ".") continue;
			if (is_dir($path."/".$file)) {
				erase_all_dir($path."/".$file);
			} else {
				unlink($path."/".$file);
			}
		}
		closedir($dir);
	}
}

if ($tiki_p_admin != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

$done = '';
$output = '';
$buf = '';

if (isset($_GET['do'])) {
	if ($_GET['do'] == 'templates_c') {
		erase_all_dir('templates_c');
	} elseif ($_GET['do'] == 'modules_cache') {
		erase_all_dir('modules/cache');
	}
}

$templates_c_size = du('templates_c');
$smarty->assign('templates_c_size', $templates_c_size);

$modules_size = du('modules/cache');
$smarty->assign('modules_size', $modules_size);

$smarty->assign('mid', 'tiki-admin_system.tpl');
$smarty->display("tiki.tpl");
?>
