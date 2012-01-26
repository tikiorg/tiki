<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

// Javascript auto-detection
//   (to be able to generate non-javascript code if there is no javascript, when noscript tag is not useful enough)
//   It uses cookies instead of session vars to keep the correct value after a session timeout

if ($prefs['disableJavascript'] == 'y' ) {
	$prefs['javascript_enabled'] = 'n';
} elseif ( isset($_COOKIE['javascript_enabled']) ) {
	// Update the pref with the cookie value
	$prefs['javascript_enabled'] = $_COOKIE['javascript_enabled'];
} else {
	// Set the cookie to 'n', through PHP / HTTP headers
	$prefs['javascript_enabled'] = 'n';
}

if ( $prefs['javascript_enabled'] != 'y' && $prefs['disableJavascript'] != 'y' ) {
	// Set the cookie to 'y', through javascript (will override the above cookie set to 'n' and sent by PHP / HTTP headers) - duration: approx. 1 year
	$headerlib->add_js("var jsedate = new Date();\njsedate.setTime(" . ( 1000 * ( $tikilib->now + 365 * 24 * 3600 ) ) . ");\nsetCookieBrowser('javascript_enabled', 'y', null, jsedate);");

	// the first and second time, we should not trust the absence of javascript_enabled cookie yet, as it could be a redirection and the js will not get a chance to run yet, so we wait until the third run, assuming that js is on before then
	if ( !isset($_COOKIE['runs_before_js_detect']) ) {
		$prefs['javascript_enabled'] = 'y';
		setcookie( 'runs_before_js_detect', '2', $tikilib->now + 365 * 24 * 3600 );
	} elseif ( $_COOKIE['runs_before_js_detect'] > 0 ) {
		$prefs['javascript_enabled'] = 'y';
		setcookie( 'runs_before_js_detect', $_COOKIE['runs_before_js_detect'] - 1, $tikilib->now + 365 * 24 * 3600 );
	}
}
if ($prefs['javascript_enabled'] == 'n') {
	// disable js dependant features
	$prefs['feature_tabs'] = 'n';
	$prefs['feature_jquery'] = 'n';
	$prefs['feature_shadowbox'] = 'n';
	$prefs['feature_wysiwyg'] = 'n';
	$prefs['feature_ajax'] = 'n';
}

if ($prefs['javascript_enabled'] == 'y') {	// we have JavaScript

	$prefs['feature_jquery'] = 'y';	// just in case
	
	// load translations lang object from /lang/xx/language.js if there
	if (file_exists('lang/' . $prefs['language'] . '/language.js')) {
		// after the usual lib includes (up to 10) but before custom.js (50)
		$headerlib->add_jsfile('lang/' . $prefs['language'] . '/language.js', 25);
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
jqueryTiki.jqs5 = '.($prefs['feature_jquery_jqs5'] == 'y' ? 'true' : 'false') . ';
jqueryTiki.validate = '.($prefs['feature_jquery_validation'] == 'y' ? 'true' : 'false') . ';

jqueryTiki.effect = "'.$prefs['jquery_effect'].'";				// Default effect
jqueryTiki.effect_direction = "'.$prefs['jquery_effect_direction'].'";	// "horizontal" | "vertical" etc
jqueryTiki.effect_speed = '.($prefs['jquery_effect_speed'] == 'normal' ? '400' : '"'.$prefs['jquery_effect_speed'].'"').';	// "slow" | "normal" | "fast" | milliseconds (int) ]
jqueryTiki.effect_tabs = "'.$prefs['jquery_effect_tabs'].'";		// Different effect for tabs
jqueryTiki.effect_tabs_direction = "'.$prefs['jquery_effect_tabs_direction'].'";
jqueryTiki.effect_tabs_speed = '.($prefs['jquery_effect_tabs_speed'] == 'normal' ? '400' : '"'.$prefs['jquery_effect_tabs_speed'].'"').';

jqueryTiki.autosave = '.($prefs['ajax_autosave'] == 'y' ? 'true' : 'false') . ';
jqueryTiki.sefurl = '.($prefs['feature_sefurl'] == 'y' ? 'true' : 'false') . ';
jqueryTiki.ajax = '.($prefs['feature_ajax'] == 'y' ? 'true' : 'false') . ';
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
';
		if ($prefs['feature_ajax'] !== 'y') {
			$headerlib->add_js_config('var mobile_ajaxEnabled = false;');
		}
	}
	$headerlib->add_js($js, 100);	
	
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
			$headerlib->add_jsfile ($scriptpath, 200);
			$headerlib->add_js (<<<JS
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
