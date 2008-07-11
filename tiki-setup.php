<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-setup.php,v 1.474.2.11 2008-03-20 19:35:06 kerrnel22 Exp $
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/* Automatically set params used for absolute URLs - BEGIN */

$tmp = dirname(str_replace(realpath(dirname(__FILE__)),'',realpath($_SERVER['SCRIPT_FILENAME'])));
if ($tmp != '/') {
        $dir_level = substr_count($tmp,"/");
} else {
        $dir_level = 0;
}
unset($tmp);

$tikiroot = dirname($_SERVER['PHP_SELF']);
$tikipath = dirname($_SERVER['SCRIPT_FILENAME']);

if ($dir_level > 0) {
        $tikiroot = preg_replace('#(/[^/]+){'.$dir_level.'}$#','',$tikiroot);
        $tikipath = preg_replace('#(/[^/]+){'.$dir_level.'}$#','',$tikipath);
        chdir(join('../',array_fill(0,$dir_level+1,'')));
}

if ( substr($tikiroot,-1,1) != '/' ) $tikiroot .= '/';
if ( substr($tikipath,-1,1) != '/' ) $tikipath .= '/';

require_once('lib/init/initlib.php');
///if ( ! isset($_SESSION['votes']) ) $_SESSION['votes'] = array();
$num_queries = 0;
$elapsed_in_db = 0.0;
$server_load = '';
$area = 'tiki';
$crumbs = array();

require_once('lib/setup/tikisetup.class.php');
TikiSetup::prependIncludePath($tikipath);
TikiSetup::prependIncludePath('lib');
TikiSetup::prependIncludePath('lib/pear');

require_once('lib/setup/timer.class.php');
$tiki_timer = new timer();
$tiki_timer->start();

require_once('tiki-setup_base.php');
require_once('lib/setup/compatibility.php');
require_once('lib/setup/prefs.php');
// TikiTests are PHP5 only
if ($prefs['feature_tikitests'] == 'y' and version_compare(PHP_VERSION, '5.0.0', '>='))  {
	require_once('tiki_tests/tikitestslib.php');
}
$crumbs[] = new Breadcrumb($prefs['siteTitle'], '', $prefs['tikiIndex']);

if ( $prefs['site_closed'] == 'y' ) require_once('lib/setup/site_closed.php');
require_once('lib/setup/error_reporting.php');
if ( $prefs['feature_bot_bar_debug'] == 'y' || $prefs['use_load_threshold'] == 'y' ) require_once('lib/setup/load_threshold.php');
require_once('lib/setup/absolute_urls.php');
if ( ($prefs['feature_wysiwyg'] != 'n' && $prefs['feature_wysiwyg'] != 'y') || $prefs['case_patched'] == 'n' ) require_once('lib/setup/patches.php');
require_once('lib/setup/sections.php');
require_once('lib/headerlib.php');

if ( isset($_REQUEST['PHPSESSID']) ) $tikilib->update_session($_REQUEST['PHPSESSID']);
elseif ( function_exists('session_id') ) $tikilib->update_session(session_id());

require_once('lib/setup/cookies.php');
require_once('lib/setup/js_detect.php');
require_once('lib/setup/user_prefs.php');
require_once('lib/setup/language.php');
require_once('lib/setup/wiki.php');
if ( $prefs['feature_polls'] == 'y' ) require_once('lib/setup/polls.php');
if ( $prefs['feature_mailin'] == 'y' ) require_once('lib/setup/mailin.php');
if ( $prefs['useGroupHome'] == 'y' ) require_once('lib/setup/default_homepage.php');
require_once('lib/setup/theme.php');
if ( $prefs['feature_babelfish'] == 'y' || $prefs['feature_babelfish_logo'] == 'y' ) require_once('lib/setup/babelfish.php');

if ( $varcheck_errors != '' ) {
	$smarty->assign('msg', $varcheck_errors);
	$smarty->display('error.tpl');
	die;
}

