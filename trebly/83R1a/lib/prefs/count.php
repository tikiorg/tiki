<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: count.php 33845 2011-04-06 21:03:07Z lphuberdeau $

function prefs_count_list() {
	return array(
		'count_admin_pvs' => array(
			'name' => tra('Count admin pageviews'),
			'description' => tra('Include pageviews by the Admin when reporting stats.'),
			'type' => 'flag',
			'default' => 'n',
		),
	);
}

