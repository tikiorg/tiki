<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_system.php,v 1.15 2004-05-06 00:47:10 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');

function du($path, $begin=null) {
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
			if (isset($begin) && substr($file, 0, strlen($begin)) != $begin)
				continue; // the file name doesn't begin with the good beginning
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

function cache_templates($path,$newlang) {
	global $language;
	global $smarty;
	$oldlang=$language;
	$language=$newlang;
	if (!$path or !is_dir($path)) return 0;
	if ($dir = opendir($path)) {
		while (false !== ($file = readdir($dir))) {
			$a=explode(".",$file);
			$ext=strtolower(end($a));
			if (substr($file,0,1) == "." or $file == 'CVS') continue;
			if (is_dir($path."/".$file)) {
				$language=$oldlang;
				cache_templates($path."/".$file,$newlang);
				$language=$newlang;
			} else {
				if ($ext=="tpl") {
					$file=substr($path."/".$file,10);
					$comppath=$smarty->_get_compile_path($file);
					//rewrite the language thing, see setup_smarty.php
					$comppath=preg_replace("#/".$oldlang."/#","/".$newlang."/",$comppath,1);
					if(!$smarty->_is_compiled($file,$comppath)) {
						$smarty->_compile_resource($file,$comppath);
					}
				}
			}
		}
		closedir($dir);
	}
	$language=$oldlang;
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
		erase_dir_content("templates_c/$tikidomain");
		$logslib->add_log('system','erased templates_c content');
	} elseif ($_GET['do'] == 'temp_cache') {
		erase_dir_content("temp/cache/$tikidomain");
		$logslib->add_log('system','erased temp/cache content');
	} elseif ($_GET['do'] == 'modules_cache') {
		erase_dir_content("modules/cache/$tikidomain");
		$logslib->add_log('system','erased modules/cache content');
	}
}

if (isset($_GET['compiletemplates'])) {
	$ctempl = 'templates';
	if ($tikidomain) {
		$ctempl.= "/$tikidomain";
	}
	cache_templates($ctempl,$_GET['compiletemplates']);
	$logslib->add_log('system','compiled templates');
}

$languages = array();
$languages = $tikilib->list_languages();

$templates_c = du("templates_c/$tikidomain");
$smarty->assign('templates_c', $templates_c);

$tempcache = du("temp/cache/$tikidomain");
$smarty->assign('tempcache', $tempcache);

$modules = du("modules/cache/$tikidomain");
$smarty->assign('modules', $modules);

$templates=array();
foreach($languages as $clang) {
	if(is_dir("templates_c/$tikidomain/".$clang["value"])) {
		$templates[$clang["value"]] = du("templates_c/$tikidomain/".$clang["value"]);
	} else {
		$templates[$clang["value"]] = du("templates_c/", substr($tikidomain.$clang["value"], 1));
	}
}
$smarty->assign_by_ref('templates', $templates);

$smarty->assign('mid', 'tiki-admin_system.tpl');
$smarty->display("tiki.tpl");
?>
