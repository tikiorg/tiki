<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER['SCRIPT_NAME'], basename(__FILE__));

if ( !isset($_REQUEST['mobile_mode']) || $_REQUEST['mobile_mode'] === 'y' ) {

	require_once 'lib/mobileesp/mdetect.php';

	$uagent_info = new uagent_info();

	$supported_device = $uagent_info->DetectIphoneOrIpod() ||
						$uagent_info->DetectIpad() ||
						$uagent_info->DetectAndroid() ||
						$uagent_info->DetectBlackBerry() ||
						$uagent_info->DetectOperaMobile() ||
						$uagent_info->DetectPalmWebOS();

	if ((!isset($_COOKIE['mobile_mode']) && $supported_device) || $_COOKIE['mobile_mode'] === 'y') {		// supported by jquery.mobile

		//die(var_dump($prefs['mobile_mode'], $_REQUEST['mobile_mode'], $_COOKIE['mobile_mode']));
		$prefs['mobile_mode'] = 'y';

		// hard-wire a few incompatible prefs shut to speed development
		$prefs['feature_jquery_ui'] = 'n';
		$prefs['feature_jquery_reflection'] = 'n';
		$prefs['feature_fullscreen'] = 'n';
		$prefs['feature_syntax_highlighter'] = 'n';
		$prefs['feature_layoutshadows'] = 'n';
		$prefs['feature_sefurl'] = 'n';
		$prefs['feature_wysiwyg'] = 'n';
		$prefs['themegenerator_feature'] = 'n';
		$prefs['ajax_autosave'] = 'n';
		$prefs['change_theme'] = 'n';
		$prefs['feature_syntax_highlighter'] = 'n';
		$prefs['feature_shadowbox'] = 'n';
		$prefs['jquery_ui_selectmenu'] = 'n';

		$headerlib->add_js('function sfHover() {alert("not working?");}', 100);	// try and override the css menu func

		// a few requirements
		$prefs['feature_html_head_base_tag'] = 'y';
		$prefs['style'] = 'mobile.css'; // set in perspectives but seems to need a nudge here

		if (!is_array($prefs['mobile_perspectives'])) {
			$prefs['mobile_perspectives'] = unserialize($prefs['mobile_perspectives']);
		}
		if (count($prefs['mobile_perspectives']) > 0) {
			global $perspectivelib; require_once 'lib/perspectivelib.php';

			if (!in_array($perspectivelib->get_current_perspective($prefs), $prefs['mobile_perspectives'])) {
				$_SESSION['current_perspective'] = $prefs['mobile_perspectives'][0];
			}
		}
	} else {
		$prefs['mobile_mode'] = 'n';
	}
} else {
	$prefs['mobile_mode'] = 'n';
}

setcookie('mobile_mode', $prefs['mobile_mode']);
