<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'admin';

require_once ('tiki-setup.php');
$adminlib = TikiLib::lib('admin');

if (! empty($_SESSION['tikifeedback'])) {
	$tikifeedback = $_SESSION['tikifeedback'];
	$_SESSION['tikifeedback'] = array();
} else {
	$tikifeedback = array();
	$_SESSION['tikifeedback'] = array();
}
$auto_query_args = array('page');

$access->check_permission('tiki_p_admin');
$logslib = TikiLib::lib('logs');

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
	TikiLib::lib('prefs')->addRecent($name);

	$_SESSION['tikifeedback'][] = array(
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
	global $prefs;
	$logslib = TikiLib::lib('logs');
	$tikilib = TikiLib::lib('tiki');
	$smarty = TikiLib::lib('smarty');
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
	$cachelib = TikiLib::lib('cache');
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
	global $prefs;
	$logslib = TikiLib::lib('logs');
	$tikilib = TikiLib::lib('tiki');
	$smarty = TikiLib::lib('smarty');
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
	$cachelib = TikiLib::lib('cache');
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
	global $prefs;
	$logslib = TikiLib::lib('logs');
	$tikilib = TikiLib::lib('tiki');
	$smarty = TikiLib::lib('smarty');
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
	simple_set_value($feature, $pref);
}

$crumbs[] = new Breadcrumb(tra('Control Panels'), tra('Sections'), 'tiki-admin.php', 'Admin+Home', tra('Help on Configuration Sections', '', true));
// Default values for AdminHome
$admintitle = tra('Control Panels');
$helpUrl = 'Admin+Home';
$helpDescription = $description = '';
$url = 'tiki-admin.php';
$adminPage = '';

$prefslib = TikiLib::lib('prefs');

if ( isset ($_REQUEST['pref_filters']) ) {
	$prefslib->setFilters($_REQUEST['pref_filters']);
}

$temp_filters = isset($_REQUEST['filters']) ? explode(' ', $_REQUEST['filters']) : null;
$smarty->assign('pref_filters', $prefslib->getFilters($temp_filters));

if ( isset( $_REQUEST['lm_preference'] ) ) {
	$check = key_check(null, false);
	if ($check === true) {
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
					$tikilib->saveEditorToolbars($toolbars, 'global', $t_action);
				}
			}
		}
	} else {
		$smarty->assign('csrferror',
			tr('Bad request - potential cross-site request forgery (CSRF) detected. Operation blocked. The security ticket may have expired - try reloading the page in this case.'));
	}
}

