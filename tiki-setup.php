<?php
/**
 * contains the hooks for Tiki's internal functionality.
 *
 * this script may only be included, it will die if called directly.
 *
 * @package TikiWiki
 * @copyright (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project. All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * @licence Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */
// $Id$

// die if called directly.
/**
 * @global array $prefs
 * @global array $tikilib
 */
global $prefs, $tikilib;
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}
if (version_compare(PHP_VERSION, '5.5.0', '<') && php_sapi_name() != 'cli') {
	header('location: tiki-install.php');
	exit;
} elseif (version_compare(PHP_VERSION, '5.5.0', '<') && php_sapi_name() == 'cli') {
	// This is command-line. No 'location' command make sense here. Let admins access what works and deal with the rest.
	echo "Warning: Tiki13 and above expects PHP 5.5.0 and above. You are running " . phpversion() . " at your own risk\n";
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
$headerlib = TikiLib::lib('header');

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
	unset($old);
	unset($new);
}

if ( isset($domain_map[$host]) && ! defined('TIKI_CONSOLE') ) {
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
		setCookieSection($prefs['cookie_consent_name'], 'y');	// set both real cookie and tiki_cookie_jar
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
			if (strpos($k, session_name()) === false) {
				setcookie($k, '', time() - 3600);        // unset any previously existing cookies except the session
			}
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
if ($prefs['feature_sefurl'] == 'y' && ! defined('TIKI_CONSOLE')) {
	//TODO: need a better way to know which is the type of the tikiIndex URL (wiki page, blog, file gallery etc)
	//TODO: implement support for types other than wiki page and blog
	if ($prefs['tikiIndex'] == 'tiki-index.php' && $prefs['wikiHomePage']) {
		$wikilib = TikiLib::lib('wiki');
		$prefs['tikiIndex'] = $wikilib->sefurl($userlib->best_multilingual_page($prefs['wikiHomePage']));
	} else if (substr($prefs['tikiIndex'], 0, strlen('tiki-view_blog.php')) == 'tiki-view_blog.php') {
		include_once('tiki-sefurl.php');
		$prefs['tikiIndex'] = filter_out_sefurl($prefs['tikiIndex'], 'blog');
	}
}

require_once ('lib/setup/theme.php');
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
	$cachelib->empty_cache('templates_c');
}
if ($prefs['feature_freetags'] == 'y') {
	require_once ('lib/setup/freetags.php');
}
if ($prefs['feature_areas'] == 'y' && $prefs['feature_categories'] == 'y' && $prefs['categories_used_in_tpl'] == 'y') {
	require_once ('lib/setup/categories.php');
	$areaslib = TikiLib::lib('areas');
	$areaslib->HandleObjectCategories($objectCategoryIdsNoJail);
} elseif ($prefs['feature_categories'] == 'y') {
	require_once ('lib/setup/categories.php');
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


if ($prefs['feature_antibot'] == 'y' && empty($user)) {
	if ($prefs['recaptcha_enabled'] === 'y') {
		if ($prefs['recaptcha_version'] == '2') {
			$headerlib->add_jsfile_cdn("$url_scheme://www.google.com/recaptcha/api.js");
		} else {
			$headerlib->add_jsfile_cdn("$url_scheme://www.google.com/recaptcha/api/js/recaptcha_ajax.js");
		}
	}
	$captchalib = TikiLib::lib('captcha');
	$smarty->assign('captchalib', $captchalib);
}

if ($prefs['feature_credits'] == 'y') {
	require_once('lib/setup/credits.php');
}

if ( $prefs['https_external_links_for_users'] == 'y' ) {
	$base_url_canonical_default = $base_url_https;
} else {
	$base_url_canonical_default = $base_url_http;
}

if ( !empty($prefs['feature_canonical_domain']) ) {
	$base_url_canonical = $prefs['feature_canonical_domain'];
} else {
	$base_url_canonical = $base_url_canonical_default;
}
// Since it's easier to be error-resistant than train users, ensure base_url_canonical ends with '/'
if ( substr($base_url_canonical,-1) != '/' ) {
	$base_url_canonical .= '/';
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
$smarty->assign('base_url_canonical', $base_url_canonical);
$smarty->assign('base_url_canonical_default', $base_url_canonical_default);
$smarty->assign('show_stay_in_ssl_mode', $show_stay_in_ssl_mode);
$smarty->assign('stay_in_ssl_mode', $stay_in_ssl_mode);
$smarty->assign('tiki_version', $TWV->version);
$smarty->assign('tiki_branch', $TWV->branch);
$smarty->assign('tiki_star', $TWV->getStar());
$smarty->assign('tiki_uses_svn', $TWV->svn);

$smarty->assign('symbols', TikiLib::symbols());

if ( isset( $_GET['msg'] ) ) {
	$smarty->assign('display_msg', $_GET['msg']);
} elseif ( isset( $_SESSION['msg'] ) ) {
	$smarty->assign('display_msg', $_SESSION['msg']);
	unset($_SESSION['msg']);
} else {
	$smarty->assign('display_msg', '');
}

if ( isset( $_GET['msgtype'] ) ) {
	$smarty->assign('display_msgtype', $_GET['msgtype']);
} elseif ( isset( $_SESSION['msgtype'] ) ) {
	$smarty->assign('display_msgtype', $_SESSION['msgtype']);
	unset($_SESSION['msgtype']);
} else {
	$smarty->assign('display_msgtype', '');
}

require_once 'lib/setup/events.php';

if ( $prefs['rating_advanced'] == 'y' && $prefs['rating_recalculation'] == 'randomload' ) {
	$ratinglib = TikiLib::lib('rating');
	$ratinglib->attempt_refresh();
}

$headerlib->add_jsfile('lib/tiki-js.js');

// using jquery-migrate-1.3.0.js plugin for tiki 11, still required in tiki 12 LTS to support some 3rd party plugins

if ( isset($prefs['javascript_cdn']) && $prefs['javascript_cdn'] == 'google' ) {
	$headerlib->add_jsfile_cdn("$url_scheme://ajax.googleapis.com/ajax/libs/jquery/$headerlib->jquery_version/jquery.min.js");
	// goggle is not hosting migrate so load from local
	$headerlib->add_jsfile_dependancy("vendor/jquery/plugins/migrate-min/jquery-migrate-$headerlib->jquerymigrate_version.min.js", true);
} else if ( isset($prefs['javascript_cdn']) && $prefs['javascript_cdn'] == 'jquery' ) {
	$headerlib->add_jsfile_cdn("$url_scheme://code.jquery.com/jquery-$headerlib->jquery_version.min.js");
	$headerlib->add_jsfile_cdn("$url_scheme://code.jquery.com/jquery-migrate-$headerlib->jquerymigrate_version.min.js");
} else {
	if ( isset($prefs['tiki_minify_javascript']) && $prefs['tiki_minify_javascript'] === 'y' ) {
		$headerlib->add_jsfile_dependancy("vendor/jquery/jquery-min/jquery-$headerlib->jquery_version.min.js", true);
		$headerlib->add_jsfile_dependancy("vendor/jquery/plugins/migrate-min/jquery-migrate-$headerlib->jquerymigrate_version.min.js", true);
	} else {
		$headerlib->add_jsfile_dependancy("vendor/jquery/jquery/jquery-$headerlib->jquery_version.js", true);
		$headerlib->add_jsfile_dependancy("vendor/jquery/plugins/migrate/jquery-migrate-$headerlib->jquerymigrate_version.js", true);
	}
}

if ( isset($prefs['fgal_elfinder_feature']) && $prefs['fgal_elfinder_feature'] === 'y' ) {
	$str = $prefs['tiki_minify_javascript'] === 'y' ? 'min' : 'full';
	// elfinder is sensible to js compression - problem is inside elfinder
	// see http://stackoverflow.com/questions/11174170/js-invalid-left-hand-side-expression-in-postfix-operation for more general details
	$headerlib->add_jsfile('vendor_extra/elfinder/js/elfinder.' . $str . '.js', true)
			->add_cssfile('vendor_extra/elfinder/css/elfinder.' . $str . '.css')
			->add_jsfile('lib/jquery_tiki/elfinder/tiki-elfinder.js');

	$elFinderLang = str_replace(array('cn', 'pt-br'), array('zh_CN', 'pt_BR'), $language);

	if (file_exists('vendor_extra/elfinder/js/i18n/elfinder.' . $elFinderLang . '.js')) {
		$headerlib->add_jsfile('vendor_extra/elfinder/js/i18n/elfinder.' . $elFinderLang . '.js');
	}
}

$headerlib->add_jsfile('lib/jquery_tiki/tiki-jquery.js');

if (isset($_REQUEST['geo_zoomlevel_to_found_location'])) {
	$zoomToFoundLocation = $_REQUEST['geo_zoomlevel_to_found_location'];
} else {
	$zoomToFoundLocation = isset($prefs['geo_zoomlevel_to_found_location']) ? $prefs['geo_zoomlevel_to_found_location'] : 'street';
}
$headerlib->add_js('var zoomToFoundLocation = "'.$zoomToFoundLocation.'";');	// Set the zoom option after searching for a location

if ($prefs['geo_openlayers_version'] === 'ol3') {
	$headerlib->add_jsfile('lib/jquery_tiki/tiki-maps-ol3.js');
} else {
	$headerlib->add_jsfile('lib/jquery_tiki/tiki-maps.js');
}
$headerlib->add_jsfile('vendor/jquery/plugins/jquery-json/src/jquery.json.js');

if ($prefs['feature_jquery_zoom'] === 'y') {
	$headerlib->add_jsfile('vendor/jquery/plugins/zoom/jquery.zoom.js')
		->add_css('
.img_zoom {
	display:inline-block;
}
.img_zoom:after {
	content:"";
	display:block;
	width:33px;
	height:33px;
	position:absolute;
	top:0;
	right:0;
	background:url(vendor/jquery/plugins/zoom/icon.png);
}
.img_zoom img {
	display:block;
}
');
}

if ($prefs['feature_syntax_highlighter'] == 'y') {
	//add codemirror stuff
	$headerlib
		->add_cssfile('vendor/codemirror/codemirror/lib/codemirror.css')
		->add_jsfile_dependancy('vendor/codemirror/codemirror/lib/codemirror.js')
		->add_jsfile('vendor/codemirror/codemirror/addon/search/searchcursor.js')
		->add_jsfile('vendor/codemirror/codemirror/addon/mode/overlay.js')
	//add tiki stuff
		->add_cssfile('lib/codemirror_tiki/codemirror_tiki.css')
		->add_jsfile('lib/codemirror_tiki/codemirror_tiki.js');

	require_once("lib/codemirror_tiki/tiki_codemirror.php");
	createCodemirrorModes();
}
if ($prefs['feature_wikilingo'] == 'y') {
	$headerlib
		//flp stuff
		->add_cssfile('vendor/jquery/plugins/tablesorter/css/theme.dropbox.css')
		->add_jsfile('vendor/jquery/plugins/tablesorter/js/jquery.tablesorter.js');
}

if ( $prefs['feature_jquery_carousel'] == 'y' ) {
	$headerlib->add_jsfile('vendor/jquery/plugins/infinitecarousel/jquery.infinitecarousel3.js');
}

if ( $prefs['feature_ajax'] === 'y' ) {
	$headerlib->add_jsfile('lib/jquery_tiki/tiki-ajax_services.js');
	if ( $prefs['ajax_autosave'] === 'y' ) {
		$headerlib->add_jsfile('lib/ajax/autosave.js');
	}
}

// $url_scheme is 'http' or 'https' depending on request type condsidering already a reverse proxy
// $https_mode is true / false depending on request type condsidering already a reverse proxy
if ( $prefs['feature_jquery_ui'] == 'y' ) {
	if ( isset($prefs['javascript_cdn']) && $prefs['javascript_cdn'] == 'google' ) {
		$headerlib->add_jsfile_cdn("$url_scheme://ajax.googleapis.com/ajax/libs/jqueryui/$headerlib->jqueryui_version/jquery-ui.min.js");
	} else if ( isset($prefs['javascript_cdn']) && $prefs['javascript_cdn'] == 'jquery' ) {
		$headerlib->add_jsfile_cdn("$url_scheme://code.jquery.com/ui/$headerlib->jqueryui_version/jquery-ui.min.js");
	} else {
		if ( $prefs['tiki_minify_javascript'] === 'y' ) {
			$headerlib->add_jsfile_dependancy("vendor/jquery/jquery-ui/jquery-ui.min.js", true);
		} else {
			$headerlib->add_jsfile_dependancy("vendor/jquery/jquery-ui/jquery-ui.js");
		}
	}

	// restore jquery-ui buttons function, thanks to http://stackoverflow.com/a/23428433/2459703
	$headerlib->add_js('
var bootstrapButton = $.fn.button.noConflict() // return $.fn.button to previously assigned value
$.fn.bootstrapBtn = bootstrapButton            // give $().bootstrapBtn the Bootstrap functionality
');

	if ( $prefs['feature_jquery_ui_theme'] !== 'none' ) {
		if ( isset($prefs['javascript_cdn']) && $prefs['javascript_cdn'] == 'jquery' ) {
			// // cdn for css not working - this is the only css from a cdn anyway - so use local version 
			// $headerlib->add_cssfile("$url_scheme://code.jquery.com/ui/$headerlib->jqueryui_version/themes/{$prefs['feature_jquery_ui_theme']}/jquery-ui.css");
			$headerlib->add_cssfile('vendor/jquery/jquery-ui-themes/themes/' . $prefs['feature_jquery_ui_theme'] . '/jquery-ui.css');
		} else {
			$headerlib->add_cssfile('vendor/jquery/jquery-ui-themes/themes/' . $prefs['feature_jquery_ui_theme'] . '/jquery-ui.css');
		}
	}

	if ( $prefs['feature_jquery_autocomplete'] == 'y' ) {
		$headerlib->add_css(
			'.ui-autocomplete-loading { background: white url("img/spinner.gif") right center no-repeat; }'
		);
	}
	if ( $prefs['jquery_ui_chosen'] == 'y' ) {
		$headerlib->add_jsfile('vendor/jquery/plugins/chosen/chosen.jquery.js');
	//	$headerlib->add_cssfile('vendor/jquery/plugins/chosen/chosen.css'); Replaced by github.com/alxlit/bootstrap-chosen
		$headerlib->add_css(
			'select { font-size: 16px; padding: 4px; border-color:#fff; /* this forces chrome to apply the font-size */ }'
		);
	}
	$headerlib->add_jsfile('vendor/jquery/jquery-timepicker-addon/dist/jquery-ui-timepicker-addon.js');
	$headerlib->add_cssfile('vendor/jquery/jquery-timepicker-addon/dist/jquery-ui-timepicker-addon.css');
}
if ( $prefs['jquery_fitvidjs'] == 'y' ) {
	$headerlib->add_jsfile('vendor/jquery/plugins/fitvidjs/jquery.fitvids.js')
				->add_jq_onready('$("article").fitVids();');		// apply fitvid to any video in the middle section
}
if ( $prefs['feature_jquery_superfish'] == 'y' ) {
	$headerlib->add_jsfile('vendor/jquery/plugins/superfish/dist/js/superfish.js');
	$headerlib->add_jsfile('vendor/jquery/plugins/superfish/dist/js/supersubs.js');
}
if ( $prefs['feature_jquery_tooltips'] === 'y' || $prefs['feature_jquery_superfish'] === 'y' ) {
	$headerlib->add_jsfile('vendor/jquery/plugins/superfish/dist/js/hoverIntent.js');
}
if ( $prefs['feature_jquery_reflection'] == 'y' ) {
	$headerlib->add_jsfile('vendor/jquery/plugins/reflection-jquery/js/reflection.js');
}
if ( $prefs['feature_jquery_media'] == 'y' ) {
	$headerlib->add_jsfile('vendor/jquery/plugins/media/jquery.media.js');
}
if ( $prefs['feature_jquery_tablesorter'] == 'y' ) {
	$headerlib->add_jsfile('vendor/jquery/plugins/tablesorter/js/jquery.tablesorter.combined.js');
	$headerlib->add_jsfile('vendor/jquery/plugins/tablesorter/js/widgets/widget-pager.js');
	$headerlib->add_jsfile('vendor/jquery/plugins/tablesorter/js/widgets/widget-filter-formatter-jui.js');
	$headerlib->add_jsfile('vendor/jquery/plugins/tablesorter/js/widgets/widget-grouping.js');
	$headerlib->add_jsfile('vendor/jquery/plugins/tablesorter/js/widgets/widget-columnSelector.js');
	$headerlib->add_jsfile('vendor/jquery/plugins/tablesorter/js/parsers/parser-input-select.js');
	$headerlib->add_jsfile('vendor/jquery/plugins/tablesorter/js/widgets/widget-math.js');
}

if ( $prefs['feature_jquery_tagcanvas'] == 'y' ) {
	$headerlib->add_jsfile('vendor/jquery/plugins/tagcanvas/jquery.tagcanvas.js');
}

if ( $prefs['feature_shadowbox'] == 'y' ) {
	$headerlib->add_jsfile('vendor/jquery/plugins/colorbox/jquery.colorbox.js');
	$headerlib->add_cssfile('vendor/jquery/plugins/colorbox/' . $prefs['jquery_colorbox_theme'] . '/colorbox.css');
}

if ( $prefs['wikiplugin_flash'] == 'y' ) {
	$headerlib->add_jsfile('lib/swfobject/swfobject.js');
}
if ($prefs['jquery_timeago'] === 'y') {
	$headerlib->add_jsfile('vendor/jquery/plugins/timeago/jquery.timeago.js');
	$language_short = substr($prefs['language'], 0, 2);
	$timeago_locale = "vendor/jquery/plugins/timeago/locales/jquery.timeago.{$language_short}.js";
	if (is_readable($timeago_locale)) {
		$headerlib->add_jsfile($timeago_locale);	// TODO handle zh-CN and zh-TW
	}
	$headerlib->add_jq_onready('$("time.timeago").timeago();');
}

if ( $prefs['feature_jquery_validation'] == 'y' ) {
	$headerlib->add_jsfile('vendor/jquery/plugins/jquery-validation/dist/jquery.validate.js');
	$headerlib->add_jsfile('lib/validators/validator_tiki.js');
}

// note: jquery.async.js load a copy of jquery
$headerlib->add_jsfile('vendor/jquery/plugins/async/jquery.async.js');
$headerlib->add_jsfile('vendor/jquery/plugins/treetable/javascripts/src/jquery.treetable.js');
$headerlib->add_cssfile('vendor/jquery/plugins/treetable/stylesheets/jquery.treetable.css');

$headerlib->add_jsfile('vendor/cwspear/bootstrap-hover-dropdown/bootstrap-hover-dropdown.js');

if ($prefs['feature_equal_height_rows_js'] == 'y'){
	$headerlib->add_jsfile("vendor/Sam152/Javascript-Equal-Height-Responsive-Rows/grids.min.js");
}

if (empty($user) && $prefs['feature_antibot'] == 'y') {
	$headerlib->add_jsfile_late('lib/captcha/captchalib.js');
}

if ( $prefs['feature_jcapture'] === 'y' ) {
	$headerlib->add_jsfile('lib/jcapture_tiki/tiki-jcapture.js');
}

if ( ! empty( $prefs['header_custom_css'] ) ) {
	$headerlib->add_css($prefs['header_custom_css']);
}

if ( ! empty( $prefs['header_custom_js'] ) ) {
	$headerlib->add_js($prefs['header_custom_js']);
}

if ($prefs['feature_file_galleries'] == 'y') {
	$headerlib->add_jsfile('lib/jquery_tiki/files.js');
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
	//svg-edit/empbedapi.js neededs to be external - why?
	$headerlib->add_jsfile("vendor/svg-edit/svg-edit/embedapi.js");
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

		return "tiki-ajax_services.php?" + $.buildParams(query);
	};'
	);
}

if ($prefs['feature_friends'] == 'y' || $prefs['monitor_enabled'] == 'y') {
	$headerlib->add_jsfile('lib/jquery_tiki/social.js');
}

if ($prefs['ajax_inline_edit'] == 'y') {
	$headerlib->add_jsfile('lib/jquery_tiki/inline_edit.js');
}

if ($prefs['mustread_enabled'] == 'y') {
	$headerlib->add_jsfile('lib/jquery_tiki/mustread.js');
}

if (true) {
	// Before being clever and moving this close to where you think it's needed (again),
	// consider there are more places that you think.
	
	// This is not a very helpful comment...
	$headerlib->add_jsfile_dynamic('tiki-jsplugin.php?language='.$prefs['language']);
	if ($prefs['wikiplugin_module'] === 'y' && $prefs['wikiplugininline_module'] === 'n') {
		$headerlib->add_jsfile_dynamic('tiki-jsmodule.php?language='.$prefs['language']);
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

if ( !empty($prefs['access_control_allow_origin']) && !empty($_SERVER['HTTP_ORIGIN']) && $base_host !== $_SERVER['HTTP_ORIGIN']) {
	$http_origin = $_SERVER['HTTP_ORIGIN'];

	if (in_array($http_origin, preg_split('/[\s,]+/', $prefs['access_control_allow_origin']))) {
	    header("Access-Control-Allow-Origin: $http_origin");
	}
}

if ( isset($token_error) ) {
	$smarty->assign('token_error', $token_error);
	$smarty->display('error.tpl');
	die;
}

require_once( 'lib/setup/plugins_actions.php' );

if ($tiki_p_admin == 'y') {
	$headerlib->add_jsfile_late('lib/jquery_tiki/tiki-admin.js');
}

if ($prefs['wikiplugin_addtocart'] == 'y') {
	$headerlib->add_jsfile('lib/payment/cartlib.js');
}

//////////////////////////////////////////////////////////////////////////
// ******************************************************************** //
// ** IMPORTANT NOTE:                                                ** //
// ** USE THE GLOBAL VARIABLE BELOW TO CONTROL THE VERSION OF EMAIL  ** //
// ** WHICH IS USED                                                  ** //
// **   $prefs['openpgp_gpg_pgpmimemail'] == 'y'                     ** //
// **       USE TIKI OpenPGP Enabled PGP/MIME-standard mail          ** //
// **   $prefs['openpgp_gpg_pgpmimemail'] == 'n'                     ** //
// **       USE TIKI normal mail functionality                       ** //
// **                                                                ** //
// ** SETTING THIS PREFERENCES VARIABLE TO "y" NEED PROPER           ** //
// ** CONFIGURATION OF gnupg AND RELATED KEYRING WITH PROPERLY       ** //
// ** CONFIGURED TIKI-SENDER KEYPAIR (PRIVATE/PUBLIC) AND ALL USER   ** //
// ** ACCOUNT-RELATED PUBLIC KEYS                                    ** //
// **                                                                ** //
// ** DO NOT SWITCH THIS VARIABLE TO TRUE FOR THIS EXPERIMENTAL      ** //
// ** FULLY PGP/MIME-ENCRYPTION COMPLIANT EMAIL FUNCTIONALITY, IF    ** //
// ** YOU ARE **NOT ABSOLUTE SURE HOW TO CONFIGURE IT**!             ** //
// **                                                                ** //
// ** ONCE PROPERLY CONFIGURED, SUCH 100% OPAQUE FUNCTIONALITY       ** //
// ** DELIVERS ROBUST END-TO-END PRIVACY WITH HIGH DEGREE OF TESTED  ** //
// ** ROBUSTNESS FOR THE FOLLOWING MAIL TRAFFIC:                     ** //
// **                                                                ** //
// **   - all webmail-based messaging from messu-compose.php         ** //
// **   - all admin notifications                                    ** //
// **   - all newsletters                                            ** //
// **                                                                ** //
// ** PLEASE NOTE THAT ALL SITE ACCOUNTS **MUST** HAVE PROPERLY	     ** //
// ** CONFIGURED OpenPGP-COMPLIANT PUBLIC-KEY IN THE SYSTEM's	     ** //
// ** KEYRING, SO IT IS NOT THEN WISE/POSSIBLE TO ALLOW ANONYMOUS    ** //
// ** SUBSCRIPTIONS TO NEWSLETTERS ETC, OR USE NOT FULLY PGP/MIME    ** //
// ** READY ACCOUNTS IN SUCH SYSTEM.                                 ** //
// **                                                                ** //
// ** IT IS ASSUMED, THAT IF AND WHEN YOU TURN SUCH PGP/MIME ON      ** //
// ** YOU ARE FULLY AWARE OF THE REQUIREMENTS AND CONSEQUENCES.      ** //
// **                                                                ** //
if ($prefs['openpgp_gpg_pgpmimemail'] == 'y') {
	// hollmeer 2012-11-03:
	// TURNED ON openPGP support from a lib based class
	require_once( 'lib/openpgp/openpgplib.php' );
}
// **                                                                ** //
// ******************************************************************** //
//////////////////////////////////////////////////////////////////////////

if( $prefs['feature_hidden_links'] == 'y' && isset($jitRequest['wysiwyg']) && $jitRequest['wysiwyg'] != 'y') {
	$headerlib->add_js("$('body').find('h1, h2, h3, h4, h5, h6').each(function() {
	var headerid = $(this).attr('id');
		if(headerid != undefined) {
			$(this).append('<a class=\"tiki_anchor\" href=\"#'+headerid+'\"></a>');
		}
	});");
}

// any furher $headerlib->add_js() call not using rank = 'external' will be put into rank 'late'
// this should separate the overall JS from page specific JS
$headerlib->forceJsRankLate();

if ( $prefs['conditions_enabled'] == 'y' ) {
	if (! Services_User_ConditionsController::hasRequiredAge($user)) {
		$servicelib = TikiLib::lib('service');
		$broker = $servicelib->getBroker();
		$broker->process('user_conditions', 'age_validation', $jitRequest);
		exit;
	}
	if (Services_User_ConditionsController::requiresApproval($user)) {
		$servicelib = TikiLib::lib('service');
		$broker = $servicelib->getBroker();
		$broker->process('user_conditions', 'approval', $jitRequest);
		exit;
	}
}
