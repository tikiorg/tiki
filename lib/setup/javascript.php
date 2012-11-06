<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER['SCRIPT_NAME'], basename(__FILE__));

// Javascript auto-detection
//   (to be able to generate non-javascript code if there is no javascript, when noscript tag is not useful enough)
//   It uses cookies instead of session vars to keep the correct value after a session timeout

$js_cookie = getCookie('javascript_enabled');

if ($prefs['disableJavascript'] == 'y' ) {
	$prefs['javascript_enabled'] = 'n';
} elseif ($js_cookie) {
	// Update the pref with the cookie value
	$prefs['javascript_enabled'] = $js_cookie;
} else {
	// Set the cookie to 'n', through PHP / HTTP headers
	$prefs['javascript_enabled'] = 'n';
}

if ( $prefs['javascript_enabled'] != 'y' && $prefs['disableJavascript'] != 'y' ) {
	// Set the cookie to 'y', through javascript (will override the above cookie set to 'n' and sent by PHP / HTTP headers) - duration: approx. 1 year
	$headerlib->add_js("setCookie('javascript_enabled', 'y');", 0);

	// the first and second time, we should not trust the absence of javascript_enabled cookie yet, as it could be a redirection and the js will not get a chance to run yet, so we wait until the third run, assuming that js is on before then
	$runs_before_js_detect = getCookie('runs_before_js_detect');
	if ( $runs_before_js_detect === null ) {
		$prefs['javascript_enabled'] = 'y';
		setCookieSection('runs_before_js_detect', '1', null, $tikilib->now + 365 * 24 * 3600);
	} elseif ( $runs_before_js_detect > 0 ) {
		$prefs['javascript_enabled'] = 'y';
		setCookieSection('runs_before_js_detect', $runs_before_js_detect - 1, null, $tikilib->now + 365 * 24 * 3600);
	}
}

if ($prefs['javascript_enabled'] == 'n') {
	// disable js dependant features
	$prefs['feature_tabs'] = 'n';
	$prefs['feature_jquery'] = 'n';
	$prefs['feature_shadowbox'] = 'n';
	$prefs['feature_wysiwyg'] = 'n';
	$prefs['feature_ajax'] = 'n';
	$prefs['calendar_fullcalendar'] = 'n';
}

