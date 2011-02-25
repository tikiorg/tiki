<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'admin';

require_once ('tiki-setup.php');
include_once ('lib/admin/adminlib.php');

$tikifeedback = array();
$auto_query_args = array('page');

$access->check_permission('tiki_p_admin');
global $logslib; include_once('lib/logs/logslib.php');

/**
 * Display feedback on prefs changed
 * 
 * @param $name		Name of feature
 * @param $message	Other message
 * @param $st		Type of change (0=disabled, 1=enabled, 2=changed, 3=info)
 * @param $num		unknown
 * @return void
 */
function add_feedback( $name, $message, $st, $num = null )
{
	global $tikifeedback;
	$tikifeedback[] = array(
		'num' => $num,
		'mes' => $message,
		'st' => $st,
		'name' => $name,
	);
}

/**
 * simple_set_toggle 
 * 
 * @param mixed $feature 
 * @access public
 * @return void
 */
function simple_set_toggle($feature)
{
	global $_REQUEST, $tikilib, $smarty, $prefs, $logslib;
	if (isset($_REQUEST[$feature]) && $_REQUEST[$feature] == 'on') {
		if ((!isset($prefs[$feature]) || $prefs[$feature] != 'y')) {
			// not yet set at all or not set to y
			$tikilib->set_preference($feature, 'y');
			add_feedback( $feature, tr('%0 enabled', $feature), 1, 1 );
			$logslib->add_action('feature', $feature, 'system', 'enabled');
		}
	} else {
		if ((!isset($prefs[$feature]) || $prefs[$feature] != 'n')) {
			// not yet set at all or not set to n
			$tikilib->set_preference($feature, 'n');
			add_feedback($feature, tr('%0 disabled', $feature), 0, 1);
			$logslib->add_action('feature', $feature, 'system', 'disabled');
		}
	}
	global $cachelib;
	require_once ('lib/cache/cachelib.php');
	$cachelib->invalidate('allperms');
}

/**
 * simple_set_value 
 * 
 * @param mixed $feature 
 * @param string $pref 
 * @param mixed $isMultiple 
 * @access public
 * @return void
 */
function simple_set_value($feature, $pref = '', $isMultiple = false)
{
	global $_REQUEST, $tikilib, $prefs, $logslib;
	$old = $prefs[$feature];
	if (isset($_REQUEST[$feature])) {
		if ($pref != '') {
			$tikilib->set_preference($pref, $_REQUEST[$feature]);
			$prefs[$feature] = $_REQUEST[$feature];
		} else {
			$tikilib->set_preference($feature, $_REQUEST[$feature]);
		}
	} elseif ($isMultiple) {
		// Multiple selection controls do not exist if no item is selected.
		// We still want the value to be updated.
		if ($pref != '') {
			$tikilib->set_preference($pref, array());
			$prefs[$feature] = $_REQUEST[$feature];
		} else {
			$tikilib->set_preference($feature, array());
		}
	}
	if (isset($_REQUEST[$feature]) && $old != $_REQUEST[$feature]) {
		add_feedback( $feature, ($_REQUEST[$feature]) ? tr('%0 set', $feature) : tr('%0 unset', $feature), 2 );
		$logslib->add_action('feature', $feature, 'system', $old .'=>'.isset($_REQUEST['feature'])?$_REQUEST['feature']:'');
	}
	global $cachelib;
	require_once ('lib/cache/cachelib.php');
	$cachelib->invalidate('allperms');
}

/**
 * simple_set_int 
 * 
 * @param mixed $feature 
 * @access public
 * @return void
 */
function simple_set_int($feature) 
{
	global $_REQUEST, $tikilib, $prefs, $logslib;
	if (isset($_REQUEST[$feature]) && is_numeric($_REQUEST[$feature])) {
		$old = $prefs[$feature];
		if ($old != $_REQUEST[$feature]) {
			$tikilib->set_preference($feature, $_REQUEST[$feature]);
			add_feedback( $feature, tr('%0 set', $feature), 2 );
			$logslib->add_action('feature', $feature, 'system', $old . '=>' . $_REQUEST['feature']);
		}
	}
}

/**
 * byref_set_value 
 * 
 * @param mixed $feature 
 * @param string $pref 
 * @access public
 * @return void
 */
function byref_set_value($feature, $pref = '')
{
	global $_REQUEST, $tikilib, $smarty, $logslib;
	simple_set_value($feature, $pref);
}

