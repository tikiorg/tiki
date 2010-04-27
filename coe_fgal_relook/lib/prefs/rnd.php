<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_rnd_list() {
	return array(
		'rnd_num_reg' => array(
			'name' => tra('Use CAPTCHA to prevent automatic/robot registrations'),
			'type' => 'flag',
			'help' => 'Spam+Protection',
		),
	);
}
