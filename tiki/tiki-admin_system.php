<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_system.php,v 1.3 2003-12-16 16:27:26 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');

function du($path) {
	if (!$path or !is_dir($path)) return 0;
	$total = 0; 
	$cant = 0;
	$back = array();
	$all = opendir($path);
	while ($file = readdir($all)) {
		if (is_dir($path.'/'.$file) and $file <> ".." and $file <> "." and $file <> "CVS") {
			$du = du($path.'/'.$file);
			$total+= $du['total'];
			$cant+= $du['cant'];
			unset($file);
		} elseif (!is_dir($path.'/'.$file)) {
			$stats = stat($path.'/'.$file);
			$total += $stats['size'];
			$cant++;
			unset($file);
		}
	}
	closedir($all);
	unset($all);
	$back['total'] = $total;
	$back['cant'] = $cant;
	return $back;
}

function erase_dir_content($path) {
	if (!$path or !is_dir($path)) return 0;
	if ($dir = opendir($path)) {
		while (false !== ($file = readdir($dir))) {
			if (substr($file,0,1) == "." or $file == 'CVS') continue;
			if (is_dir($path."/".$file)) {
				erase_dir_content($path."/".$file);
				rmdir($path."/".$file);
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
		erase_dir_content('templates_c');
	} elseif ($_GET['do'] == 'modules_cache') {
		erase_dir_content('modules/cache');
	}
}

$templates_c = du('templates_c');
$smarty->assign('templates_c', $templates_c);

$modules = du('modules/cache');
$smarty->assign('modules', $modules);

$smarty->assign('mid', 'tiki-admin_system.tpl');
$smarty->display("tiki.tpl");
?>
