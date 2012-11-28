<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
global $prefs, $tikilib;
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}
if (version_compare(PHP_VERSION, '5.3.0', '<')) {
	header('location: tiki-install.php');
	exit;
}

// Be sure that the user is not already defined by PHP on hosts that still have the php.ini config "register_globals = On"
unset($user);

require_once 'lib/setup/third_party.php';
// Enable Versioning
include_once ('lib/setup/twversion.class.php');
$TWV = new TWVersion();
$num_queries = 0;
$elapsed_in_db = 0.0;
$server_load = '';
$area = 'tiki';
$crumbs = array();
require_once ('lib/setup/tikisetup.class.php');
require_once ('lib/setup/timer.class.php');
$tiki_timer = new timer();
$tiki_timer->start();
require_once ('tiki-setup_base.php');

// Attempt setting locales. This code is just a start, locales should be set per-user.
// Also, different operating systems use different locale strings. en_US.utf8 is valid on POSIX systems, maybe not on Windows, feel free to add alternative locale strings.
setlocale(LC_ALL, ''); // Attempt changing the locale to the system default.
// Since the system default may not be UTF-8 but we may be dealing with multilingual content, attempt ensuring the collations are intelligent by forcing a general UTF-8 collation.
// This will have no effect if the locale string is not valid or if the designated locale is not generated.

foreach (array('en_US.utf8') as $UnicodeLocale) {
	if (setlocale(LC_COLLATE, $UnicodeLocale)) {
		break;
	}
}

if ($prefs['feature_tikitests'] == 'y') {
	require_once ('tiki_tests/tikitestslib.php');
}
$crumbs[] = new Breadcrumb($prefs['browsertitle'], '', $prefs['tikiIndex']);
if ($prefs['site_closed'] == 'y') {
	require_once ('lib/setup/site_closed.php');
}
require_once ('lib/setup/error_reporting.php');
if ($prefs['use_load_threshold'] == 'y') {
	require_once ('lib/setup/load_threshold.php');
}
require_once ('lib/setup/sections.php');
require_once ('lib/headerlib.php');

$domain_map = array();
if ( isset($_SERVER['HTTP_HOST']) ) {
	$host = $_SERVER['HTTP_HOST'];
} else {
	$host = "";
}
if ( isset($_SERVER['REQUEST_URI']) ) {
	$requestUri = $_SERVER['REQUEST_URI'];
} else {
	$requestUri = "";
}

if ( $prefs['tiki_domain_prefix'] == 'strip' && substr($host, 0, 4) == 'www.' ) {
	$domain_map[$host] = substr($host, 4);
} elseif ( $prefs['tiki_domain_prefix'] == 'force' && substr($host, 0, 4) != 'www.' ) {
	$domain_map[$host] = 'www.' . $host;
}

if (strpos($prefs['tiki_domain_redirects'], ',') !== false) {
	foreach (explode("\n", $prefs['tiki_domain_redirects']) as $row) {
		list($old, $new) = array_map('trim', explode(',', $row, 2));
		$domain_map[$old] = $new;
	}
}

if ( isset($domain_map[$host]) ) {
	$prefix = $tikilib->httpPrefix();
	$prefix = str_replace("://$host", "://{$domain_map[$host]}", $prefix);
	$url = $prefix . $requestUri;

	$access->redirect($url, null, 301);
	exit;
}

if (isset($_REQUEST['PHPSESSID'])) {
	$tikilib->setSessionId($_REQUEST['PHPSESSID']);
}
elseif (function_exists('session_id')) $tikilib->setSessionId(session_id());

if ($prefs['cookie_consent_feature'] === 'y' && empty($_COOKIE[$prefs['cookie_consent_name']])) {
	$feature_no_cookie = true;
} else {
	$feature_no_cookie = false;
}

// Session info needs to be kept up to date if pref login_multiple_forbidden is set
if ( $prefs['login_multiple_forbidden'] == 'y' ) {
	$tikilib->update_session();
}

require_once ('lib/setup/cookies.php');

if ($prefs['mobile_feature'] === 'y') {
	require_once ('lib/setup/mobile.php');	// needs to be before js_detect but after cookies
} else {
	$prefs['mobile_mode'] = '';
}