if ( isset( $_REQUEST['lm_criteria'] ) ) {
	$check = key_get(null, null, null, false);
	$smarty->assign('ticket', $check['ticket']);
	set_time_limit(0);
	try {
		$smarty->assign('lm_criteria', $_REQUEST['lm_criteria']);
		$results = $prefslib->getMatchingPreferences($_REQUEST['lm_criteria'], $temp_filters);
		$results = array_slice($results, 0, 50);
		$smarty->assign('lm_searchresults', $results);
		$smarty->assign('lm_error', '');
	} catch(ZendSearch\Lucene\Exception\ExceptionInterface $e) {
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
	header('Location: ' . $base_url . 'tiki-admin.php');
}

$admin_icons = array(
	"general" => array(
		'title' => tr('General'),
		'description' => tr('Global site configuration, date formats, admin password, etc.'),
		'help' => 'General Admin',
	),
	"features" => array(
		'title' => tr('Features'),
		'description' => tr('Switches for major features'),
		'help' => 'Features Admin',
	),
	"login" => array(
		'title' => tr('Log in'),
		'description' => tr('User registration, remember me cookie settings and authentication methods'),
		'help' => 'Login Config',
	),
	"community" => array(
		'title' => tr('Community'),
		'description' => tr('User specific features and settings'),
		'help' => 'Community',
	),
	"profiles" => array(
		'title' => tr('Profiles'),
		'description' => tr('Repository configuration, browse and apply profiles'),
		'help' => 'Profiles',
	),
	"look" => array(
		'title' => tr('Look & Feel'),
		'description' => tr('Theme selection, layout settings and UI effect controls'),
		'help' => 'Look and Feel',
	),
	"textarea" => array(
		'title' => tr('Editing and Plugins'),
		'description' => tr('Text editing settings applicable to many areas. Plugin activation and plugin alias management'),
		'help' => 'Text area',
	),
	"module" => array(
		'title' => tr('Modules'),
		'description' => tr('Module appearance settings'),
		'help' => 'Module',
	),
	"metatags" => array(
		'title' => tr('Meta Tags'),
		'description' => tr('Information to include in the header of each page'),
		'help' => 'Meta Tags',
	),
	"i18n" => array(
		'title' => tr('i18n'),
		'description' => tr('Internationalization and localization - multilingual features'),
		'help' => 'i18n',
	),
	"maps" => array(
		'title' => tr('Maps'),
		'description' => tr('Settings and features for maps'),
		'help' => 'Maps',
		'disabled' => false,
	),
	"performance" => array(
		'title' => tr('Performance'),
		'description' => tr('Server performance settings'),
		'help' => 'Performance',
	),
	"security" => array(
		'title' => tr('Security'),
		'description' => tr('Site security settings'),
		'help' => 'Security',
	),
	"comments" => array(
		'title' => tr('Comments'),
		'description' => tr('Comments settings'),
		'help' => 'Comments',
	),
	"rss" => array(
		'title' => tr('Feeds'),
		'help' => 'Feeds User',
		'description' => tr('Outgoing RSS feed setup'),
	),
	"connect" => array(
		'title' => tr('Connect'),
		'help' => 'Connect',
		'description' => tr('Tiki Connect - join in!'),
	),
	"rating" => array(
		'title' => tr('Rating'),
		'help' => 'Rating',
		'description' => tr('Rating settings'),
		'disabled' => $prefs['wiki_simple_ratings'] !== 'y' &&
						$prefs['wiki_comments_simple_ratings'] !== 'y' &&
						$prefs['comments_vote'] !== 'y' &&
						$prefs['rating_advanced'] !== 'y' &&
						$prefs['trackerfield_rating'] !== 'y' &&
						$prefs['article_user_rating'] !== 'y' &&
						$prefs['rating_results_detailed'] !== 'y' &&
						$prefs['rating_smileys'] !== 'y',
	),
	"search" => array(
		'title' => tr('Search'),
		'description' => tr('Search configuration'),
		'help' => 'Search',
		'disabled' => $prefs['feature_search'] !== 'y' &&
						$prefs['feature_search_fulltext'] !== 'y',
	),
	"wiki" => array(
		'title' => tr('Wiki'),
		'disabled' => $prefs['feature_wiki'] != 'y',
		'description' => tr('Wiki page settings and features'),
		'help' => 'Wiki Config',
	),
	"fgal" => array(
		'title' => tr('File Galleries'),
		'disabled' => $prefs['feature_file_galleries'] != 'y',
		'description' => tr('Defaults and configuration for file galleries'),
		'help' => 'File Gallery',
	),
	"blogs" => array(
		'title' => tr('Blogs'),
		'disabled' => $prefs['feature_blogs'] != 'y',
		'description' => tr('Settings for blogs'),
		'help' => 'Blog',
	),
	"gal" => array(
		'title' => tr('Image Galleries'),
		'disabled' => $prefs['feature_galleries'] != 'y',
		'description' => tr('Defaults and configuration for image galleries (will be phased out in favour of file galleries)'),
		'help' => 'Image Gallery',
	),
	"articles" => array(
		'title' => tr('Articles'),
		'disabled' => $prefs['feature_articles'] != 'y',
		'description' => tr('Settings and features for articles'),
		'help' => 'Articles',
	),
	"forums" => array(
		'title' => tr('Forums'),
		'disabled' => $prefs['feature_forums'] != 'y',
		'description' => tr('Settings and features for forums'),
		'help' => 'Forum',
	),
	"trackers" => array(
		'title' => tr('Trackers'),
		'disabled' => $prefs['feature_trackers'] != 'y',
		'description' => tr('Settings and features for trackers'),
		'help' => 'Trackers',
	),
	"polls" => array(
		'title' => tr('Polls'),
		'disabled' => $prefs['feature_polls'] != 'y',
		'description' => tr('Settings and features for polls'),
		'help' => 'Polls',
	),
	"calendar" => array(
		'title' => tr('Calendar'),
		'disabled' => $prefs['feature_calendar'] != 'y',
		'description' => tr('Settings and features for calendars'),
		'help' => 'Calendar',
	),
	"category" => array(
		'title' => tr('Categories'),
		'disabled' => $prefs['feature_categories'] != 'y',
		'description' => tr('Settings and features for categories'),
		'help' => 'Category',
	),
	"workspace" => array(
		'title' => tr('Workspaces'),
		'disabled' => $prefs['workspace_ui'] != 'y' && $prefs['feature_areas'] != 'y',
		'description' => tr('Configure workspace feature'),
		'help' => 'Workspace',
	),
	"score" => array(
		'title' => tr('Score'),
		'disabled' => $prefs['feature_score'] != 'y',
		'description' => tr('Values of actions for users rank score'),
		'help' => 'Score',
	),
	"freetags" => array(
		'title' => tr('Tags'),
		'disabled' => $prefs['feature_freetags'] != 'y',
		'description' => tr('Settings and features for tags'),
		'help' => 'Tags',
	),
	"faqs" => array(
		'title' => tr('FAQs'),
		'disabled' => $prefs['feature_faqs'] != 'y',
		'description' => tr('Settings and features for FAQs'),
		'help' => 'FAQ',
	),
	"directory" => array(
		'title' => tr('Directory'),
		'disabled' => $prefs['feature_directory'] != 'y',
		'description' => tr('Settings and features for directory of links'),
		'help' => 'Directory',
	),
	"copyright" => array(
		'title' => tr('Copyright'),
		'disabled' => $prefs['feature_copyright'] != 'y',
		'description' => tr('Site-wide copyright information'),
		'help' => 'Copyright',
	),
	"messages" => array(
		'title' => tr('Messages'),
		'disabled' => $prefs['feature_messages'] != 'y',
		'description' => tr('Message settings'),
		'help' => 'Inter-User Messages',
	),
	"userfiles" => array(
		'title' => tr('User Files'),
		'disabled' => $prefs['feature_userfiles'] != 'y',
		'description' => tr('User files settings'),
		'help' => 'User Files',
	),
	"webmail" => array(
		'title' => tr('Webmail'),
		'disabled' => $prefs['feature_webmail'] != 'y',
		'description' => tr('Webmail settings'),
		'help' => 'Webmail',
	),
	"wysiwyg" => array(
		'title' => tr('Wysiwyg'),
		'disabled' => $prefs['feature_wysiwyg'] != 'y',
		'description' => tr('Options for WYSIWYG editor'),
		'help' => 'Wysiwyg',
	),
	"ads" => array(
		'title' => tr('Banners'),
		'disabled' => $prefs['feature_banners'] != 'y',
		'description' => tr('Site advertisements and notices'),
		'help' => 'Banners',
	),
	"intertiki" => array(
		'title' => tr('InterTiki'),
		'disabled' => $prefs['feature_intertiki'] != 'y',
		'description' => tr('Set up links between Tiki servers'),
		'help' => 'InterTiki',
	),
	"semantic" => array(
		'title' => tr('Semantic Links'),
		'disabled' => $prefs['feature_semantic'] != 'y',
		'description' => tr('Manage semantic wiki links'),
		'help' => 'Semantic Admin',
	),
	"webservices" => array(
		'title' => tr('Webservices'),
		'disabled' => $prefs['feature_webservices'] != 'y',
		'description' => tr('Register and manage web services'),
		'help' => 'WebServices',
	),
	"sefurl" => array(
		'title' => tr('SEF URL'),
		'disabled' => $prefs['feature_sefurl'] != 'y' && $prefs['feature_canonical_url'] != 'y',
		'description' => tr('Search Engine Friendly URLs'),
		'help' => 'Rewrite Rules',
	),
	"video" => array(
		'title' => tr('Video'),
		'disabled' => $prefs['feature_kaltura'] != 'y',
		'description' => tr('Video integration configuration'),
		'help' => 'Kaltura Config',
	),
	"payment" => array(
		'title' => tr('Payment'),
		'disabled' => $prefs['payment_feature'] != 'y',
		'description' => tr('Payment settings'),
		'help' => 'Payment',
	),
	"socialnetworks" => array(
		'title' => tr('Social networks'),
		'disabled' => $prefs['feature_socialnetworks'] != 'y',
		'description' => tr('Configure social networks integration'),
		'help' => 'Social Networks',
	),
	"share" => array(
		'title' => tr('Share'),
		'disabled' => $prefs['feature_share'] != 'y',
		'description' => tr('Configure share feature'),
		'help' => 'Share',
	),
	"stats" => array(
		'title' => tr('Statistics'),
//		'disabled' => $prefs['feature_stats'] != 'y',
		'description' => tr('Configure statistics reporting for your site usage'),
		'help' => 'Statistics',
	),
);

if (isset($_REQUEST['page'])) {
	$adminPage = $_REQUEST['page'];
	$check = key_get(null, null, null, false);
	$smarty->assign('ticket', $check['ticket']);
	// Check if the associated incude_*.php file exists. If not, check to see if it might exist in the Addons.
	// If it exists, include the associated file and generate the ticket.
	$utilities = new TikiAddons_Utilities();
	if (file_exists("admin/include_$adminPage.php")) {
		include_once ("admin/include_$adminPage.php");
	} elseif ($filepath = $utilities->getAddonFilePath("admin/include_$adminPage.php")) {
		include_once ($filepath);
	}
	$url = 'tiki-admin.php' . '?page=' . $adminPage;

	if (isset($admin_icons[$adminPage])) {
		$admin_icon = $admin_icons[$adminPage];

		$admintitle = $admin_icon['title'];
		$description = isset($admin_icon['description']) ? $admin_icon['description'] : '';
		$helpUrl = isset($admin_icon['help']) ? $admin_icon['help'] : '';
	}
	$helpDescription = tr("Help on %0 Config", $admintitle);

	$smarty->assign('include', $adminPage);

	if (!empty($changes) && key_check(null, false)) {
		$access->redirect($_SERVER['REQUEST_URI'], '', 200);
	}

} else {
	$smarty->assign('include', 'list_sections');
	$smarty->assign('admintitle', 'Control Panels');
	$smarty->assign('description', 'Home Page for Administrators');
	$smarty->assign('headtitle', breadcrumb_buildHeadTitle($crumbs));
	$smarty->assign('description', $crumbs[0]->description);
}
$headerlib->add_cssfile('themes/base_files/feature_css/admin.css');
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
	$headerlib = TikiLib::lib('header');
	$headerlib->add_jq_onready(
		"$('body,html')
			.animate({scrollTop: $('." . htmlspecialchars($_REQUEST['lm_criteria']). "')
					.addClass('ui-state-highlight')
					.offset().top - 10}, 1);"
	);
}

foreach ($admin_icons as &$admin_icon) {
	$admin_icon = array_merge(array( 'disabled' => false, 'description' => ''), $admin_icon);
}

// SSL setup
$haveMySQLSSL = $tikilib->haveMySQLSSL();
$smarty->assign('haveMySQLSSL', $haveMySQLSSL);
if ($haveMySQLSSL) {
	$isSSL = $tikilib->isMySQLConnSSL();
} else {
	$isSSL = false;
}
$smarty->assign('mysqlSSL', $isSSL);

$smarty->assign('admin_icons', $admin_icons);

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