$crumbs[] = new Breadcrumb(tra('Administration'), tra('Sections'), 'tiki-admin.php', 'Admin+Home', tra('Help on Configuration Sections', '', true));
// Default values for AdminHome
$admintitle = 'Administration';
$helpUrl = 'Admin+Home';
$helpDescription = $description = '';
$url = 'tiki-admin.php';
$adminPage = '';

global $prefslib; require_once 'lib/prefslib.php';
if( isset( $_REQUEST['lm_preference'] ) ) {
	
	$changes = $prefslib->applyChanges( (array) $_REQUEST['lm_preference'], $_REQUEST );
	foreach( $changes as $pref => $val ) {
		$value = $val['new'];
		if( $value == 'y' ) {
			add_feedback( $pref, tr('%0 enabled', $pref), 1, 1 );
			$logslib->add_action('feature', $pref, 'system', 'enabled');
		} elseif( $value == 'n' ) {
			add_feedback( $pref, tr('%0 disabled', $pref), 0, 1 );
			$logslib->add_action('feature', $pref, 'system', 'disabled');
		} else {
			add_feedback( $pref, tr('%0 set', $pref), 1, 1 );
			$logslib->add_action('feature', $pref, 'system', (is_array($val['old'])?implode($val['old'], ','):$val['old']).'=>'.(is_array($value)?implode($value, ','):$value));
		}
	}
}

if( isset( $_REQUEST['lm_criteria'] ) ) {
	global $prefslib; require_once 'lib/prefslib.php';

	set_time_limit(0);
	try {
		$smarty->assign( 'lm_criteria', $_REQUEST['lm_criteria'] );
		$results = $prefslib->getMatchingPreferences( $_REQUEST['lm_criteria'] );
		$results = array_slice( $results, 0, 10 );
		$smarty->assign( 'lm_searchresults', $results );
		$smarty->assign( 'lm_error', '' );
	} catch(Zend_Search_Lucene_Exception $e) {
		$smarty->assign( 'lm_criteria', $_REQUEST['lm_criteria'] );
		$smarty->assign( 'lm_error', $e->getMessage() );
		$smarty->assign( 'lm_searchresults', '' );
	}
} else {
	$smarty->assign( 'lm_criteria', '' );
	$smarty->assign( 'lm_searchresults', '' );
	$smarty->assign( 'lm_error', '' );
}

$smarty->assign('indexNeedsRebuilding', $prefslib->indexNeedsRebuilding());