require_once ('lib/setup/user_prefs.php');
require_once ('lib/setup/language.php');
require_once ('lib/setup/javascript.php');
require_once ('lib/setup/wiki.php');

/* Cookie consent setup, has to be after the JS decision and wiki setup */

$cookie_consent_html = '';
if ($prefs['cookie_consent_feature'] === 'y') {
	if (!empty($_REQUEST['cookie_consent_checkbox']) || $prefs['site_closed'] === 'y') {
		// js disabled
		$feature_no_cookie = false;
		setCookieSection($prefs['cookie_consent_name'], 'y');
	}
	$cookie_consent = getCookie($prefs['cookie_consent_name']);
	if (empty($cookie_consent)) {
		if ($prefs['javascript_enabled'] !== 'y') {
			$prefs['cookie_consent_mode'] = '';
		} else {
			$headerlib->add_js('jqueryTiki.no_cookie = true; jqueryTiki.cookie_consent_alert = "' . addslashes($prefs['cookie_consent_alert']) . '";');
		}
		foreach ($_COOKIE as $k => $v) {
			setcookie($k, '', time() - 3600);		// unset any previously existing cookies
		}
		$cookie_consent_html = $smarty->fetch('cookie_consent.tpl');
	} else {
		$feature_no_cookie = false;
	}
}
$smarty->assign('cookie_consent_html', $cookie_consent_html);

if ($prefs['feature_polls'] == 'y') {
	require_once ('lib/setup/polls.php');
}
if ($prefs['feature_mailin'] == 'y') {
	require_once ('lib/setup/mailin.php');
}
require_once ('lib/setup/tikiIndex.php');
if ($prefs['useGroupHome'] == 'y') {
	require_once ('lib/setup/default_homepage.php');
}

// change $prefs['tikiIndex'] if feature_sefurl is enabled (e.g. tiki-index.php?page=HomePage becomes HomePage)
if ($prefs['feature_sefurl'] == 'y') {
	//TODO: need a better way to know which is the type of the tikiIndex URL (wiki page, blog, file gallery etc)
	//TODO: implement support for types other than wiki page and blog
	if ($prefs['tikiIndex'] == 'tiki-index.php' && $prefs['wikiHomePage']) {
		global $wikilib; include_once('lib/wiki/wikilib.php');
		$prefs['tikiIndex'] = $wikilib->sefurl($userlib->best_multilingual_page($prefs['wikiHomePage']));
	} else if (substr($prefs['tikiIndex'], 0, strlen('tiki-view_blog.php')) == 'tiki-view_blog.php') {
		include_once('tiki-sefurl.php');
		$prefs['tikiIndex'] = filter_out_sefurl($prefs['tikiIndex'], 'blog');
	}
}

require_once ('lib/setup/theme.php');
if ($prefs['feature_babelfish'] == 'y' || $prefs['feature_babelfish_logo'] == 'y') {
	require_once ('lib/setup/babelfish.php');
}
if (!empty($varcheck_errors)) {
	$smarty->assign('msg', $varcheck_errors);
	$smarty->display('error_raw.tpl');
	die;
}
if ($prefs['feature_challenge'] == 'y') {
	require_once ('lib/setup/challenge.php');
}
if ($prefs['feature_usermenu'] == 'y') {
	require_once ('lib/setup/usermenu.php');
}
if ($prefs['feature_live_support'] == 'y') {
	require_once ('lib/setup/live_support.php');
}
if ($prefs['feature_referer_stats'] == 'y' || $prefs['feature_stats'] == 'y') {
	require_once ('lib/setup/stats.php');
}
require_once ('lib/setup/dynamic_variables.php');
require_once ('lib/setup/output_compression.php');
if ($prefs['feature_debug_console'] == 'y') {
	// Include debugger class declaration. So use loggin facility in php files become much easier :)
	include_once ('lib/debug/debugger.php');
}
if ($prefs['feature_integrator'] == 'y') {
	require_once ('lib/setup/integrator.php');
}
if (isset($_REQUEST['comzone'])) {
	require_once ('lib/setup/comments_zone.php');
}
if ($prefs['feature_lastup'] == 'y') {
	require_once ('lib/setup/last_update.php');
}
if (!empty($_SESSION['interactive_translation_mode']) && ($_SESSION['interactive_translation_mode'] == 'on')) {
	include_once ('lib/multilingual/multilinguallib.php');
	$cachelib->empty_cache('templates_c');
}
if ($prefs['feature_freetags'] == 'y') {
	require_once ('lib/setup/freetags.php');
}
if ($prefs['feature_areas'] == 'y' && $prefs['feature_categories'] == 'y') {
	global $areaslib; require_once('lib/perspective/binderlib.php');
	require_once ('lib/setup/categories.php');
	$areaslib->HandleObjectCategories($objectCategoryIdsNoJail);
}
if ($prefs['feature_userlevels'] == 'y') {
	require_once ('lib/setup/userlevels.php');
}
if ($prefs['auth_method'] == 'openid') {
	require_once ('lib/setup/openid.php');
}
if ($prefs['feature_wysiwyg'] == 'y') {
	if (!isset($_SESSION['wysiwyg'])) {
		$_SESSION['wysiwyg'] = 'n';
	}
	$smarty->assign_by_ref('wysiwyg', $_SESSION['wysiwyg']);
}


