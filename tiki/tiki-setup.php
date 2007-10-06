<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-setup.php,v 1.465 2007-10-06 15:18:43 nyloth Exp $
// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

require_once('lib/init/initlib.php');
if ( ! isset($_SESSION['votes']) ) $_SESSION['votes'] = array();
$num_queries = 0;
$elapsed_in_db = 0.0;
$server_load = '';
$area = 'tiki';
$crumbs = array();

require_once('lib/setup/tikisetup.class.php');
TikiSetup::prependIncludePath('lib');
TikiSetup::prependIncludePath('lib/pear');

require_once('lib/setup/timer.class.php');
$tiki_timer = new timer();
$tiki_timer->start();

require_once('tiki-setup_base.php');
require_once('lib/setup/compatibility.php');
require_once('lib/setup/prefs.php');
$crumbs[] = new Breadcrumb($siteTitle, '', $tikiIndex);

if ( $site_closed == 'y' ) require_once('lib/setup/site_closed.php');
require_once('lib/setup/error_reporting.php');
if ( $display_server_load == 'y' || $use_load_threshold == 'y' ) require_once('lib/setup/load_threshold.php');
require_once('lib/setup/absolute_urls.php');
if ( ($feature_wysiwyg != 'n' && $feature_wysiwyg != 'y') || $case_patched == 'n' ) require_once('lib/setup/patches.php');
require_once('lib/setup/sections.php');
require_once('lib/headerlib.php');

if ( isset($_REQUEST['PHPSESSID']) ) $tikilib->update_session($_REQUEST['PHPSESSID']);
elseif ( function_exists('session_id') ) $tikilib->update_session(session_id());

require_once('lib/setup/cookies.php');
require_once('lib/setup/language.php');
require_once('lib/setup/wiki.php');
if ( $feature_polls == 'y' ) require_once('lib/setup/polls.php');
if ( $feature_mailin == 'y' ) require_once('lib/setup/mailin.php');
if ( $useGroupHome == 'y' ) require_once('lib/setup/default_homepage.php');
require_once('lib/setup/user_prefs.php');
require_once('lib/setup/theme.php');
if ( $feature_babelfish == 'y' || $feature_babelfish_logo == 'y' ) require_once('lib/setup/babelfish.php');

if ( $varcheck_errors != '' ) {
	$smarty->assign('msg', $varcheck_errors);
	$smarty->display('error.tpl');
}

if ( $feature_challenge == 'y' ) require_once('lib/setup/challenge.php');
require_once('lib/setup/menus.php');
if ( $feature_usermenu == 'y' ) require_once('lib/setup/usermenu.php');
if ( $feature_live_support == 'y' ) require_once('lib/setup/live_support.php');
if ( $feature_referer_stats == 'y' || $feature_stats == 'y' ) require_once('lib/setup/stats.php');
require_once('lib/setup/dynamic_variables.php');
require_once('lib/setup/output_compression.php');
if ( $feature_debug_console == 'y' ) {
	// Include debugger class declaration. So use loggin facility in php files become much easier :)
	include_once ('lib/debug/debugger.php');
}
if ( $feature_integrator == 'y' ) require_once('lib/setup/integrator.php');
if ( $feature_search == 'y' && $feature_search_fulltext != 'y' && $search_refresh_index_mode == 'random' ) {
	include_once('lib/search/refresh.php');
	register_shutdown_function('refresh_search_index');
}
if ( isset($_REQUEST['comzone']) ) require_once('lib/setup/comments_zone.php');
if ( $feature_lastup == 'y' ) require_once('lib/setup/last_update.php');
if ( ! empty($_SESSION['interactive_translation_mode']) && ($_SESSION['interactive_translation_mode']=='on') ) {
	include_once("lib/multilingual/multilinguallib.php");
	$cachelib->empty_full_cache();
}
if ( $feature_freetags == 'y' ) require_once('lib/setup/freetags.php');
if ( $feature_userlevels == 'y' ) require_once('lib/setup/userlevels.php');
if ( $feature_fullscreen == 'y' ) require_once('lib/setup/fullscreen.php');
if ( $auth_method == 'openid' ) require_once('lib/setup/openid.php');
if ( $feature_wysiwyg == 'y' ) {
	if ( ! isset($_SESSION['wysiwyg']) ) $_SESSION['wysiwyg'] = 'n';
	$smarty->assign_by_ref('wysiwyg', $_SESSION['wysiwyg']);
}

$smarty->assign_by_ref('phpErrors', $phpErrors);
$smarty->assign_by_ref('num_queries', $num_queries);
$smarty->assign_by_ref('elapsed_in_db', $elapsed_in_db);
$smarty->assign_by_ref('crumbs', $crumbs);

$smarty->assign('lock', false);
$smarty->assign('title', $title);
$smarty->assign('edit_page', 'n');
$smarty->assign('forum_mode', 'n');
$smarty->assign('uses_tabs', 'n');
$smarty->assign('uses_jscalendar', 'n');
$smarty->assign('uses_phplayers', 'n');
$smarty->assign('wiki_extras', 'n');