if ($prefs['javascript_enabled'] == 'y') {	// we have JavaScript

	$prefs['feature_jquery'] = 'y';	// just in case

	// load translations lang object from /lang/xx/language.js if there
	if (file_exists('lang/' . $prefs['language'] . '/language.js')) {
		// after the usual lib includes (up to 10) but before custom.js (50)
		$headerlib
			->add_jsfile('lang/' . $prefs['language'] . '/language.js', 25)
			->add_js("$.lang = '" . $prefs['language'] . "';");
	}

	/** Use custom.js in styles or options dir if there **/
	$custom_js = $tikilib->get_style_path($prefs['style'], $prefs['style_option'], 'custom.js');
	if (!empty($custom_js)) {
		$headerlib->add_jsfile($custom_js, 50);
	} else {															// there's no custom.js in the current style or option
		$custom_js = $tikilib->get_style_path('', '', 'custom.js');		// so use one in the root of /styles if there
		if (!empty($custom_js)) {
			$headerlib->add_jsfile($custom_js, 50);
		}
	}

	// setup timezone array
	$tz = array_keys(DateTimeZone::listAbbreviations());
	$headerlib->add_js(
		'
function inArray(item, array) {
    for (var i in array) {
        if (array[i] === item) {
            return i;
        }
    }
    return false;
}
var allTimeZoneCodes = ' . json_encode(array_map("strtoupper", $tz)) . ';
var now = new Date();
var now_string = now.toString();
var m = now_string.match(/[ \(]([A-Z]{3,6})[ \)]?[ \d]*$/);	// try three or more char tz first at the end or just before the year
if (!m) {
	m = now_string.match(/[ \(]([A-Z]{1,6})[ \)]?[ \d]*$/);	// might be a "military" one if not
}
if (m) {
	m = m[1];
} else {	// IE (sometimes) gives UTC +offset instead of the abbreviation
	// sadly this workaround will fail for non-whole hour offsets
	var hours = - now.getTimezoneOffset() / 60;
	m = "GMT" + (hours > 0 ? "+" : "") + hours;
}
// Etc/GMT+ is equivalent to GMT-
if (m.substring(0,4) == "GMT+") {
	m = "Etc/GMT-" + m.substring(4);
}
if (m.substring(0,4) == "GMT-") {
	m = "Etc/GMT+" + m.substring(4);
}
if (inArray(m, allTimeZoneCodes)) {
	setCookie("local_tz", m);
}
', 2
);

	$js = '
// JS Object to hold prefs for jq
var jqueryTiki = new Object();
jqueryTiki.ui = '.($prefs['feature_jquery_ui'] == 'y' ? 'true' : 'false') . ';
jqueryTiki.ui_theme = "'.$prefs['feature_jquery_ui_theme'].'";
jqueryTiki.tooltips = '.($prefs['feature_jquery_tooltips'] == 'y' ? 'true' : 'false') . ';
jqueryTiki.autocomplete = '.($prefs['feature_jquery_autocomplete'] == 'y' ? 'true' : 'false') . ';
jqueryTiki.superfish = '.($prefs['feature_jquery_superfish'] == 'y' ? 'true' : 'false') . ';
jqueryTiki.replection = '.($prefs['feature_jquery_reflection'] == 'y' ? 'true' : 'false') . ';
jqueryTiki.tablesorter = '.($prefs['feature_jquery_tablesorter'] == 'y' ? 'true' : 'false') . ';
jqueryTiki.colorbox = '.($prefs['feature_shadowbox'] == 'y' ? 'true' : 'false') . ';
jqueryTiki.cboxCurrent = "{current} / {total}";
jqueryTiki.sheet = '.($prefs['feature_sheet'] == 'y' ? 'true' : 'false') . ';
jqueryTiki.carousel = '.($prefs['feature_jquery_carousel'] == 'y' ? 'true' : 'false') . ';
jqueryTiki.validate = '.($prefs['feature_jquery_validation'] == 'y' ? 'true' : 'false') . ';

jqueryTiki.effect = "'.$prefs['jquery_effect'].'";				// Default effect
jqueryTiki.effect_direction = "'.$prefs['jquery_effect_direction'].'";	// "horizontal" | "vertical" etc
jqueryTiki.effect_speed = '.($prefs['jquery_effect_speed'] == 'normal' ? '400' : '"'.$prefs['jquery_effect_speed'].'"').';	// "slow" | "normal" | "fast" | milliseconds (int) ]
jqueryTiki.effect_tabs = "'.$prefs['jquery_effect_tabs'].'";		// Different effect for tabs
jqueryTiki.effect_tabs_direction = "'.$prefs['jquery_effect_tabs_direction'].'";
jqueryTiki.effect_tabs_speed = '.($prefs['jquery_effect_tabs_speed'] == 'normal' ? '400' : '"'.$prefs['jquery_effect_tabs_speed'].'"').';
jqueryTiki.home_file_gallery = "'.$prefs['home_file_gallery'].'";

jqueryTiki.autosave = '.($prefs['ajax_autosave'] == 'y' ? 'true' : 'false') . ';
jqueryTiki.sefurl = '.($prefs['feature_sefurl'] == 'y' ? 'true' : 'false') . ';
jqueryTiki.ajax = '.($prefs['feature_ajax'] == 'y' ? 'true' : 'false') . ';
jqueryTiki.syntaxHighlighter = '.($prefs['feature_syntax_highlighter'] == 'y' ? 'true' : 'false') . ';
jqueryTiki.selectmenu = '.($prefs['jquery_ui_selectmenu'] == 'y' ? 'true' : 'false') . ';
jqueryTiki.selectmenuAll = '.($prefs['jquery_ui_selectmenu_all'] == 'y' ? 'true' : 'false') . ';
jqueryTiki.mapTileSets = ' . json_encode($tikilib->get_preference('geo_tilesets', array('openstreetmap'), true)) . ';
jqueryTiki.infoboxTypes = ' . json_encode(Services_Object_Controller::supported()) . ';
jqueryTiki.googleStreetView = '.($prefs['geo_google_streetview'] == 'y' ? 'true' : 'false') . ';
jqueryTiki.googleStreetViewOverlay = '.($prefs['geo_google_streetview_overlay'] == 'y' ? 'true' : 'false') . ';
jqueryTiki.structurePageRepeat = '.($prefs['page_n_times_in_a_structure'] == 'y' ? 'true' : 'false') . ';
jqueryTiki.mobile = '.($prefs['mobile_mode'] == 'y' ? 'true' : 'false') . ';
jqueryTiki.jcapture = '.($prefs['feature_jcapture'] == 'y' ? 'true' : 'false') . ';
//do not fix following line for notices -  appropriately throws off a notice when user has not
//flushed prefs cache after upgrading - i.e. not running the updater
jqueryTiki.jcaptureFgal = ' . ((int)$prefs['fgal_for_jcapture']) . ';
jqueryTiki.no_cookie = false;
';	// NB replace "normal" speeds with int to workaround issue with jQuery 1.4.2

	if ($prefs['mobile_feature'] === 'y' && $prefs['mobile_mode'] === 'y') {
		$js .= '
// overrides for prefs for jq in mobile mode
jqueryTiki.ui = false;
jqueryTiki.ui_theme = "";
jqueryTiki.tooltips = false;
jqueryTiki.autocomplete = false;
jqueryTiki.superfish = false;
jqueryTiki.colorbox = false;
jqueryTiki.tablesorter = false;
jqueryTiki.useInlineComment = false;	// Inline comments do not seem to work, and an option is missing. Disable for now.
';
		if ($prefs['feature_ajax'] !== 'y') {
			$headerlib->add_js_config('var mobile_ajaxEnabled = false;');
		}
	}

	if ($prefs['feature_calendar'] === 'y') {
		global $calendarlib; include_once('lib/calendar/calendarlib.php');
		$firstDayofWeek = $calendarlib->firstDayofWeek();
		$js .= "jqueryTiki.firstDayofWeek = $firstDayofWeek;\n";
	}

	if ($prefs['feature_syntax_highlighter'] !== 'y') {
		// add a dummy syntaxHighlighter object as it seems to be used all over the place without checking for the feature
		$js .= '
var syntaxHighlighter = {
	ready: function(textarea, settings) { return null; },
	sync: function(textarea) { return null; },
	add: function(editor, $input, none, skipResize) { return null; },
	remove: function($input) { return null; },
	get: function($input) { return null; },
	fullscreen: function(textarea) { return null; },
	find: function(textareaEditor, val) { return null; },
	searchCursor: [],
	replace: function(textareaEditor, val, replaceVal) { return null; },
	insertAt: function(textareaEditor, replaceString, perLine, blockLevel) { return null; }
};
';
	}

	$headerlib->add_js($js);

	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6') !== false) {

		$smarty->assign('ie6', true);

		if ($prefs['feature_iepngfix'] == 'y') {
			/**
			 * \brief another attempt for PNG alpha transparency fix which seems to work best for IE6 and can be applied even on background positioned images
			 *
			 * is applied explicitly on defined CSS selectors or HTMLDomElement
			 *
			 */
			if (($fixoncss = $prefs['iepngfix_selectors']) == '') {
				$fixoncss = '#sitelogo a img';
			}
			if (($fixondom = $prefs['iepngfix_elements']) != '') {
				$fixondom = "DD_belatedPNG.fixPng($fixondom); // list of HTMLDomElements to fix separated by commas (default is none)";
			}
			$scriptpath = 'lib/iepngfix/DD_belatedPNG-min.js';
			$headerlib->add_jsfile($scriptpath, 200);
			$headerlib->add_js(
<<<JS
DD_belatedPNG.fix('$fixoncss'); // list of CSS selectors to fix separated by commas (default is set to fix sitelogo)
$fixondom
JS
			);
		}
	}
}

if ($prefs['feature_ajax'] != 'y') {
	$prefs['ajax_autosave'] = 'n';
}