if ($prefs['feature_antibot'] == 'y' && is_null($user)) {
	require_once('lib/captcha/captchalib.php');
	$smarty->assign_by_ref('captchalib', $captchalib);
}

if ($prefs['feature_credits'] == 'y') {
	require_once('lib/setup/credits.php');
}

$smarty->assign_by_ref('phpErrors', $phpErrors);
$smarty->assign_by_ref('num_queries', $num_queries);
$smarty->assign_by_ref('elapsed_in_db', $elapsed_in_db);
$smarty->assign_by_ref('crumbs', $crumbs);
$smarty->assign('lock', false);
$smarty->assign('edit_page', 'n');
$smarty->assign('forum_mode', 'n');
$smarty->assign('uses_tabs', 'n');
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
$smarty->assign('tiki_version', $TWV->version);
$smarty->assign('tiki_branch', $TWV->branch);
$smarty->assign('tiki_star', $TWV->getStar());
$smarty->assign('tiki_uses_svn', $TWV->svn);

if ( isset( $_GET['msg'] ) ) {
	$smarty->assign('display_msg', $_GET['msg']);
} elseif ( isset( $_SESSION['msg'] ) ) {
	$smarty->assign('display_msg', $_SESSION['msg']);
	unset($_SESSION['msg']);
} else {
	$smarty->assign('display_msg', '');
}

require_once 'lib/setup/events.php';

if ( $prefs['rating_advanced'] == 'y' && $prefs['rating_recalculation'] == 'randomload' ) {
	global $ratinglib; require_once 'lib/rating/ratinglib.php';
	$ratinglib->attempt_refresh();
}

$headerlib->add_jsfile('lib/tiki-js.js');

if ( $prefs['feature_cssmenus'] == 'y' ) {
	$headerlib->add_cssfile('css/cssmenus.css');
}
if ( $prefs['feature_bidi'] == 'y' ) {
	$headerlib->add_cssfile('styles/BiDi/BiDi.css');
}

