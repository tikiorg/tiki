<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
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
		'description' => tra('Only displays the body markup if cookie consent has been granted by the user.'),
		'prefs' => array('wikiplugin_cookieconsent', 'cookie_consent_feature'),
		'body' => tra('Wiki syntax containing the content that can be hidden or shown.'),
		'filter' => 'wikicontent',
		'icon' => 'img/icons/question.gif',
		'params' => array(
			'no_consent_message' => array(
				'required' => false,
				'name' => tra('No Cookie Message'),
				'description' => tra('Message displayed if user has not consented to accepting cookies.'),
				'default' => tra($prefs['cookie_consent_alert']),
			),
			'element' => array(
				'required' => false,
				'name' => tra('Containing Element'),
				'description' => tra('DOM element to contain everything (DIV, SPAN etc). Default to empty for no container.'),
				'default' => '',
			),
			'element_class' => array(
				'required' => false,
				'name' => tra('Element CSS Class'),
				'description' => tra('CSS class for above.'),
				'default' => '',
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

	if ($feature_no_cookie) {
		$body = $params['no_consent_message'];
	}

	$tag1 = $tag2 = '';
	if ($params['element']) {
		if ($params['element_class']) {
			$tag1 = "<{$params['element']} class=\"{$params['element_class']}\"}>";
		} else {
			$tag1 = "<{$params['element']}>";
		}
		$tag2 = "<{$params['element']}>";
	}

	return $tag1 . $body . $tag2;
}
