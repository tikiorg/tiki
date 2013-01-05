<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_cookie_list()
{
	return array(
		'cookie_name' => array(
			'name' => tra('Cookie name'),
            'description' => tra(''),
			'type' => 'text',
			'size' => 35,
			'perspective' => false,
			'default' => 'tikiwiki',
		),
		'cookie_domain' => array(
			'name' => tra('Domain'),
            'description' => tra(''),
			'type' => 'text',
			'size' => 35,
			'perspective' => false,
			'default' => '',
		),
		'cookie_path' => array(
			'name' => tra('Path'),
            'description' => tra(''),
			'type' => 'text',
			'size' => 35,
			'perspective' => false,
			'default' => $GLOBALS['tikiroot'],
		),
		'cookie_consent_feature' => array(
			'name' => tra('Cookie Consent'),
			'description' => tra('Ask users permission before setting any cookies, and obey their decision.'),
			'hint' => tra('Complies with EU Privacy and Electronic Communications Regulations.'),
			'type' => 'flag',
			'help' => 'Cookie+Consent',
			'default' => 'n',
			'tags' => array('experimental'),
		),
		'cookie_consent_name' => array(
			'name' => tra('Cookie Consent Name'),
			'description' => tra('Name of the cookie to record consent if they agree.'),
			'type' => 'text',
			'size' => 35,
			'default' => 'tiki_cookies_accepted',
		),
		'cookie_consent_expires' => array(
			'name' => tra('Cookie Consent Expiry'),
			'description' => tra('Expiry date for the cookie to record consent (in days).'),
			'type' => 'text',
			'filter' => 'int',
			'default' => 365,
		),
		'cookie_consent_description' => array(
			'name' => tra('Cookie Consent Text'),
			'description' => tra('Description for the dialog.'),
			'hint' => tra('Wiki parsed'),
			'type' => 'textarea',
			'size' => 6,
			'default' => tra('This site would like to place cookies on your computer to help us make it better. To find out more about the cookies, see our ((privacy notice)).'),
		),
		'cookie_consent_question' => array(
			'name' => tra('Cookie Consent Question'),
			'description' => tra('Specific question next to the checkbox for agreement.'),
			'hint' => tra('Wiki parsed'),
			'type' => 'text',
			'size' => 35,
			'default' => tra('I accept cookies from this site.'),
		),
		'cookie_consent_button' => array(
			'name' => tra('Cookie Consent Button'),
			'description' => tra('Label on the agreement button.'),
			'type' => 'text',
			'size' => 35,
			'default' => tra('Continue'),
		),
		'cookie_consent_alert' => array(
			'name' => tra('Cookie Consent Alert'),
			'description' => tra('Alert displayed whn user tries to access a feature requiring cooies.'),
			'type' => 'text',
			'size' => 35,
			'default' => tra('Sorry, cookie consent required'),
		),
		'cookie_consent_mode' => array(
			'name' => tra('Cookie Consent Mode'),
			'description' => tra('Appearance of consent dialog'),
			'hint' => tra('Dialog style requires feature_jquery_ui'),
			'type' => 'list',
			'options' => array(
				'' => tra('Plain'),
				'banner' => tra('Banner'),
				'dialog' => tra('Dialog'),
			),
			'default' =>'',
		),
		'cookie_consent_dom_id' => array(
			'name' => tra('Cookie Consent Dialog Id'),
			'description' => tra('DOM id for the dialog container div.'),
			'type' => 'text',
			'size' => 35,
			'default' => 'cookie_consent_div',
		),
	);
}
