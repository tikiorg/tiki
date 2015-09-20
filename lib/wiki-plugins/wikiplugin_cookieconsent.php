<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_cookieconsent_info()
{
	global $prefs;
	return array(
		'name' => tra('Cookie Consent'),
		'documentation' => 'PluginCookieConsent',
		'description' => tra('Display content based on whether cookie consent has been granted by the user'),
		'prefs' => array('wikiplugin_cookieconsent', 'cookie_consent_feature'),
		'body' => tra('Wiki syntax containing the content that can be hidden or shown.'),
		'filter' => 'wikicontent',
		'introduced' => 10,
		'iconname' => 'information',
		'params' => array(
			'no_consent_message' => array(
				'required' => false,
				'name' => tra('No Cookie Message'),
				'description' => tra('Message displayed if user has not consented to accepting cookies.'),
				'since' => '10.0',
				'default' => tra($prefs['cookie_consent_alert']),
				'filter' => 'text',
			),
			'element' => array(
				'required' => false,
				'name' => tra('Containing Element'),
				'description' => tr('DOM element to contain everything (DIV, SPAN etc). Default to %0,
					set to %1 for no container.', '<code>div</code>', '<code>none</code>'),
				'since' => '10.0',
				'default' => 'div',
				'filter' => 'word',
			),
			'element_class' => array(
				'required' => false,
				'name' => tra('Element CSS Class'),
				'description' => tra('CSS class for above.'),
				'since' => '10.0',
				'default' => '',
				'filter' => 'text',
			),
			'no_consent_class' => array(
				'required' => false,
				'name' => tra('No Consent CSS Class'),
				'description' => tr('CSS class for no consent message. Default: %0', '<code>wp-cookie-consent-required</code>'),
				'since' => '11.1',
				'default' => 'wp-cookie-consent-required',
				'filter' => 'text',
			),
		)
	);
}

function wikiplugin_cookieconsent( $body, $params )
{
	global $prefs, $feature_no_cookie;

	if ($prefs['cookie_consent_feature'] !== 'y') {
		return $body;
	}

	//set defaults
	$plugininfo = wikiplugin_cookieconsent_info();
	$defaults = array();
	foreach ($plugininfo['params'] as $key => $param) {
		$defaults[$key] = $param['default'];
	}
	$params = array_merge($defaults, $params);

	$class = $params['element_class'];

	if ($feature_no_cookie) {
		$body = $params['no_consent_message'];
		$class .= ($class ? ' ' : '') . $params['no_consent_class'];
	}

	$tag1 = $tag2 = '';
	if ($params['element'] && $params['element'] !== 'none') {
		if ($class) {
			$class = " class=\"{$class}\"";
		}
		$tag1 = "<{$params['element']}$class>";
		$tag2 = "</{$params['element']}>";
	}

	return $tag1 . $body . $tag2;
}
