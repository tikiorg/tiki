<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$access->check_permission(array('tiki_p_clean_cache'));

$done = '';
$output = '';
$buf = '';
global $cachelib;
include_once ('lib/cache/cachelib.php');
if (isset($_GET['do'])) {
	$cachelib->empty_cache($_GET['do']);
}
if (isset($_GET['compiletemplates'])) {
	$ctempl = 'templates';
	$cachelib->cache_templates($ctempl, $_GET['compiletemplates']);
	if ($tikidomain) {
		$ctempl.= "/$tikidomain";
	}
	$cachelib->cache_templates($ctempl, $_GET['compiletemplates']);
	$logslib->add_log('system', 'compiled templates');
}
$languages = array();
$languages = $tikilib->list_languages();
$templates_c = $cachelib->count_cache_files("templates_c/$tikidomain");
$smarty->assign('templates_c', $templates_c);
$tempcache = $cachelib->count_cache_files("temp/cache/$tikidomain");
$smarty->assign('tempcache', $tempcache);
$temppublic = $cachelib->count_cache_files("temp/public/$tikidomain");
$smarty->assign('temppublic', $temppublic);
$modules = $cachelib->count_cache_files("modules/cache/$tikidomain");
$smarty->assign('modules', $modules);
$templates = array();
foreach($languages as $clang) {
	if ($smarty->use_sub_dirs) { // was if(is_dir("templates_c/$tikidomain/")) ppl with tikidomains should test. redflo
		$templates[$clang["value"]] = $cachelib->count_cache_files("templates_c/$tikidomain/" . $clang["value"] . "/");
	} else {
		$templates[$clang["value"]] = $cachelib->count_cache_files("templates_c/", $tikidomain . $clang["value"]);
	}
}
$smarty->assign_by_ref('templates', $templates);
if ($prefs['feature_forums'] == 'y') {
	include_once ('lib/comments/commentslib.php');
	$commentslib = new Comments($dbTiki);
	$dirs = $commentslib->list_directories_to_save();
} else {
	$dirs = array();
}
if ($prefs['feature_galleries'] == 'y' && !empty($prefs['gal_use_dir'])) {
	$dirs[] = $prefs['gal_use_dir'];
}
if ($prefs['feature_file_galleries'] == 'y' && !empty($prefs['fgal_use_dir'])) {
	$dirs[] = $prefs['fgal_use_dir'];
}
if ($prefs['feature_trackers'] == 'y') {
	if (!empty($prefs['t_use_dir'])) $dirs[] = $prefs['t_use_dir'];
	$dirs[] = 'img/trackers';
}
if ($prefs['feature_wiki'] == 'y') {
	if (!empty($prefs['w_use_dir'])) $dirs[] = $prefs['w_use_dir'];
	if ($prefs['feature_create_webhelp'] == 'y') $dirs[] = 'whelp';
	$dirs[] = 'img/wiki';
	$dirs[] = 'img/wiki_up';
}
$dirs = array_unique($dirs);
$dirsExist = array();
foreach($dirs as $i => $d) {
	$dirsWritable[$i] = is_writable($d);
}
$smarty->assign_by_ref('dirs', $dirs);
$smarty->assign_by_ref('dirsWritable', $dirsWritable);
if (isset($_REQUEST['zip']) && isset($_REQUEST['zipPath']) && $tiki_p_admin == 'y') {
	include_once ('lib/pclzip/pclzip.lib.php');
	if (!$archive = new PclZip($_REQUEST['zipPath'])) {
		$smarty->assign('msg', tra('Error:') . $archive->errorInfo(true));
		$smarty->display('error.tpl');
		die;
	}
	foreach($dirs as $d) {
		if (file_exists($d)) $dirs2[] = $d;
	}
	if (!$archive->add($dirs2)) {
		$smarty->assign('msg', tra('Error:') . $archive->errorInfo(true));
		$smarty->display('error.tpl');
		die;
	}
	$smarty->assign_by_ref('zipPath', $_REQUEST['zipPath']);
}
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('mid', 'tiki-admin_system.tpl');
$smarty->display("tiki.tpl");
