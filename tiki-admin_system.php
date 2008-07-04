<?php
// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_system.php,v 1.28.2.5 2008/03/24 14:51:10 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');

if ($user != 'admin' && $tiki_p_admin != 'y' && $tiki_p_clean_cache != 'y') { // admin test needed for the first inclusion of this perm befroring clearing the cache
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra('You do not have permission to use this feature'));
	$smarty->display('error.tpl');
	die;
}

$done = '';
$output = '';
$buf = '';

if (isset($_GET['do'])) {
	if ($_GET['do'] == 'templates_c') {
		$cachelib->erase_dir_content("templates_c/$tikidomain");
		$logslib->add_log('system','erased templates_c content');
	} elseif ($_GET['do'] == 'temp_cache') {
		$cachelib->erase_dir_content("temp/cache/$tikidomain");
		$logslib->add_log('system','erased temp/cache content');
	} elseif ($_GET['do'] == 'modules_cache') {
		$cachelib->erase_dir_content("modules/cache/$tikidomain");
		$logslib->add_log('system','erased modules/cache content');
	} elseif ($_GET['do'] == 'prefs') {
		$tikilib->set_lastUpdatePrefs();
	}
}

if (isset($_GET['compiletemplates'])) {
	$ctempl = 'templates';
	$cachelib->cache_templates($ctempl,$_GET['compiletemplates']);
	if ($tikidomain) {
		$ctempl.= "/$tikidomain";
	}
	$cachelib->cache_templates($ctempl,$_GET['compiletemplates']);
	$logslib->add_log('system','compiled templates');
}

$languages = array();
$languages = $tikilib->list_languages();

$templates_c = $cachelib->du("templates_c/$tikidomain");
$smarty->assign('templates_c', $templates_c);

$tempcache = $cachelib->du("temp/cache/$tikidomain");
$smarty->assign('tempcache', $tempcache);

$modules = $cachelib->du("modules/cache/$tikidomain");
$smarty->assign('modules', $modules);

$templates=array();

foreach($languages as $clang) {
	if($smarty->use_sub_dirs) { // was if(is_dir("templates_c/$tikidomain/")) ppl with tikidomains should test. redflo
		$templates[$clang["value"]] = $cachelib->du("templates_c/$tikidomain/".$clang["value"]."/");
	} else {
		$templates[$clang["value"]] = $cachelib->du("templates_c/", $tikidomain.$clang["value"]);
	}
}

$smarty->assign_by_ref('templates', $templates);

// fixing UTF-8 Errors
require_once('lib/admin/adminlib.php');
$tabfields=$adminlib->list_content_tables();
$smarty->assign_by_ref('tabfields', $tabfields);

if(isset($_REQUEST['utf8it'])) {
   if($adminlib->check_utf8($_REQUEST['utf8it'],$_REQUEST['utf8if'])) {
      $smarty->assign('investigate_utf8',tra('No Errors detected'));
   } else {
      $smarty->assign('investigate_utf8',tra('Errors detected'));
   }
   $smarty->assign('utf8it',$_REQUEST['utf8it']);
   $smarty->assign('utf8if',$_REQUEST['utf8if']);
}

if(isset($_REQUEST['utf8ft'])) {
   $errc=$adminlib->fix_utf8($_REQUEST['utf8ft'],$_REQUEST['utf8ff']);
   $smarty->assign('errc',$errc);
   $smarty->assign('utf8ft',$_REQUEST['utf8ft']);
   $smarty->assign('utf8ff',$_REQUEST['utf8ff']);
}

if ($prefs['feature_forums'] == 'y') {
	include_once('lib/commentslib.php');$commentslib = new Comments($dbTiki);
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
	if (!empty($prefs['t_use_dir']))
		$dirs[] = $prefs['t_use_dir'];
	$dirs[] = 'img/trackers';
}
if ($prefs['feature_wiki'] == 'y') {
	if (!empty($prefs['w_use_dir']))
		$dirs[] = $prefs['w_use_dir'];
	if ($prefs['feature_create_webhelp'] == 'y') 
		$dirs[] = 'whelp';
	$dirs[] = 'img/wiki';
	$dirs[] = 'img/wiki_up';
}
if ($prefs['feature_maps'] && !empty($prefs['map_path'])) {
	$dirs[] = $prefs['map_path'];
}
$dirs = array_unique($dirs);
$smarty->assign_by_ref('dirs', $dirs);

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-admin_system.tpl');
$smarty->display("tiki.tpl");
?>
