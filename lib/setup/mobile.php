<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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

		if (!is_array($prefs['mobile_perspectives'])) {
			$prefs['mobile_perspectives'] = unserialize($prefs['mobile_perspectives']);
		}
		if (count($prefs['mobile_perspectives']) > 0) {
			$persp = $prefs['mobile_perspectives'][0];

			if (Perms::get( array( 'type' => 'perspective', 'object' => $persp ))->perspective_view) {

				$prefs['mobile_mode'] = 'y';

				// pre-tiki14/bootstrap these prefs were disabled by default
				// they can still be disabled via the mobile perspective if needed
				/*$prefs['feature_jquery_ui'] = 'n';
				$prefs['feature_jquery_reflection'] = 'n';
				$prefs['feature_fullscreen'] = 'n';
				$prefs['feature_syntax_highlighter'] = 'n';
				$prefs['feature_layoutshadows'] = 'n';
				$prefs['feature_wysiwyg'] = 'n';
				$prefs['ajax_autosave'] = 'n';
				$prefs['change_theme'] = 'n';
				$prefs['feature_syntax_highlighter'] = 'n';
				$prefs['jquery_ui_chosen'] = 'n';
				$prefs['fgal_show_explorer'] = 'n';
				$prefs['feature_fixed_width'] = 'n';
				$prefs['fgal_elfinder_feature'] = 'n';
				$prefs['wiki_auto_toc'] = 'n';
				$prefs['feature_smileys'] = 'n';
				$prefs['feature_jcapture'] = 'n';
				$prefs['calendar_fullcalendar'] = 'n';
				$prefs['feature_inline_comments'] = 'n';
				$prefs['feature_jquery_tablesorter'] = 'n';*/

				$headerlib = TikiLib::lib('header');

				if ($prefs['feature_shadowbox'] === 'y') {
					$headerlib
						->add_jsfile_external('vendor/jquery/photoswipe/lib/klass.min.js', true)
						->add_jsfile_external('vendor/jquery/photoswipe/code.photoswipe.jquery-3.0.5.min.js', true)
						->add_jq_onready('var $photosToSwipe = $("a[data-box*=\'box\'][data-box*=\'type=img\'], a[data-box*=\'box\'][data-box!=\'type=\']");
if ($photosToSwipe.length) {$photosToSwipe.photoSwipe();}', 5)
						->add_cssfile('vendor/jquery/photoswipe/photoswipe.css');
				}

				global $base_url;
				$perspectivelib = TikiLib::lib('perspective');
				if (!in_array($perspectivelib->get_current_perspective($prefs), $prefs['mobile_perspectives'])) {	// change perspective

					$hp = $prefs['wikiHomePage'];							// get default non mobile homepage

					$_SESSION['current_perspective'] = $persp;
					$_SESSION['current_perspective_name'] = $perspectivelib->get_perspective_name($_SESSION['current_perspective']);

					if ($prefs['tikiIndex'] === 'tiki-index.php' && isset($_REQUEST['page'])) {

						$pprefs = $perspectivelib->get_preferences($_SESSION['current_perspective']);
						if (in_array('wikiHomePage', array_keys($pprefs))) {				// mobile persp has home page set (often the case)
							if ($hp == $_REQUEST['page']) {
								header('Location: ' . $base_url);							// so redirect to site root and try again
							}
						}
					}
				}
			} else {
				$prefs['mobile_mode'] = 'n';
				if (! $supported_device) {	// send error only if not on a read mobile device
					TikiLib::lib('errorreport')->report(tra('Mobile mode: Permission denied, please log in.'));
				}
			}
		}
	} else {
		$prefs['mobile_mode'] = 'n';
	}
} else {
	$prefs['mobile_mode'] = 'n';
}

if ($prefs['mobile_mode'] === 'y') {
	setCookieSection('mobile_mode', $prefs['mobile_mode']);
}