if ( $prefs['feature_challenge'] == 'y' ) require_once('lib/setup/challenge.php');
require_once('lib/setup/menus.php');
if ( $prefs['feature_usermenu'] == 'y' ) require_once('lib/setup/usermenu.php');
if ( $prefs['feature_live_support'] == 'y' ) require_once('lib/setup/live_support.php');
if ( $prefs['feature_referer_stats'] == 'y' || $prefs['feature_stats'] == 'y' ) require_once('lib/setup/stats.php');
require_once('lib/setup/dynamic_variables.php');
require_once('lib/setup/output_compression.php');
if ( $prefs['feature_debug_console'] == 'y' ) {
	// Include debugger class declaration. So use loggin facility in php files become much easier :)
	include_once ('lib/debug/debugger.php');
}
if ( $prefs['feature_integrator'] == 'y' ) require_once('lib/setup/integrator.php');
if ( $prefs['feature_search'] == 'y' && $prefs['feature_search_fulltext'] != 'y' && $prefs['search_refresh_index_mode'] == 'random' ) {
	include_once('lib/search/refresh.php');
	register_shutdown_function('refresh_search_index');
}
if ( isset($_REQUEST['comzone']) ) require_once('lib/setup/comments_zone.php');
if ( $prefs['feature_lastup'] == 'y' ) require_once('lib/setup/last_update.php');
if ( ! empty($_SESSION['interactive_translation_mode']) && ($_SESSION['interactive_translation_mode']=='on') ) {
	include_once("lib/multilingual/multilinguallib.php");
	$cachelib->empty_full_cache();
}
if ( $prefs['feature_freetags'] == 'y' ) require_once('lib/setup/freetags.php');
//if ( $prefs['feature_categories'] == 'y') require_once('lib/setup/categories.php');
if ( $prefs['feature_userlevels'] == 'y' ) require_once('lib/setup/userlevels.php');
if ( $prefs['feature_fullscreen'] == 'y' ) require_once('lib/setup/fullscreen.php');
if ( $prefs['auth_method'] == 'openid' ) require_once('lib/setup/openid.php');
if ( $prefs['feature_wysiwyg'] == 'y' ) {
	if ( ! isset($_SESSION['wysiwyg']) ) $_SESSION['wysiwyg'] = 'n';
	$smarty->assign_by_ref('wysiwyg', $_SESSION['wysiwyg']);
}

$smarty->assign_by_ref('phpErrors', $phpErrors);
$smarty->assign_by_ref('num_queries', $num_queries);
$smarty->assign_by_ref('elapsed_in_db', $elapsed_in_db);
$smarty->assign_by_ref('crumbs', $crumbs);

$smarty->assign('lock', false);
$smarty->assign('edit_page', 'n');
$smarty->assign('forum_mode', 'n');
$smarty->assign('uses_tabs', 'n');
$smarty->assign('uses_jscalendar', 'n');
$smarty->assign('uses_phplayers', 'n');
$smarty->assign('wiki_extras', 'n');

$smarty->assign('tikipath', $tikipath);
$smarty->assign('tikiroot', $tikiroot);
$smarty->assign('url_scheme', $url_scheme);
$smarty->assign('url_host', $url_host);
$smarty->assign('url_port', $url_port);
$smarty->assign('url_path', $url_path);

$smarty->assign('dir_level', $dir_level);
$smarty->assign('base_host', $base_host);
$smarty->assign('base_url', $base_url);
$smarty->assign('base_url_http', $base_url_http);
$smarty->assign('base_url_https', $base_url_https);

$smarty->assign('show_stay_in_ssl_mode', $show_stay_in_ssl_mode);
$smarty->assign('stay_in_ssl_mode', $stay_in_ssl_mode);

// Enable Versioning
// Please update the specified class below at release time, as well as
// adding new release to http://tikiwiki.org/{$branch}.version file
include_once ('lib/setup/twversion.class.php');
$TWV = new TWVersion();
$smarty->assign('tiki_version', $TWV->version);
$smarty->assign('tiki_branch', $TWV->branch);
$smarty->assign('tiki_star', $TWV->star);
$smarty->assign('tiki_uses_cvs', $TWV->cvs);