if ($prefs['javascript_enabled'] != 'n') {


	if ( isset($prefs['javascript_cdn']) && $prefs['javascript_cdn'] == 'google' ) {
		$headerlib->add_jsfile_dependancy("$url_scheme://ajax.googleapis.com/ajax/libs/jquery/$headerlib->jquery_version/jquery.min.js");
	} else if ( isset($prefs['javascript_cdn']) && $prefs['javascript_cdn'] == 'jquery' ) {
		$headerlib->add_jsfile_dependancy("http://code.jquery.com/jquery-$headerlib->jquery_version.min.js");
	} else {
		if ( $prefs['tiki_minify_javascript'] === 'y' ) {
			$headerlib->add_jsfile_dependancy("lib/jquery/jquery-$headerlib->jquery_version.min.js");
		} else {
			$headerlib->add_jsfile_dependancy("lib/jquery/jquery-$headerlib->jquery_version.js");
		}
	}

	if ( $prefs['fgal_elfinder_feature'] === 'y' ) {
		$headerlib->add_jsfile("lib/jquery/elfinder/js/elfinder.min.js")
				->add_cssfile("lib/jquery/elfinder/css/elfinder.min.css")
				->add_jsfile("lib/jquery_tiki/elfinder/tiki-elfinder.js");
	}

	$headerlib->add_jsfile('lib/jquery_tiki/tiki-jquery.js');
	$headerlib->add_jsfile('lib/jquery_tiki/tiki-maps.js');
	$headerlib->add_jsfile('lib/jquery/jquery.json-2.3.js');

	if ($prefs['feature_syntax_highlighter'] == 'y') {
		//add codemirror stuff
		$headerlib
			->add_cssfile('lib/codemirror/lib/codemirror.css')
			->add_jsfile_dependancy('lib/codemirror/lib/codemirror.js')
			->add_jsfile_dependancy('lib/codemirror/lib/util/searchcursor.js')
		//add tiki stuff
			->add_cssfile('lib/codemirror_tiki/codemirror_tiki.css')
			->add_jsfile_dependancy('lib/codemirror_tiki/codemirror_tiki.js');

		require_once("lib/codemirror_tiki/tiki_codemirror.php");
		codemirrorModes($prefs['tiki_minify_javascript'] === 'y');
	}


	if ($prefs['mobile_feature'] === 'y' && $prefs['mobile_mode'] === 'y') {

		$headerlib->add_jsfile('lib/jquery_tiki/tiki-jquery.mobile.js');

		$jsmin = $prefs['tiki_minify_javascript'] === 'y' ? '.min' : '';
		$cssmin = $prefs['tiki_minify_css'] === 'y' ? '.min' : '';
		if ($prefs['mobile_use_latest_lib'] === 'y') {
			$headerlib->add_jsfile("http://code.jquery.com/mobile/latest/jquery.mobile$jsmin.js");
			$headerlib->add_cssfile("http://code.jquery.com/mobile/latest/jquery.mobile$cssmin.css");
		} else {
			// TODO add jQuery CDN when 1.1 is available there
			$headerlib->add_jsfile("lib/jquery/jquery.mobile/jquery.mobile-$headerlib->jquerymobile_version$jsmin.js");
			$headerlib->add_cssfile("lib/jquery/jquery.mobile/jquery.mobile-$headerlib->jquerymobile_version$cssmin.css");
		}

		$headerlib->drop_cssfile('css/cssmenus.css');

	} else {

		$headerlib->add_jsfile('lib/swfobject/swfobject.js');

		if ( $prefs['feature_ajax'] === 'y' ) {
			if ( $prefs['ajax_autosave'] === 'y' ) {
				$headerlib->add_jsfile('lib/ajax/autosave.js');
			}
		}

		if ( $prefs['feature_jquery_ui'] == 'y' ) {
			if ( isset($prefs['javascript_cdn']) && $prefs['javascript_cdn'] == 'google' ) {
				$headerlib->add_jsfile_dependancy("$url_scheme://ajax.googleapis.com/ajax/libs/jqueryui/$headerlib->jqueryui_version/jquery-ui.min.js");
			} else if ( isset($prefs['javascript_cdn']) && $prefs['javascript_cdn'] == 'jquery' ) {
				$headerlib->add_jsfile_dependancy("http://code.jquery.com/ui/$headerlib->jqueryui_version/jquery-ui.min.js");
			} else {
				if ( $prefs['tiki_minify_javascript'] === 'y' ) {
					$headerlib->add_jsfile_dependancy("lib/jquery/jquery-ui/ui/minified/jquery-ui-$headerlib->jqueryui_version.min.js");
				} else {
					$headerlib->add_jsfile_dependancy("lib/jquery/jquery-ui/ui/jquery-ui-$headerlib->jqueryui_version.js");
				}
			}
			$headerlib->add_jsfile('lib/jquery/jquery-ui/external/jquery.bgiframe-2.1.2.js');

			if ( isset($prefs['javascript_cdn']) && $prefs['javascript_cdn'] == 'jquery' ) {
				$headerlib->add_cssfile("http://code.jquery.com/ui/$headerlib->jqueryui_version/themes/{$prefs['feature_jquery_ui_theme']}/jquery-ui.css");
			} else {
				$headerlib->add_cssfile('lib/jquery/jquery-ui/themes/' . $prefs['feature_jquery_ui_theme'] . '/jquery-ui.css');
			}

			if ( $prefs['feature_jquery_autocomplete'] == 'y' ) {
				$headerlib->add_css(
					'.ui-autocomplete-loading { background: white url("lib/jquery/jquery-ui/themes/' .
					'base/images/ui-anim_basic_16x16.gif") right center no-repeat; }'
				);
			}
			if ( $prefs['jquery_ui_selectmenu'] == 'y' ) {
				$headerlib->add_jsfile('lib/jquery/jquery-ui-selectmenu/ui/jquery.ui.selectmenu.js');
				$headerlib->add_cssfile('lib/jquery/jquery-ui-selectmenu/themes/base/jquery.ui.selectmenu.css');
				// standard css for selectmenu seems way too big for tiki - to be added to layout.css when not so experimental
				$headerlib->add_css(
					'.ui-selectmenu-menu ul li a, .ui-selectmenu-status { white-space: nowrap; }
					.ui-selectmenu { height: 1.8em; padding-right: 16px; }
					.ui-selectmenu-menu ul { padding-right: 16px; }
					.ui-selectmenu-menu li a,.ui-selectmenu-status { line-height: 1.0em; padding: .4em 1em; }
					.ui-selectmenu-status { line-height: .8em; margin-right: 16px; }'
				);
			}
			$headerlib->add_jsfile('lib/jquery/jquery-ui-timepicker-addon.js');
		}

		if ( $prefs['feature_jquery_tooltips'] == 'y' ) {
			$headerlib->add_jsfile('lib/jquery/cluetip/lib/jquery.hoverIntent.js');
			if ( $prefs['feature_jquery_ui'] !== 'y' ) {
				$headerlib->add_jsfile('lib/jquery/cluetip/lib/jquery.bgiframe.min.js');
			}
			$headerlib->add_jsfile('lib/jquery/cluetip/jquery.cluetip.js');
			$headerlib->add_cssfile('lib/jquery/cluetip/jquery.cluetip.css');
		}

		if ( $prefs['feature_jquery_superfish'] == 'y' ) {
			$headerlib->add_jsfile('lib/jquery/superfish/js/superfish.js');
			$headerlib->add_jsfile('lib/jquery/superfish/js/supersubs.js');
		}
		if ( $prefs['feature_jquery_reflection'] == 'y' ) {
			$headerlib->add_jsfile('lib/jquery/reflection-jquery/js/reflection.js');
		}
		if ( $prefs['feature_jquery_media'] == 'y' ) {
			$headerlib->add_jsfile('lib/jquery/jquery.media.js');
		}
		if ( $prefs['feature_jquery_tablesorter'] == 'y' ) {
			$headerlib->add_cssfile('lib/jquery_tiki/tablesorter/themes/tiki/style.css');
			$headerlib->add_jsfile('lib/jquery/tablesorter/addons/pager/jquery.tablesorter.pager.js');

			if ( $prefs['tiki_minify_javascript'] === 'y' ) {
				//tablesorter has bad syntax in the non-min file, however the min file seems to work fine when double minned :)
				$headerlib->add_jsfile('lib/jquery/tablesorter/jquery.tablesorter.min.js');
			} else {
				$headerlib->add_jsfile('lib/jquery/tablesorter/jquery.tablesorter.js');
			}
		}
		if ( $prefs['feature_shadowbox'] == 'y' ) {
			$headerlib->add_jsfile('lib/jquery/colorbox/jquery.colorbox.js');
			$headerlib->add_cssfile('lib/jquery/colorbox/styles/colorbox.css');
		}
		if ( $prefs['feature_jquery_carousel'] == 'y' ) {
			$headerlib->add_jsfile('lib/jquery/infinitecarousel/jquery.infinitecarousel3.js');
		}

		if ( ( $prefs['feature_jquery'] != 'y' || $prefs['feature_jquery_tablesorter'] != 'y' ) && $prefs['javascript_enabled'] == 'y' ) {
			$headerlib->add_jsfile('lib/tiki-js-sorttable.js');
		}

		if ( $prefs['wikiplugin_flash'] == 'y' ) {
			$headerlib->add_jsfile('lib/swfobject/swfobject.js');
		}

		if ( $prefs['feature_metrics_dashboard'] == 'y' ) {
			$headerlib->add_cssfile("css/metrics.css");
			$headerlib->add_jsfile("lib/jquery/jquery.sparkline.min.js");
			$headerlib->add_jsfile("lib/metrics.js");
		}

		// include and setup themegen editor if already open
		if (! empty($tiki_p_admin) && $tiki_p_admin === 'y' && !empty($prefs['themegenerator_feature']) && $prefs['themegenerator_feature'] === 'y' && !empty($_COOKIE['themegen']) &&
				(strpos($_SERVER['SCRIPT_NAME'], 'tiki-admin.php') === false || strpos($_SERVER['QUERY_STRING'], 'page=look') === false)) {
			include_once 'lib/themegenlib.php';
			$themegenlib->setupEditor();
		}
	} // end not in $prefs['mobile_mode']

	// libs for both mobile and normal
	if ( $prefs['feature_jquery_validation'] == 'y' ) {
		$headerlib->add_jsfile('lib/jquery/jquery-validate/jquery.validate.js');
		$headerlib->add_jsfile('lib/validators/validator_required_in_group.js');
	}

	$headerlib->add_jsfile('lib/jquery/jquery-ui/external/jquery.cookie.js');
	$headerlib->add_jsfile('lib/jquery/jquery.async.js', 10);
	$headerlib->add_jsfile('lib/jquery/treeTable/src/javascripts/jquery.treeTable.js');
	$headerlib->add_cssfile('lib/jquery/treeTable/src/stylesheets/jquery.treeTable.css');

	if (empty($user) && $prefs['feature_antibot'] == 'y') {
		$headerlib->add_jsfile('lib/captcha/captchalib.js');
	}

	if ( $prefs['feature_jcapture'] === 'y' ) {
		$headerlib->add_jsfile('lib/jcapture_tiki/tiki-jcapture.js');
	}

}	// end if $prefs['javascript_enabled'] != 'n'