if (isset($_REQUEST['page'])) {
	$adminPage = $_REQUEST['page'];
	if ($adminPage == 'features') {
		$admintitle = 'Features'; //get_strings tra('Features')
		$description = 'Enable/disable Tiki features here, but configure them elsewhere'; //get_strings tra('Enable/disable Tiki features here, but configure them elsewhere')
		$helpUrl = 'Features+Admin';
		include_once ('tiki-admin_include_features.php');
	} else if ($adminPage == 'general') {
		$admintitle = 'General'; //get_strings tra('General')
		$description = 'General preferences and settings'; //get_strings tra('General preferences and settings')
		$helpUrl = 'General+Admin';
		include_once ('tiki-admin_include_general.php');
	} else if ($adminPage == 'login') {
		$admintitle = 'Login'; //get_strings tra('Login')
		$description = 'User registration, login and authentication'; //get_strings tra('User registration, login and authentication')
		$helpUrl = 'Login+Config';
		include_once ('tiki-admin_include_login.php');
	} else if ($adminPage == 'wiki') {
		$admintitle = 'Wiki'; //get_strings tra('Wiki')
		$description = 'Wiki settings'; //get_strings tra('Wiki settings')
		$helpUrl = 'Wiki+Config';
		include_once ('tiki-admin_include_wiki.php');
	} else if ($adminPage == 'wikiatt') {
		$admintitle = 'Wiki Attachments'; //get_strings tra('Wiki Attachments')
		$description = 'Wiki attachments'; //get_strings tra('Wiki attachments')
		$helpUrl = 'Wiki+Config';
		include_once ('tiki-admin_include_wikiatt.php');
	} else if ($adminPage == 'gal') {
		$admintitle = 'Image Galleries'; //get_strings tra('Image Galleries')
		$description = 'Image galleries'; //get_strings tra('Image galleries')
		$helpUrl = 'Image+Gallery';
		include_once ('tiki-admin_include_gal.php');
	} else if ($adminPage == 'fgal') {
		$admintitle = 'File Galleries'; //get_strings tra('File Galleries')
		$description = 'File Galleries'; //get_strings tra('File Galleries')
		$helpUrl = 'File+Gallery';
		include_once ('tiki-admin_include_fgal.php');
	} else if ($adminPage == 'cms') {
		$admintitle = 'Articles'; //get_strings tra('Articles')
		$description = 'Article/CMS settings'; //get_strings tra('Article/CMS settings')
		$helpUrl = 'Articles';
		include_once ('tiki-admin_include_cms.php');
	} else if ($adminPage == 'polls') {
		$admintitle = 'Polls'; //get_strings tra('Polls')
		$description = 'Poll comments settings'; //get_strings tra('Poll comments settings')
		$helpUrl = 'Polls';
		include_once ('tiki-admin_include_polls.php');
	} else if ($adminPage == 'blogs') {
		$admintitle = 'Blogs'; //get_strings tra('Blogs')
		$description = 'Configuration options for all blogs on your site'; //get_strings tra('Configuration options for all blogs on your site')
		$helpUrl = 'Blog';
		include_once ('tiki-admin_include_blogs.php');
	} else if ($adminPage == 'forums') {
		$admintitle = 'Forums'; //get_strings tra('Forums')
		$description = 'Forums settings'; //get_strings tra('Forums settings')
		$helpUrl = 'Forum';
		include_once ('tiki-admin_include_forums.php');
	} else if ($adminPage == 'faqs') {
		$admintitle = 'FAQs'; //get_strings tra('FAQs')
		$description = 'FAQ comments settings'; //get_strings tra('FAQ comments settings')
		$helpUrl = 'FAQ';
		include_once ('tiki-admin_include_faqs.php');
	} else if ($adminPage == 'trackers') {
		$admintitle = 'Trackers'; //get_strings tra('Trackers')
		$description = 'Trackers settings'; //get_strings tra('Trackers settings')
		$helpUrl = 'Trackers';
		include_once ('tiki-admin_include_trackers.php');
	} else if ($adminPage == 'webmail') {
		$admintitle = 'Webmail'; //get_strings tra('Webmail')
		$description = 'Webmail';
		$helpUrl = 'Webmail';
		include_once ('tiki-admin_include_webmail.php');
	} else if ($adminPage == 'comments') {
		$admintitle = 'Comments'; //get_strings tra('Comments')
		$description = 'Comments settings'; //get_strings tra('Comments settings')
		$helpUrl = 'Comments';
		include_once ('tiki-admin_include_comments.php');
	} else if ($adminPage == 'rss') {
		$admintitle = 'Feeds'; //get_strings tra('Feeds')
		$description = 'Feeds settings'; //get_strings tra('Feeds settings')
		$helpUrl = 'Feeds User';
		include_once ('tiki-admin_include_rss.php');
	} else if ($adminPage == 'directory') {
		$admintitle = 'Directory'; //get_strings tra('Directory')
		$description = 'Directory settings'; //get_strings tra('Directory settings')
		$helpUrl = 'Directory';
		include_once ('tiki-admin_include_directory.php');
	} else if ($adminPage == 'userfiles') {
		$admintitle = 'User Files'; //get_strings tra('User files')
		$description = 'User files'; //get_strings tra('User files')
		$helpUrl = 'User+Files';
		include_once ('tiki-admin_include_userfiles.php');
	} else if ($adminPage == 'maps') {
		$admintitle = 'Maps'; //get_strings tra('Maps')
		$description = 'Maps configuration'; //get_strings tra('Maps configuration')
		$helpUrl = 'Maps';
		include_once ('tiki-admin_include_maps.php');
	} else if ($adminPage == 'metatags') {
		$admintitle = 'Meta Tags'; //get_strings tra('Meta Tags')
		$description = 'Meta Tags settings'; //get_strings tra('Meta Tags settings')
		$helpUrl = 'Meta+Tags';
		include_once ('tiki-admin_include_metatags.php');
	} else if ($adminPage == 'performance') {
		$admintitle = 'Performance'; //get_strings tra('Performance')
		$description = 'Speed & Performance'; // get_strings('Speed & Performance')
		$helpUrl = 'Performance';
		include_once ('tiki-admin_include_performance.php');
	} else if ($adminPage == 'security') {
		$admintitle = 'Security'; //get_strings tra('Security')
		$description = 'Security'; //get_strings tra('Security')
		$helpUrl = 'Security';
		include_once ('tiki-admin_include_security.php');
	} else if ($adminPage == 'search') {
		$admintitle = 'Search'; //get_strings tra('Search')
		$description = 'Search settings'; //get_strings tra('Search settings')
		$helpUrl = 'Search';
		include_once ('tiki-admin_include_search.php');
	} else if ($adminPage == 'score') {
		$admintitle = 'Score'; //get_strings tra('Score')
		$description = 'Score settings'; //get_strings tra('Score settings')
		$helpUrl = 'Score';
		include_once ('tiki-admin_include_score.php');
	} else if ($adminPage == 'community') {
		$admintitle = 'Community'; //get_strings tra('Community')
		$description = 'Community settings'; //get_strings tra('Community settings')
		$helpUrl = 'Community';
		include_once ('tiki-admin_include_community.php');
	} else if ($adminPage == 'messages') {
		$admintitle = 'Messages'; //get_strings tra('Site Identity')
		$description = 'User Messages'; // already translated
		$helpUrl = 'Inter-User+Messages';
		include_once ('tiki-admin_include_messages.php');
	} else if ($adminPage == 'calendar') {
		$admintitle = 'Calendar'; //get_strings tra('Calendar')
		$description = 'Calendar settings'; //get_strings tra('Calendar settings')
		$helpUrl = 'Calendar';
		include_once ('tiki-admin_include_calendar.php');
	} else if ($adminPage == 'intertiki') {
		$admintitle = 'Intertiki'; //get_strings tra('Intertiki')
		$description = 'Intertiki settings'; //get_strings tra('Intertiki settings')
		$helpUrl = 'InterTiki';
		include_once ('tiki-admin_include_intertiki.php');
	} else if ($adminPage == 'freetags') {
		$admintitle = 'Freetags'; //get_strings tra('Freetags')
		$description = 'Freetags settings'; //get_strings tra('Freetags settings')
		$helpUrl = 'Tags';
		include_once ('tiki-admin_include_freetags.php');
	} else if ($adminPage == 'gmap') {
		$admintitle = 'Google Maps'; //get_strings tra('Google Maps')
		$description = 'Google Maps'; //get_strings tra('Google Maps')
		$helpUrl = 'gmap';
		include_once ('tiki-admin_include_gmap.php');
	} else if ($adminPage == 'i18n') {
		$admintitle = 'i18n'; //get_strings tra('i18n')
		$description = 'Internationalization'; //get_strings tra('i18n')
		$helpUrl = 'i18n';
		include_once ('tiki-admin_include_i18n.php');
	} else if ($adminPage == 'wysiwyg') {
		$admintitle = 'wysiwyg'; //get_strings tra('i18n')
		$description = 'Wysiwyg editor'; //get_strings tra('i18n')
		$helpUrl = 'Wysiwyg';
		include_once ('tiki-admin_include_wysiwyg.php');
	} else if ($adminPage == 'copyright') {
		$admintitle = 'Copyright'; //get_strings tra('i18n')
		$description = 'Copyright management'; //get_strings tra('i18n')
		$helpUrl = 'Copyright';
		include_once ('tiki-admin_include_copyright.php');
	} else if ($adminPage == 'category') {
		$admintitle = 'Category'; //get_strings tra('Category')
		$description = 'Category'; //get_strings tra('Category')
		$helpUrl = 'Category';
		include_once ('tiki-admin_include_category.php');
	} else if ($adminPage == 'module') {
		$admintitle = 'Module'; //get_strings tra('Module')
		$description = 'Module'; //get_strings tra('Module')
		$helpUrl = 'Module';
		include_once ('tiki-admin_include_module.php');
	} else if ($adminPage == 'look') {
		$admintitle = 'Look & Feel'; //get_strings tra('Look & Feel')
		$description = 'Customize look and feel of your Tiki'; //get_strings tra('Customize look and feel of your Tiki')
		$helpUrl = 'Look+and+Feel';
		include_once ('tiki-admin_include_look.php');
	} else if ($adminPage == 'textarea') {
		$admintitle = 'Text area'; //get_strings tra('Text area')
		$description = 'Text area'; //get_strings tra('Text area')
		$helpUrl = 'Text+area';
		include_once ('tiki-admin_include_textarea.php');
	} else if ($adminPage == 'ads') {
		$admintitle = 'Site Ads and Banners'; // get_strings('Site Ads and Banners');
		$description = 'Configure Site Ads and Banners'; //get_strings tra('Configure Site Ads and Banners')
		$helpUrl = 'Look+and+Feel';
		include_once ('tiki-admin_include_ads.php');
	} else if ($adminPage == 'profiles') {
		$admintitle = 'Profiles';		// get_strings('Profiles')
		$description = 'Install predefined configuration profiles and add-ons.';		// get_strings('Install predefined configuration profiles and add-ons.')
		$helpUrl = 'Profiles';
		include_once ('tiki-admin_include_profiles.php');
	} else if ($adminPage == 'plugins') {
		$admintitle = 'Plugin Alias';		// get_strings('Plugin Alias')
		$description = 'Create shortcut syntaxes to plugins.';		// get_strings('Create shortcut syntaxes to plugins.')
		$helpUrl = 'Plugin+Alias';
		include_once ('tiki-admin_include_plugins.php');
	} else if ($adminPage == 'semantic') {
		$admintitle = 'Semantic Wiki Links';		// get_strings('Semantic Wiki Links')
		$description = 'Manage semantic tokens used throughout the wiki.';		// get_strings('Manage semantic tokens used throughout the wiki.')
		$helpUrl = 'Semantic+Admin';
		include_once ('tiki-admin_include_semantic.php');
	} else if ($adminPage == 'webservices') {
		$admintitle = 'Webservice Registration';		// get_strings('Webservice Registration')
		$description = 'Discover and register web services to allow direct use in wiki pages.';		// get_strings('Discover and register web services to allow direct use in wiki pages.')
		$helpUrl = 'WebServices';
		include_once ('tiki-admin_include_webservices.php');
	} else if ($adminPage == 'sefurl') {
		$admintitle = 'Search engine friendly url';		// get_strings('Search engine friendly url')
		$description = 'Search engine friendly url';		// get_strings('Search engine friendly url')
		$helpUrl = 'Rewrite+Rules';
		include_once ('tiki-admin_include_sefurl.php');
	} else if ($adminPage == 'video') {
		$admintitle = 'Video';		//get_strings tra('Video')
		$description = 'Settings for video streaming integration';		//get_strings tra('Settings for video streaming integration')
		$helpUrl = 'Kaltura+Config';
		include_once ('tiki-admin_include_video.php');
	} else if ($adminPage == 'connect') {
		$admintitle = 'Connect';		// get_strings('Connect')
		$description = 'Connect';		// get_strings('Connect')
		$helpUrl = 'Connect';
		include_once ('tiki-admin_include_connect.php');
	} else if ($adminPage == 'rating') {
		$admintitle = 'Advanced Rating';		// get_strings('Advanced Rating')
		$description = 'Advanced Rating';		// get_strings('Advanced Rating')
		$helpUrl = 'Advanced+Rating';
		include_once ('tiki-admin_include_rating.php');
	} else if ($adminPage == 'payment') {
		$admintitle = 'Payment';		// get_strings('Payment')
		$description = 'Payment';		// get_strings('Payment')
		$helpUrl = 'Payment';
		include_once ('tiki-admin_include_payment.php');
	} else if ($adminPage == 'socialnetworks') {
		$admintitle = 'Social Networks';		// get_strings('Social Networks')
		$description = 'Settings for social networks integration';		// get_strings('Settings for social networks integration')
		$helpUrl = 'Social+Networks';
		include_once ('tiki-admin_include_socialnetworks.php');
	} else if ($adminPage == 'share') {
		$admintitle = 'Share';		// get_strings('Share')
		$description = 'Settings for share preferences';		// get_strings('Settings for share preferences')
		$helpUrl = 'share';
		include_once ('tiki-admin_include_share.php');
	} else {
		$helpUrl = '';
	}
	$url = 'tiki-admin.php' . '?page=' . $adminPage;
	if (!$helpUrl) {
		$helpUrl = ucfirst($adminPage) . '+Config';
	}
	$helpDescription = "Help on $admintitle Config"; //get_strings tra("Help on $admintitle Config")
	
} else {
	$smarty->assign('admintitle', 'Admin Home');
	$smarty->assign('description', 'Home Page for Administrators');
	$smarty->assign('headtitle', breadcrumb_buildHeadTitle($crumbs));
	$smarty->assign('description', $crumbs[0]->description);
}
$headerlib->add_cssfile('css/admin.css');
if (isset($admintitle)) {
	$admintitle = tra($admintitle);
	$crumbs[] = new Breadcrumb($admintitle, $description, $url, $helpUrl, $helpDescription);
	$smarty->assign_by_ref('admintitle', $admintitle);
	$headtitle = breadcrumb_buildHeadTitle($crumbs);
	$smarty->assign_by_ref('headtitle', $headtitle);
	$smarty->assign_by_ref('helpUrl', $helpUrl);
	$smarty->assign_by_ref('description', $description);
}

