<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_quantify_list() {
	return array(
		'quantify_changes' => array(
			'name' => tra('Quantify change size'),
			'description' => tra('In addition to tracking the changes, track the change size and display the approximate up-to-date-ness of the page.'),
			'type' => 'flag',
		),
	);
}