if ( ! empty( $prefs['header_custom_css'] ) ) {
	$headerlib->add_css($prefs['header_custom_css']);
}

if ( ! empty( $prefs['header_custom_js'] ) ) {
	$headerlib->add_js($prefs['header_custom_js']);
}

if ($prefs['feature_trackers'] == 'y') {
	$headerlib->add_jsfile('lib/jquery_tiki/tiki-trackers.js');

	if ($prefs['feed_tracker'] === 'y') {
		$opts = TikiLib::lib('trk')->get_trackers_options(null, 'publishRSS', 'y');
		foreach ($opts as & $o) {
			$o = $o['trackerId'];
		}
		$trackers = TikiLib::lib('trk')->list_trackers();

		$rss_trackers = array();
		foreach ($trackers['data'] as $trk) {
			if (in_array($trk['trackerId'], $opts)) {
				$rss_trackers[] = array(
					'trackerId' => $trk['trackerId'],
					'name' => $trk['name'],
				);
			}
		}
		TikiLib::lib('smarty')->assign('rsslist_trackers', $rss_trackers);
	}

}

if ($prefs['feature_draw'] == 'y') {
	//svg-edit/empbedapi.js neededs to be external because it is not minifyable, dirty code
	$headerlib->add_jsfile("lib/svg-edit/embedapi.js", "external");
	$headerlib->add_jsfile("lib/svg-edit_tiki/draw.js");
	$headerlib->add_cssfile("lib/svg-edit_tiki/draw.css");
}

