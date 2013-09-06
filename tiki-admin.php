<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
 * @param $st		Type of change (0=disabled, 1=enabled, 2=changed, 3=info, 4=reset)
 * @param $num		unknown
 * @return void
 */
function add_feedback( $name, $message, $st, $num = null )
{
	global $tikifeedback;

	TikiLib::lib('prefs')->addRecent($name);

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
			if ($tikilib->set_preference($feature, 'y')) {
				add_feedback($feature, tr('%0 enabled', $feature), 1, 1);
				$logslib->add_action('feature', $feature, 'system', 'enabled');
			}
		}
	} else {
		if ((!isset($prefs[$feature]) || $prefs[$feature] != 'n')) {
			// not yet set at all or not set to n
			if ($tikilib->set_preference($feature, 'n')) {
				add_feedback($feature, tr('%0 disabled', $feature), 0, 1);
				$logslib->add_action('feature', $feature, 'system', 'disabled');
			}
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
			if ($tikilib->set_preference($pref, $_REQUEST[$feature])) {
				$prefs[$feature] = $_REQUEST[$feature];
			}
		} else {
			$tikilib->set_preference($feature, $_REQUEST[$feature]);
		}
	} elseif ($isMultiple) {
		// Multiple selection controls do not exist if no item is selected.
		// We still want the value to be updated.
		if ($pref != '') {
			if ($tikilib->set_preference($pref, array())) {
				$prefs[$feature] = $_REQUEST[$feature];
			}
		} else {
			$tikilib->set_preference($feature, array());
		}
	}
	if (isset($_REQUEST[$feature]) && $old != $_REQUEST[$feature]) {
		add_feedback($feature, ($_REQUEST[$feature]) ? tr('%0 set', $feature) : tr('%0 unset', $feature), 2);
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
			add_feedback($feature, tr('%0 set', $feature), 2);
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
$admintitle = tra('Administration');
$helpUrl = 'Admin+Home';
$helpDescription = $description = '';
$url = 'tiki-admin.php';
$adminPage = '';

global $prefslib; require_once 'lib/prefslib.php';

if ( isset ($_REQUEST['pref_filters']) ) {
	$prefslib->setFilters($_REQUEST['pref_filters']);
}

$temp_filters = isset($_REQUEST['filters']) ? explode(' ', $_REQUEST['filters']) : null;
$smarty->assign('pref_filters', $prefslib->getFilters($temp_filters));

if ( isset( $_REQUEST['lm_preference'] ) ) {

	$changes = $prefslib->applyChanges((array) $_REQUEST['lm_preference'], $_REQUEST);
	foreach ( $changes as $pref => $val ) {
		if ($val['type'] == 'reset') {
			add_feedback($pref, tr('%0 reset', $pref), 4);
			$logslib->add_action('feature', $pref, 'system', 'reset');
		} else {
			$value = $val['new'];
			if ( $value == 'y' ) {
				add_feedback($pref, tr('%0 enabled', $pref), 1, 1);
				$logslib->add_action('feature', $pref, 'system', 'enabled');
			} elseif ( $value == 'n' ) {
				add_feedback($pref, tr('%0 disabled', $pref), 0, 1);
				$logslib->add_action('feature', $pref, 'system', 'disabled');
			} else {
				add_feedback($pref, tr('%0 set', $pref), 1, 1);
				$logslib->add_action('feature', $pref, 'system', (is_array($val['old'])?implode($val['old'], ','):$val['old']).'=>'.(is_array($value)?implode($value, ','):$value));
			}
			/*
				Enable/disable addreference/showreference plugins alognwith references feature.
			*/
			if ($pref == 'feature_references') {
				$tikilib->set_preference('wikiplugin_addreference', $value);
				$tikilib->set_preference('wikiplugin_showreference', $value);

				/* Add/Remove the plugin toolbars from the editor */
				$toolbars = array('wikiplugin_addreference', 'wikiplugin_showreference');
				$t_action = ($value=='y') ? 'add' : 'remove';
				$tikilib->saveEditorToolbars($toolbars, 'global', 'add', $t_action);
			}
		}
	}
}

if ( isset( $_REQUEST['lm_criteria'] ) ) {
	set_time_limit(0);
	try {
		$smarty->assign('lm_criteria', $_REQUEST['lm_criteria']);
		$results = $prefslib->getMatchingPreferences($_REQUEST['lm_criteria'], $temp_filters);
		$results = array_slice($results, 0, 50);
		$smarty->assign('lm_searchresults', $results);
		$smarty->assign('lm_error', '');
	} catch(Zend_Search_Lucene_Exception $e) {
		$smarty->assign('lm_criteria', $_REQUEST['lm_criteria']);
		$smarty->assign('lm_error', $e->getMessage());
		$smarty->assign('lm_searchresults', '');
	}
} else {
	$smarty->assign('lm_criteria', '');
	$smarty->assign('lm_searchresults', '');
	$smarty->assign('lm_error', '');
}

$smarty->assign('indexNeedsRebuilding', $prefslib->indexNeedsRebuilding());

if (isset($_REQUEST['prefrebuild'])) {
	$prefslib->rebuildIndex();
}

$icons = array(
	"general" => array(
		'position' => '0px -15px;',
		'icon' => 'img/icons/large/icon-configuration.png',
		'title' => tr('General'),
		'description' => tr('Global site configuration, date formats, admin password etc.'),
		'help' => 'General+Admin',
	),
	"features" => array(
		'position' => '-100px -15px;',
		'icon' => 'img/icons/large/boot.png',
		'title' => tr('Features'),
		'description' => tr('Switches for major features'),
		'help' => 'Features+Admin',
	),
	"login" => array(
		'position' => '-200px -15px;',
		'icon' => 'img/icons/large/stock_quit.png',
		'title' => tr('Log in'),
		'description' => tr('User registration, remember me cookie settings and authentication methods'),
		'help' => 'Login+Config',
	),
	"community" => array(
		'position' => '-300px -15px;',
		'icon' => 'img/icons/large/users.png',
		'title' => tr('Community'),
		'description' => tr('User specific features and settings'),
		'help' => 'Community',
	),
	"profiles" => array(
		'position' => '-400px -15px;',
		'icon' => 'img/icons/large/profiles.png',
		'title' => tr('Profiles'),
		'description' => tr('Repository configuration, browse and apply profiles'),
		'help' => 'Profiles',
	),
	"look" => array(
		'position' => '-500px -15px;',
		'icon' => 'img/icons/large/gnome-settings-background.png',
		'title' => tr('Look & Feel'),
		'description' => tr('Theme selection, layout settings and UI effect controls'),
		'help' => 'Look+and+Feel',
	),
	"textarea" => array(
		'position' => '-100px -115px;',
		'icon' => 'img/icons/large/editing48x48.png',
		'title' => tr('Editing and Plugins'),
		'description' => tr('Text editing settings applicable to many areas. Plugin activation and plugin alias management'),
		'help' => 'Text+area',
	),
	"module" => array(
		'position' => '-200px -115px;',
		'icon' => 'img/icons/large/display-capplet.png',
		'title' => tr('Module'),
		'description' => tr('Module appearance settings'),
		'help' => 'Module',
	),
	"metatags" => array(
		'position' => '-300px -115px;',
		'icon' => 'img/icons/large/metatags.png',
		'title' => tr('Meta Tags'),
		'description' => tr('Information to include in the header of each page'),
		'help' => 'Meta+Tags',
	),
	"i18n" => array(
		'position' => '0px -115px;',
		'icon' => 'img/icons/large/i18n.png',
		'title' => tr('i18n'),
		'description' => tr('Internationalization and localization - multilingual features'),
		'help' => 'i18n',
	),
	"maps" => array(
		'icon' => 'img/icons/large/maps.png',
		'position' => '-100px -515px;',
		'title' => tr('Maps'),
		'description' => tr('Settings and features for maps'),
		'help' => 'Maps',
		'disabled' => false,
	),
	"performance" => array(
		'position' => '-400px -115px;',
		'icon' => 'img/icons/large/performance.png',
		'title' => tr('Performance'),
		'description' => tr('Server performance settings'),
		'help' => 'Performance',
	),
	"security" => array(
		'position' => '-500px -115px;',
		'icon' => 'img/icons/large/gnome-lockscreen48x48.png',
		'title' => tr('Security'),
		'description' => tr('Site security settings'),
		'help' => 'Security',
	),
	"comments" => array(
		'position' => '0px -215px;',
		'icon' => 'img/icons/large/comments.png',
		'title' => tr('Comments'),
		'description' => tr('Comments settings'),
		'help' => 'Comments',
	),
	"rss" => array(
		'position' => '-100px -215px;',
		'icon' => 'img/icons/large/feed-icon.png',
		'title' => tr('Feeds'),
		'help' => 'Feeds User',
		'description' => tr('Outgoing RSS feed setup'),
	),
	"connect" => array(
		'position' => '-200px -215px;',
		'icon' => 'img/icons/large/gnome-globe.png',
		'title' => tr('Connect'),
		'help' => 'Connect',
		'description' => tr('Tiki Connect - join in!'),
	),
	"rating" => array(
		'position' => '-300px -215px;',
		'icon' => 'img/icons/large/rating.png',
		'title' => tr('Rating'),
		'help' => 'Advanced+Rating',
		'disabled' => $prefs['wiki_simple_ratings'] !== 'y' &&
						$prefs['rating_advanced'] !== 'y' &&
						$prefs['article_user_rating'] !== 'y',
	),
	"search" => array(
		'icon' => 'img/icons/large/xfce4-appfinder.png',
		'position' => '-400px -415px;',
		'title' => tr('Search'),
		'description' => tr('Search configuration'),
		'help' => 'Search',
		'disabled' => $prefs['feature_search'] !== 'y' &&
							$prefs['feature_search_fulltext'] !== 'y',
	),
	"wiki" => array(
		'position' => '-400px -215px;',
		'icon' => 'img/icons/large/wikipages.png',
		'title' => tr('Wiki'),
		'disabled' => $prefs['feature_wiki'] != 'y',
		'description' => tr('Wiki page settings and features'),
		'help' => 'Wiki+Config',
	),
	"fgal" => array(
		'position' => '-500px -215px;',
		'icon' => 'img/icons/large/file-manager.png',
		'title' => tr('File Galleries'),
		'disabled' => $prefs['feature_file_galleries'] != 'y',
		'description' => tr('Defaults and configuration for file galleries'),
		'help' => 'File+Gallery',
	),
	"blogs" => array(
		'position' => '0px -315px;',
		'icon' => 'img/icons/large/blogs.png',
		'title' => tr('Blogs'),
		'disabled' => $prefs['feature_blogs'] != 'y',
		'description' => tr('Settings for blogs'),
		'help' => 'Blog',
	),
	"gal" => array(
		'position' => '-100px -315px;',
		'icon' => 'img/icons/large/stock_select-color.png',
		'title' => tr('Image Galleries'),
		'disabled' => $prefs['feature_galleries'] != 'y',
		'description' => tr('Defaults and configuration for image galleries (will be phased out in favour of file galleries)'),
		'help' => 'Image+Gallery',
	),
	"articles" => array(
		'position' => '-200px -315px;',
		'icon' => 'img/icons/large/stock_bold.png',
		'title' => tr('Articles'),
		'disabled' => $prefs['feature_articles'] != 'y',
		'description' => tr('Settings and features for articles'),
		'help' => 'Articles',
	),
	"forums" => array(
		'icon' => 'img/icons/large/stock_index.png',
		'position' => '-300px -315px;',
		'title' => tr('Forums'),
		'disabled' => $prefs['feature_forums'] != 'y',
		'description' => tr('Settings and features for forums'),
		'help' => 'Forum',
	),
	"trackers" => array(
		'icon' => 'img/icons/large/gnome-settings-font.png',
		'position' => '-400px -315px;',
		'title' => tr('Trackers'),
		'disabled' => $prefs['feature_trackers'] != 'y',
		'description' => tr('Settings and features for trackers'),
		'help' => 'Trackers',
	),
	"polls" => array(
		'icon' => 'img/icons/large/stock_missing-image.png',
		'position' => '-500px -315px;',
		'title' => tr('Polls'),
		'disabled' => $prefs['feature_polls'] != 'y',
		'description' => tr('Settings and features for polls'),
		'help' => 'Polls',
	),
	"calendar" => array(
		'icon' => 'img/icons/large/date.png',
		'position' => '0px -415px;',
		'title' => tr('Calendar'),
		'disabled' => $prefs['feature_calendar'] != 'y',
		'description' => tr('Settings and features for calendars'),
		'help' => 'Calendar',
	),
	"category" => array(
		'icon' => 'img/icons/large/categories.png',
		'position' => '-100px -415px;',
		'title' => tr('Categories'),
		'disabled' => $prefs['feature_categories'] != 'y',
		'description' => tr('Settings and features for categories'),
		'help' => 'Category',
	),
	"workspace" => array(
		'icon' => 'img/icons/large/areas.png',
		'position' => '-500px -715px;',
		'title' => tr('Workspaces & Areas'),
		'disabled' => $prefs['workspace_ui'] != 'y' && $prefs['feature_areas'] != 'y',
		'description' => tr('Configure workspace feature'),
		'help' => 'workspace',
	),
	"score" => array(
		'icon' => 'img/icons/large/stock_about.png',
		'position' => '-200px -415px;',
		'title' => tr('Score'),
		'disabled' => $prefs['feature_score'] != 'y',
		'description' => tr('Values of actions for users rank score'),
		'help' => 'Score',
	),
	"freetags" => array(
		'icon' => 'img/icons/large/vcard.png',
		'position' => '-300px -415px;',
		'title' => tr('Freetags'),
		'disabled' => $prefs['feature_freetags'] != 'y',
		'description' => tr('Settings and features for freetags'),
		'help' => 'Tags',
	),
	"faqs" => array(
		'icon' => 'img/icons/large/stock_dialog_question.png',
		'position' => '-500px -415px;',
		'title' => tr('FAQs'),
		'disabled' => $prefs['feature_faqs'] != 'y',
		'description' => tr('Settings and features for FAQs'),
		'help' => 'FAQ',
	),
	"directory" => array(
		'icon' => 'img/icons/large/gnome-fs-server.png',
		'position' => '0px -515px;',
		'title' => tr('Directory'),
		'disabled' => $prefs['feature_directory'] != 'y',
		'description' => tr('Settings and features for directory of links'),
		'help' => 'Directory',
	),
	"gmap" => array(
		'icon' => 'img/icons/large/google_maps.png',
		'position' => '-200px -515px;',
		'title' => tr('Google Maps'),
		'disabled' => $prefs['feature_gmap'] != 'y',
		'description' => tr('Defaults and API key for Google Maps'),
		'help' => 'gmap',
	),
	"copyright" => array(
		'icon' => 'img/icons/large/copyright.png',
		'position' => '-300px -515px;',
		'title' => tr('Copyright'),
		'disabled' => $prefs['feature_copyright'] != 'y',
		'description' => tr('Site-wide copyright information'),
		'help' => 'Copyright',
	),
	"messages" => array(
		'icon' => 'img/icons/large/messages.png',
		'position' => '-400px -515px;',
		'title' => tr('Messages'),
		'disabled' => $prefs['feature_messages'] != 'y',
		'description' => tr('Message settings'),
		'help' => 'Inter-User+Messages',
	),
	"userfiles" => array(
		'icon' => 'img/icons/large/userfiles.png',
		'position' => '-500px -515px;',
		'title' => tr('User files'),
		'disabled' => $prefs['feature_userfiles'] != 'y',
		'description' => tr('User files settings'),
		'help' => 'User+Files',
	),
	"webmail" => array(
		'icon' => 'img/icons/large/evolution.png',
		'position' => '0px -615px;',
		'title' => tr('Webmail'),
		'disabled' => $prefs['feature_webmail'] != 'y',
		'description' => tr('Webmail settings'),
		'help' => 'Webmail',
	),
	"wysiwyg" => array(
		'icon' => 'img/icons/large/wysiwyg.png',
		'position' => '-100px -615px;',
		'title' => tr('Wysiwyg'),
		'disabled' => $prefs['feature_wysiwyg'] != 'y',
		'description' => tr('Options for WYSIWYG editor'),
		'help' => 'Wysiwyg',
	),
	"ads" => array(
		'icon' => 'img/icons/large/ads.png',
		'position' => '-200px -615px;',
		'title' => tr('Site Ads and Banners'),
		'disabled' => $prefs['feature_banners'] != 'y',
		'description' => tr('Banners are a common way to display advertisements and notices on a Web page.'),
		'help' => 'Look+and+Feel',
	),
	"intertiki" => array(
		'icon' => 'img/icons/large/intertiki.png',
		'position' => '-300px -615px;',
		'title' => tr('InterTiki'),
		'disabled' => $prefs['feature_intertiki'] != 'y',
		'description' => tr('Set up links between Tiki servers'),
		'help' => 'InterTiki',
	),
	"semantic" => array(
		'icon' => 'img/icons/large/semantic.png',
		'position' => '-400px -615px;',
		'title' => tr('Semantic links'),
		'disabled' => $prefs['feature_semantic'] != 'y',
		'description' => tr('Manage semantic wiki links'),
		'help' => 'Semantic+Admin',
	),
	"webservices" => array(
		'icon' => 'img/icons/large/webservices.png',
		'position' => '-500px -615px;',
		'title' => tr('Webservices'),
		'disabled' => $prefs['feature_webservices'] != 'y',
		'description' => tr('Register and manage web services'),
		'help' => 'WebServices',
	),
	"sefurl" => array(
		'icon' => 'img/icons/large/goto.png',
		'position' => '0px -715px;',
		'title' => tr('Search engine friendly url'),
		'disabled' => $prefs['feature_sefurl'] != 'y',
		'description' => tr('Search Engine Friendly URLs'),
		'help' => 'Rewrite+Rules',
	),
	"video" => array(
		'icon' => 'img/icons/large/gnome-camera-video-32.png',
		'position' => '-100px -715px;',
		'title' => tr('Video'),
		'disabled' => $prefs['feature_kaltura'] != 'y' && $prefs['feature_watershed'] != 'y',
		'description' => tr('Video integration configuration'),
		'help' => 'Kaltura+Config',
	),
	"payment" => array(
		'icon' => 'img/icons/large/payment.png',
		'position' => '-200px -715px;',
		'title' => tr('Payment'),
		'disabled' => $prefs['payment_feature'] != 'y',
		'help' => 'Payment',
	),
	"socialnetworks" => array(
		'icon' => 'img/icons/large/socialnetworks.png',
		'position' => '-300px -715px;',
		'title' => tr('Social networks'),
		'disabled' => $prefs['feature_socialnetworks'] != 'y',
		'description' => tr('Configure social networks integration'),
		'help' => 'Social+Networks',
	),
	"share" => array(
		'icon' => 'img/icons/large/stock_contact.png',
		'position' => '-400px -715px;',
		'title' => tr('Share'),
		'disabled' => $prefs['feature_share'] != 'y',
		'description' => tr('Configure share feature'),
		'help' => 'share',
	),
);

if (isset($_REQUEST['page'])) {
	$adminPage = $_REQUEST['page'];
	if (file_exists("admin/include_$adminPage.php")) {
		include_once ("admin/include_$adminPage.php");
		$url = 'tiki-admin.php' . '?page=' . $adminPage;
	}
	if (isset($icons[$adminPage])) {
		$icon = $icons[$adminPage];

		$admintitle = $icon['title'];
		$description = isset($icon['description']) ? $icon['description'] : '';
		$helpUrl = isset($icon['help']) ? $icon['help'] : '';
	}
	$helpDescription = tr("Help on %0 Config", $admintitle);

} else {
	$smarty->assign('admintitle', 'Admin Home');
	$smarty->assign('description', 'Home Page for Administrators');
	$smarty->assign('headtitle', breadcrumb_buildHeadTitle($crumbs));
	$smarty->assign('description', $crumbs[0]->description);
}
$headerlib->add_cssfile('css/admin.css');
if (isset($admintitle) && isset($description)) {
	$crumbs[] = new Breadcrumb($admintitle, $description, $url, $helpUrl, $helpDescription);
	$smarty->assign_by_ref('admintitle', $admintitle);
	$headtitle = breadcrumb_buildHeadTitle($crumbs);
	$smarty->assign_by_ref('headtitle', $headtitle);
	$smarty->assign_by_ref('helpUrl', $helpUrl);
	$smarty->assign_by_ref('description', $description);
}

// VERSION TRACKING
$forcecheck = ! empty($_GET['forcecheck']);

// Versioning feature has been enabled, so if the time is right, do a live
// check, otherwise display the stored data.
if ($prefs['feature_version_checks'] == 'y' || $forcecheck) {
	$checker = new Tiki_Version_Checker;
	$checker->setVersion($TWV->version);
	$checker->setCycle($prefs['tiki_release_cycle']);

	$expiry = $tikilib->now - $prefs['tiki_version_check_frequency'];
	$upgrades = $checker->check(
		function ($url) use ($expiry)
		{
			$cachelib = TikiLib::lib('cache');
			$tikilib = TikiLib::lib('tiki');

			$content = $cachelib->getCached($url, 'http', $expiry);

			if ($content === false) {
				$content = $tikilib->httprequest($url);
				$cachelib->cacheItem($url, $content, 'http');
			}

			return $content;
		}
	);

	$smarty->assign(
		'upgrade_messages',
		array_map(
			function ($upgrade)
			{
				return $upgrade->getMessage();
			},
			$upgrades
		)
	);
}

if (isset($_REQUEST['lm_criteria']) && isset($_REQUEST['exact'])) {
	global $headerlib;
	$headerlib->add_jq_onready(
		"$('body,html')
			.animate({scrollTop: $('." . htmlspecialchars($_REQUEST['lm_criteria']). "')
					.addClass('ui-state-highlight')
					.offset().top - 10}, 1);"
	);
}

foreach ($icons as &$icon) {
	$icon = array_merge(array( 'disabled' => false, 'description' => '', 'icon' => 'img/icons/large/green_question48x48.png'), $icon);
}

// SSL setup
$isSSL = $tikilib->isMySQLConnSSL();
$smarty->assign('mysqlSSL', $isSSL);
$haveMySQLSSL = $tikilib->haveMySQLSSL();
$smarty->assign('haveMySQLSSL', $haveMySQLSSL);

$smarty->assign('icons', $icons);

$smarty->assign_by_ref('tikifeedback', $tikifeedback);
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template
$smarty->assign('adminpage', $adminPage);
$smarty->assign('mid', 'tiki-admin.tpl');
$smarty->assign('trail', $crumbs);
$smarty->assign('crumb', count($crumbs) - 1);
include_once ('installer/installlib.php');
$installer = new Installer;
$smarty->assign('db_requires_update', $installer->requiresUpdate());
$smarty->display('tiki.tpl');
