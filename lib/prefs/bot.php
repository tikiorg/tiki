<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_bot_list() {
	return array(
		'bot_logo_code' => array(
			'name' => tra('Custom Site Footer Content'),
			'hint' => tra('Example:') . ' ' . '<div style="text-align: center"><small>Powered by Tikiwiki</small></div>',
			'type' => 'textarea',
			'size' => '6',
		),
	);	
}