if ($prefs['geo_always_load_openlayers'] == 'y') {
	$headerlib->add_map();
}

if ($prefs['workspace_ui'] == 'y') {
	$headerlib->add_jsfile('lib/jquery_tiki/tiki-workspace-ui.js');
}

if ($prefs['feature_sefurl'] != 'y') {
	$headerlib->add_js(
		'$.service = function (controller, action, query) {
		if (! query) {
			query = {};
		}
		query.controller = controller;

		if (action) {
			query.action = action;
		}

		return "tiki-ajax_services.php?" + $.map(query, function (v, k) {
			return k + "=" + tiki_encodeURI(v);
		}).join("&");
	};'
	);
}

if ($prefs['ajax_inline_edit'] == 'y') {
	$headerlib->add_jsfile('lib/jquery_tiki/inline_edit.js');
}

if (true) {
	// Before being clever and moving this close to where you think it's needed (again),
	// consider there are more places that you think.
	$headerlib->add_jsfile('tiki-jsplugin.php?language='.$prefs['language'], 'dynamic');
	if ($prefs['wikiplugin_module'] === 'y' && $prefs['wikiplugininline_module'] === 'n') {
		$headerlib->add_jsfile('tiki-jsmodule.php?language='.$prefs['language'], 'dynamic');
	}
}

