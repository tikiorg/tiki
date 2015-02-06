<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 * @return array
 */
function module_share_info()
{
	return array(
		'name' => tra('Share'),
		'description' => tra('Links for sharing, reporting etc.'),
		'params' => array(
			'report' => array(
				'name' => tra('Report'),
				'description' => tra('Report to Webmaster') . ' (y/n)',
				'filter' => 'alpha',
			),
			'share' => array(
				'name' => tra('Share'),
				'description' => tra('Share this page') . ' (y/n)',
				'filter' => 'alpha',
			),
			'email' => array(
				'name' => tra('Email'),
				'description' => tra('Email this page') . ' (y/n)',
				'filter' => 'alpha',
			),
			'icons' => array(
				'name' => tra('Icons'),
				'description' => tra('Use icons for report, share and email links') . ' (n/y)',
				'filter' => 'alpha',
			),
			'facebook' => array(
				'name' => tra('Facebook'),
				'description' => tra('Show Facebook "Like" button on the current page') . ' (n/y)',
				'filter' => 'alpha',
			),
			'facebook_href' => array(
				'name' => tra('Facebook: URL'),
				'description' => tra('URL to "Like" (leave blank for current URL)'),
				'filter' => 'url',
			),
			'facebook_send' => array(
				'name' => tra('Facebook: Send'),
				'description' => tra('Show Facebook "Send" button') . ' (y/n)',
				'filter' => 'alpha',
			),
			'facebook_layout' => array(
				'name' => tra('Facebook: Layout'),
				'description' => tra('Size, layout and amount of social context') . ' (standard/button_count/box_count)',
				'filter' => 'text',
			),
			'facebook_width' => array(
				'name' => tra('Facebook: Width'),
				'description' => tra('Width in pixels') . ' (450)',
				'filter' => 'digits',
			),
			'facebook_height' => array(
				'name' => tra('Facebook: Height'),
				'description' => tra('Container height in CSS units (e.g. "120px" or "2em")'),
				'filter' => 'text',
			),
			'facebook_show_faces' => array(
				'name' => tra('Facebook: Show Faces'),
				'description' => tra('Show pictures of others who like this') . ' (y/n)',
				'filter' => 'alpha',
			),
			'facebook_verb' => array(
				'name' => tra('Facebook: Verb'),
				'description' => tra('Verb to display in button') . ' (like/recommend)',
				'filter' => 'word',
			),
			'facebook_colorscheme' => array(
				'name' => tra('Facebook: Colors'),
				'description' => tra('Color scheme') . ' (light/dark)',
				'filter' => 'word',
			),
			'facebook_font' => array(
				'name' => tra('Facebook: Font'),
				'description' => tra('Font to display') . ' (lucida grande/arial/segoe ui/tahoma/trebuchet ms/verdana)',
				'filter' => 'text',
			),
			'facebook_locale' => array(
				'name' => tra('Facebook: Locale'),
				'description' => tra('Locale in the format ll_CC (default "en_US")'),
				'filter' => 'text',
			),
			'facebook_ref' => array(
				'name' => tra('Facebook: Referrals'),
				'description' => tra('Label for tracking referrals (optional)'),
				'filter' => 'text',
			),
			'facebook_appId' => array(
				'name' => tra('Facebook: App Id'),
				'description' => tra('ID of your Facebook app (optional)'),
				'filter' => 'digits',
			),
			'twitter' => array(
				'name' => tra('Twitter'),
				'description' => tra('Show Twitter Follow Button on the current page') . ' (n/y)',
				'filter' => 'alpha',
			),
			'twitter_username' => array(
				'name' => tra('Twitter: User Name'),
				'description' => tra('Twitter user name to quote as "via"'),
				'filter' => 'text',
			),
			'twitter_label' => array(
				'name' => tra('Twitter: Label'),
				'description' => tra('Text to display. Default "Tweet"'),
				'filter' => 'text',
			),
			'twitter_url' => array(
				'name' => tra('Twitter: URL'),
				'description' => tra('URL to "Tweet" (leave blank for current URL)'),
				'filter' => 'url',
			),
			'twitter_show_count' => array(
				'name' => tra('Twitter: Show Count'),
				'description' => tra('Position of Tweet count') . ' (horizontal/vertical/none)',
				'filter' => 'alpha',
			),
			'twitter_language' => array(
				'name' => tra('Twitter: Language'),
				'description' => tra('Two letter language code') . ' (en/de/es/fr/id/it/ko/ja/nl/pt/re/tr)',
				'filter' => 'word',
			),
			'twitter_width' => array(
				'name' => tra('Twitter: Width'),
				'description' => tra('Width in pixels or percentage (e.g. 300px)'),
				'filter' => 'text',
			),
			'twitter_height' => array(
				'name' => tra('Twitter: Height'),
				'description' => tra('Container height in CSS units (e.g. "120px" or "2em")'),
				'filter' => 'text',
			),
			'twitter_text' => array(
				'name' => tra('Twitter: Text'),
				'description' => tra('Tweet text (leave empty to use page title'),
				'filter' => 'word',
			),
			'linkedin' => array(
				'name' => tra('LinkedIn'),
				'description' => tra('Linked in share button') . ' (n/y)',
				'filter' => 'alpha',
			),
			'linkedin_url' => array(
				'name' => tra('LinkedIn: URL'),
				'description' => tra('URL to share (leave blank for current URL)'),
				'filter' => 'url',
			),
			'linkedin_mode' => array(
				'name' => tra('LinkedIn: Count Mode'),
				'description' => tra('Position of count') . ' (none/top/right)',
				'filter' => 'word',
			),
			'google' => array(
				'name' => tra('Google +1'),
				'description' => tra('Google +1 button') . ' (n/y)',
				'filter' => 'alpha',
			),
			'google_size' => array(
				'name' => tra('Google: Size'),
				'description' => tra('Google button size') . ' (standard|small|medium|tall)',
				'filter' => 'word',
			),
			'google_annotation' => array(
				'name' => tra('Google: Annotation'),
				'description' => tra('Google annotation') . ' (bubble|inline|none)',
				'filter' => 'word',
			),
			'google_language' => array(
				'name' => tra('Google: Language'),
				'description' => tra('Google language') . ' (en-US|fr|ca|de|en-UK|...)',
				'filter' => 'text',
			),
			'google_href' => array(
				'name' => tra('Google: URL'),
				'description' => tra('URL to share (leave blank for current URL)'),
				'filter' => 'url',
			),
		),
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_share($mod_reference, $module_params)
{
	static $share_mod_usage_counter = 0;
	$smarty = TikiLib::lib('smarty');
	$smarty->assign('share_mod_usage_counter', ++$share_mod_usage_counter);

	$smarty->assign('share_icons', !empty($module_params['icons']) && $module_params['icons'] === 'y');

	// facebook like
	
	$fbData = '';
	$fbDivAttr = '';

	if (!empty($module_params['facebook_height'])) {
		$fbDivAttr .= ' height:' . $module_params['facebook_height'] . ';';
	}
	if (empty($module_params['facebook_send']) || $module_params['facebook_send'] === 'y') {
		$fbData .= ' data-send="true"';
	} else {
		$fbData .= ' data-send="false"';
	}
	if (!empty($module_params['facebook_layout']) && $module_params['facebook_layout'] !== 'standard') {
		$fbData .= ' data-layout="' . $module_params['facebook_layout'] . '"';
	}
	if (!empty($module_params['facebook_width'])) {
		$fbData .= ' data-width="' . $module_params['facebook_width'] . '"';
		$fbDivAttr .= ' width:' . $module_params['facebook_width'] . 'px;';
	}
	if (empty($module_params['facebook_show_faces']) || $module_params['facebook_layout'] === 'y') {
		$fbData .= ' data-show-faces="true"';
	} else {
		$fbData .= ' data-show-faces="false"';
	}
	if (!empty($module_params['facebook_verb']) && $module_params['facebook_verb'] !== 'like') {
		$fbData .= ' data-action="recommend"';
	}
	if (!empty($module_params['facebook_colorscheme']) && $module_params['facebook_colorscheme'] !== 'light') {
		$fbData .= ' data-colorscheme="dark"';
	}
	if (!empty($module_params['facebook_font']) && $module_params['facebook_font'] !== 'lucida grande') {
		$fbData .= ' data-font="' . $module_params['facebook_font'] . '"';
	}
	if (!empty($module_params['facebook_ref'])) {
		$fbData .= ' data-ref="' . htmlspecialchars($module_params['facebook_ref']) . '"';
	}
	if (!empty($module_params['facebook_href'])) {
		$fbData .= ' data-href="' . $module_params['facebook_href'] . '"';
	}
	$smarty->assign('fb_data_attributes', $fbData);
	
	if (!empty($module_params['facebook_appId'])) {
		$smarty->assign('fb_app_id_param', '&appId=' . $module_params['facebook_appId']);
	} else {
		$smarty->assign('fb_app_id_param', '');
	};
	if (!empty($fbDivAttr)) {
		$fbDivAttr = ' style="' . $fbDivAttr . '"';
	}
	$smarty->assign('fb_div_attributes', $fbDivAttr);

	// TODO find a way of matching up tiki lang with https://www.facebook.com/translations/FacebookLocales.xml

	if (!empty($module_params['facebook_locale'])) {
		$smarty->assign('fb_locale', $module_params['facebook_locale']);
	} else {
		$smarty->assign('fb_locale', 'en_US');
	};

	// twitter button

	$twData = '';
	$twDivAttr = '';

	if (!empty($module_params['twitter_height'])) {
		$twDivAttr .= ' height:' . $module_params['twitter_height'] . ';';
	}
	if (!empty($module_params['twitter_width'])) {
		$twDivAttr .= ' width:' . $module_params['twitter_width'] . ';';
	}
	if (empty($module_params['twitter_show_count']) || $module_params['twitter_show_count'] === 'horizontal') {
		$twData .= ' data-count="horizontal"';
	} else {
		$twData .= ' data-count="' . $module_params['twitter_show_count'] . '"';
	}
	if (!empty($module_params['twitter_username'])) {
		$twData .= ' data-via="' . $module_params['twitter_username'] . '"';
	}
	if (!empty($module_params['twitter_language'])) {
		$twData .= ' data-lang="' . $module_params['twitter_language'] . '"';
	}
	if (!empty($module_params['twitter_text'])) {
		$twData .= ' data-text="' . htmlspecialchars($module_params['twitter_text']) . '"';
	}
	if (!empty($module_params['twitter_url'])) {
		$twData .= ' data-url="' . $module_params['twitter_url'] . '"';
	}

	$smarty->assign('tw_data_attributes', $twData);

	if (!empty($twDivAttr)) {
		$twDivAttr = 'style="' . $twDivAttr . '"';
	}
	$smarty->assign('tw_div_attributes', $twDivAttr);

	// linkedin

	$liData = '';
	if (!empty($module_params['linkedin_url'])) {
		$liData .= ' data-url="' . $module_params['linkedin_url'] . '"';
	}
	if (!empty($module_params['linkedin_mode'])) {
		$liData .= ' data-counter="' . $module_params['linkedin_mode'] . '"';
	}
	$smarty->assign('li_data_attributes', $liData);

	// linkedin

	$glData = '';
	if (!empty($module_params['google_size']) && $module_params['google_size'] !== 'standard') {
		$glData .= ' data-size="' . $module_params['google_size'] . '"';
	}
	if (!empty($module_params['google_annotation']) && $module_params['google_annotation'] !== 'bubble') {
		$glData .= ' data-annotation="' . $module_params['google_annotation'] . '"';
	}
	if (!empty($module_params['google_href'])) {
		$glData .= ' data-href="' . $module_params['google_href'] . '"';
	}
	$smarty->assign('gl_data_attributes', $glData);

	if (!empty($module_params['google_language']) && $module_params['google_language'] !== 'en-US') {
		$smarty->assign('gl_script_addition', "  window.___gcfg = {lang: '{$module_params['google_language']}'};\n");
	}
}