// VERSION TRACKING
// If the user elected to force a check.
if (!empty($_GET['forcecheck'])) {
	$smarty->assign('tiki_release', $TWV->getLatestMinorRelease());
	if (!$TWV->isLatestMinorRelease()) {
		$prefs['tiki_needs_upgrade'] = 'y';
	} else {
		$prefs['tiki_needs_upgrade'] = 'n';
		add_feedback( null, tr('Current version is up to date : <b>%0</b>', $TWV->version), 3 );
	}
	$smarty->assign('tiki_needs_upgrade', $prefs['tiki_needs_upgrade']);
	// See if a major release is available.
	if (!$TWV->isLatestMajorVersion()) {
		add_feedback( null, tr('A new %0 major release branch is available.', $TWV->branch.'('.$TWV->latestRelease.')'), 3 );
	}
	$tikilib->set_preference('tiki_needs_upgrade', $prefs['tiki_needs_upgrade']);
	$tikilib->set_preference('tiki_release', $TWV->getLatestMinorRelease());
}

// Versioning feature has been enabled, so if the time is right, do a live
// check, otherwise display the stored data.
if ($prefs['feature_version_checks'] == 'y') {
	// Pull version check database settings
	$tiki_version_last_check = $tikilib->get_preference('tiki_version_last_check', 0);
	$tiki_version_check_frequency = $tikilib->get_preference('tiki_version_check_frequency', 0);
	// Time for a version check!
	if ($tikilib->now > ($prefs['tiki_version_last_check'] + $prefs['tiki_version_check_frequency'])) {
		$tikilib->set_preference('tiki_version_last_check', $tikilib->now);
		$smarty->assign('tiki_version', $TWV->version);
		if (!$TWV->isLatestMinorRelease()) {
			$prefs['tiki_needs_upgrade'] = 'y';
			$tikilib->set_preference('tiki_release', $TWV->getLatestMinorRelease());
			$smarty->assign('tiki_release', $TWV->getLatestMinorRelease());
			if (!$TWV->isLatestMajorVersion()) {
				add_feedback( null, tr('A new %0 major release branch is available.', $TWV->branch.'('.$TWV->latestRelease.')'), 3, 1);
			}
		} else {
			$prefs['tiki_needs_upgrade'] = 'n';
			$tikilib->set_preference('tiki_release', $TWV->version);
			$smarty->assign('tiki_release', $TWV->version);
		}
		$tikilib->set_preference('tiki_needs_upgrade', $prefs['tiki_needs_upgrade']);
		$smarty->assign('tiki_needs_upgrade', $prefs['tiki_needs_upgrade']);
	} else {
		$tiki_needs_upgrade = $tikilib->get_preference('tiki_needs_upgrade', 'n');
		$smarty->assign('tiki_needs_upgrade', $tiki_needs_upgrade);
		$tiki_release = $tikilib->get_preference('tiki_release', $TWV->version);
		$smarty->assign('tiki_release', $tiki_release);
		// Normalize database if necessary.  Usually when an upgrade has
		// actually been done, but for whatever reason the database has
		// not had its version tracking info updated.
		if ($tiki_needs_upgrade == 'y' && version_compare($TWV->version, $tiki_release, '>=')) {
			$tiki_needs_upgrade = 'n';
			$tikilib->set_preference('tiki_needs_upgrade', $tiki_needs_upgrade);
			$smarty->assign('tiki_needs_upgrade', $tiki_needs_upgrade);
		}
	}
}

$smarty->assign_by_ref('tikifeedback', $tikifeedback);
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template
$smarty->assign('adminpage', $adminPage);
$smarty->assign('mid', 'tiki-admin.tpl');
if (isset($helpUrl)) $smarty->assign_by_ref('sectionhelp', $helpUrl);
if (isset($description)) $smarty->assign('description', $description);
$smarty->assign('trail', $crumbs);
$smarty->assign('crumb', count($crumbs) - 1);
include_once ('installer/installlib.php');
$installer = new Installer;
$smarty->assign('db_requires_update', $installer->requiresUpdate());
$smarty->display('tiki.tpl');
