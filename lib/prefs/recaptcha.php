<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_recaptcha_list() {
    return array (
		'recaptcha_enabled' => array(
            'name' => tra('Use ReCaptcha'),
            'description' => tra('Use ReCaptcha instead of default CAPTCHA'),
			'help' => 'Spam+protection',
			'type' => 'flag',
        ),
		'recaptcha_pubkey' => array(
			'name' => tra('Pubkey'),
            'type' => 'text',
            'description' => tra('Go to google.com/recaptcha to generate your keys.'),
        	'size' => 40,
		),
		'recaptcha_privkey' => array(
			'name' => tra('Privkey'),
			'type' => 'text',
            'description' => tra('Go to google.com/recaptcha to generate your keys.'),
			'size' => 40,
		),
	);
}