if ( session_id() ) {
	if ( $prefs['tiki_cachecontrol_session'] ) {
		header('Cache-Control: ' . $prefs['tiki_cachecontrol_session']);
	}
} else {
	if ( $prefs['tiki_cachecontrol_nosession'] ) {
		header('Cache-Control: ' . $prefs['tiki_cachecontrol_nosession']);
	}
}

if ( isset($token_error) ) {
	$smarty->assign('token_error', $token_error);
	$smarty->display('error.tpl');
	die;
}

require_once( 'lib/setup/plugins_actions.php' );

//////////////////////////////////////////////////////////////////////////
// ********************************************************************	//
// ** IMPORTANT NOTE: 						     ** //
// ** USE THE GLOBAL VARIABLE BELOW TO CONTROL THE VERSION OF EMAIL  **	//
// ** WHICH IS USED:						     **	//
// ** 	$use_pgpmime_mail = true;				     **	//
// **		USE TIKI OpenPGP Enabled PGP/MIME-standard mail      **	//
// ** 	$use_pgpmime_mail = false;				     **	//
// **		USE TIKI normal mail functionality		     **	//
// **								     ** //
// ** SETTING THIS GLOBAL VARIABLE TO "true" NEED PROPER	     ** //
// ** CONFIGURATION OF gnupg AND RELATED KEYRING WITH PROPERLY	     ** //
// ** CONFIGURED TIKI-SENDER KEYPAIR (PRIVATE/PUBLIC) AND ALL USER   ** //
// ** ACCOUNT-RELATED PUBLIC KEYS				     ** //
// **								     ** //
// ** DO NOT SWITCH THIS VARIABLE TO TRUE FOR THIS EXPERIMENTAL	     ** //
// ** FULLY PGP/MIME-ENCRYPTION COMPLIANT EMAIL FUNCTIONALITY, IF    ** //
// ** YOU ARE **NOT ABSOLUTE SURE HOW TO CONFIGURE IT**!	     ** //
// **								     ** //
// ** ONCE PROPERLY CONFIGURED, SUCH 100% OPAQUE FUNCTIONALITY	     ** //
// ** DELIVERS ROBUST END-TO-END PRIVACY WITH HIGH DEGREE OF TESTED  ** //
// ** ROBUSTNESS FOR THE FOLLOWING MAIL TRAFFIC:		     ** //
// **								     ** //
// **	- all webmail-based messaging from messu-compose.php	     ** //
// **   - all admin notifications				     ** //
// **	- all newsletters					     ** //
// **								     ** //
// ** PLEASE NOTE THAT ALL SITE ACCOUNTS **MUST** HAVE PROPERLY	     ** //
// ** CONFIGURED OpenPGP-COMPLIANT PUBLIC-KEY IN THE SYSTEM's	     ** //
// ** KEYRING, SO IT IS NOT THEN WISE/POSSIBLE TO ALLOW ANONYMOUS    ** //
// ** SUBSCRIPTIONS TO NEWSLETTERS ETC, OR USE NOT FULLY PGP/MIME    ** //
// ** READY ACCOUNTS IN SUCH SYSTEM.				     ** //
// **								     ** //
// ** IT IS ASSUMED, THAT IF AND WHEN YOU TURN SUCH PGP/MIME ON	     ** //
// ** YOU ARE FULLY AWARE OF THE REQUIREMENTS AND CONSEQUENCES.	     ** //
// **								     ** //
static $use_pgpmime_mail = false; // NOTE: TURN true WITH YOUR RISK!
if ($use_pgpmime_mail) {
	// hollmeer 2012-11-03:
	// TURNED ON openPGP support from a lib based class
	require_once( 'lib/openpgp/openpgplib.php' );
}
// **								     ** //
// ********************************************************************	//
//////////////////////////////////////////////////////////////////////////

$headerlib->lockMinifiedJs();
