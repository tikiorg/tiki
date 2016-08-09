<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_cookie_list()
{
	return array(
		'cookie_name' => array(
			'name' => tra('Cookie name'),
            'description' => tra("Name of the cookie to remember the user's login"),
			'type' => 'text',
			'size' => 35,
			'perspective' => false,
			'default' => 'tikiwiki',
		),
		'cookie_domain' => array(
			'name' => tra('Domain'),
            'description' => tra('The domain that the cookie is available to.'),
			'type' => 'text',
			'size' => 35,
			'perspective' => false,
			'default' => '',
		),
		'cookie_path' => array(
			'name' => tra('Path'),
            'description' => tra('The path on the server in which the cookie will be available on. Tiki will detect if it is installed in a subdirectory and will use that automatically.'),
			'hint' => 'N.B. Needs to start with a / character to work properly in Safari',
			'type' => 'text',
			'size' => 35,
			'perspective' => false,
			'default' => isset($GLOBALS['tikiroot']) ? $GLOBALS['tikiroot'] : '' ,
		),
		'cookie_consent_feature' => array(
			'name' => tra('Cookie Consent'),
			'description' => tra('Ask permission of the user before setting any cookies, and comply with the response.'),
			'hint' => tra('Complies with EU Privacy and Electronic Communications Regulations.'),
			'type' => 'flag',
			'help' => 'Cookie+Consent',
			'default' => 'n',
			'tags' => array('experimental'),
		),
		'cookie_consent_name' => array(
			'name' => tra('Cookie consent name'),
			'description' => tra('Name of the cookie to record the user\'s consent if the user agrees.'),
			'type' => 'text',
			'size' => 35,
			'default' => 'tiki_cookies_accepted',
			'tags' => array('experimental'),
		),
		'cookie_consent_expires' => array(
			'name' => tra('Cookie consent expiration'),
			'description' => tra('Expiration date of the cookie to record consent (in days).'),
			'type' => 'text',
			'filter' => 'int',
			'default' => 365,
			'tags' => array('experimental'),
		),
		'cookie_consent_description' => array(
			'name' => tra('Cookie consent text'),
			'description' => tra('Description for the dialog.'),
			'hint' => tra('Wiki-parsed'),
			'type' => 'textarea',
			'size' => 6,
			'default' => tra('This website would like to place cookies on your computer to improve the quality of your experience of the site. To find out more about the cookies, see our ((privacy notice)).'),
			'tags' => array('experimental'),
		),
		'cookie_consent_question' => array(
			'name' => tra('Cookie consent question'),
			'description' => tra('Specific question next to the checkbox for agreement. Leave empty to not display a checkbox.'),
			'hint' => tra('Wiki-parsed'),
			'type' => 'text',
			'size' => 35,
			'default' => tra('I accept cookies from this site.'),
			'tags' => array('experimental'),
		),
		'cookie_consent_button' => array(
			'name' => tra('Cookie consent button'),
			'description' => tra('Label on the agreement button.'),
			'type' => 'text',
			'size' => 35,
			'default' => tra('Continue'),
			'tags' => array('experimental'),
		),
		'cookie_consent_alert' => array(
			'name' => tra('Cookie consent alert'),
			'description' => tra('Alert displayed when user tries to access or use a feature requiring cookies.'),
			'type' => 'text',
			'size' => 35,
			'default' => tra('Sorry, cookie consent required'),
			'tags' => array('experimental'),
		),
		'cookie_consent_mode' => array(
			'name' => tra('Cookie consent mode'),
			'description' => tra('Appearance of consent dialog'),
			'hint' => tra('Dialog style requires feature_jquery_ui'),
			'type' => 'list',
			'options' => array(
				'' => tra('Plain'),
				'banner' => tra('Banner'),
				'dialog' => tra('Dialog'),
			),
			'default' =>'',
			'tags' => array('experimental'),
		),
		'cookie_consent_dom_id' => array(
			'name' => tra('Cookie consent dialog ID'),
			'description' => tra('DOM id for the dialog container div.'),
			'type' => 'text',
			'size' => 35,
			'default' => 'cookie_consent_div',
			'tags' => array('experimental'),
		),
		'cookie_consent_disable' => array(
			'name' => tra('Cookie consent disabled'),
			'description' => tra('Do not give the option to refuse cookies but still inform the user about cookie usage.'),
			'type' => 'flag',
			'default' => 'n',
			'tags' => array('experimental'),
		),
		'cookie_refresh_rememberme' => array(
			'name' => tr('Refresh the remember-me cookie expiration'),
			'description' => tr('Each time a user is logged in with a cookie set in a previous session, the cookie expiration date is updated.'),
			'type' => 'flag',
			'default' => 'n',
			'tags' => array('advanced'),
			'dependencies' => array(
				'rememberme',
			),
		),
	);
}
