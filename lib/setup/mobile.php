<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER['SCRIPT_NAME'], basename(__FILE__));

if ( !isset($_REQUEST['mobile_mode']) || $_REQUEST['mobile_mode'] === 'y' ) {

	require_once 'vendor_extra/mobileesp/mdetect.php';

	$uagent_info = new uagent_info();

	$supported_device = $uagent_info->DetectIphoneOrIpod() ||
						$uagent_info->DetectIpad() ||
						$uagent_info->DetectAndroid() ||
						$uagent_info->DetectBlackBerry() ||
						$uagent_info->DetectOperaMobile() ||
						$uagent_info->DetectPalmWebOS();

	if ((!getCookie('mobile_mode') && $supported_device) || getCookie('mobile_mode') === 'y') {		// supported by jquery.mobile

		//die(var_dump($prefs['mobile_mode'], $_REQUEST['mobile_mode'], $_COOKIE['mobile_mode']));
		$prefs['mobile_mode'] = 'y';

		// hard-wire a few incompatible prefs shut to speed development
		$prefs['feature_jquery_ui'] = 'n';
		$prefs['feature_jquery_reflection'] = 'n';
		$prefs['feature_fullscreen'] = 'n';
		$prefs['feature_syntax_highlighter'] = 'n';
		$prefs['feature_layoutshadows'] = 'n';
		$prefs['feature_wysiwyg'] = 'n';
		$prefs['themegenerator_feature'] = 'n';
		$prefs['ajax_autosave'] = 'n';
		$prefs['change_theme'] = 'n';
		$prefs['feature_syntax_highlighter'] = 'n';
		$prefs['jquery_ui_selectmenu'] = 'n';
		$prefs['fgal_show_explorer'] = 'n';
		$prefs['feature_fixed_width'] = 'n';
		$prefs['fgal_elfinder_feature'] = 'n';

		$headerlib->add_js('function sfHover() {alert("not working?");}', 100);	// try and override the css menu func

		if ($prefs['feature_shadowbox'] === 'y') {
			$headerlib
				->add_jsfile('lib/jquery/code.photoswipe/lib/klass.min.js')
				->add_jsfile("lib/jquery/code.photoswipe/code.photoswipe.jquery-3.0.4.js")
				->add_cssfile('lib/jquery/code.photoswipe/photoswipe.css');
		}

		// a few requirements
		$prefs['feature_html_head_base_tag'] = 'y';
		$prefs['site_style'] = 'mobile.css'; // set in perspectives but seems to need a nudge here
		$prefs['site'] = $prefs['site_style'];

		if (!is_array($prefs['mobile_perspectives'])) {
			$prefs['mobile_perspectives'] = unserialize($prefs['mobile_perspectives']);
		}
		if (count($prefs['mobile_perspectives']) > 0) {
			global $perspectivelib, $base_url; require_once 'lib/perspectivelib.php';
			if (!in_array($perspectivelib->get_current_perspective($prefs), $prefs['mobile_perspectives'])) {	// change perspective

				$hp = $prefs['wikiHomePage'];							// get default non mobile homepage
				$_SESSION['current_perspective'] = $prefs['mobile_perspectives'][0];

				if ($prefs['tikiIndex'] === 'tiki-index.php' && isset($_REQUEST['page'])) {

					$pprefs = $perspectivelib->get_preferences($_SESSION['current_perspective']);
					if (in_array('wikiHomePage', array_keys($pprefs))) {				// mobile persp has home page set (often the case)
						if ($hp == $_REQUEST['page']) {
							header('Location: ' . $base_url);							// so redirect to site root and try again
						}
					}
				}
			}
		}
	} else {
		$prefs['mobile_mode'] = 'n';
	}
} else {
	$prefs['mobile_mode'] = 'n';
}

setCookieSection('mobile_mode', $prefs['mobile_mode']);
