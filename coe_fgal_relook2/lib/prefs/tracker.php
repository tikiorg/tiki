<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_tracker_list() {
	return array(
		'tracker_field_computed' => array(
			'name' => tra('Tracker computed field'),
			'description' => tra('Allow execution of computed fields. Consider using webservices or javascript to perform the task instead of using this type.'),
			'warning' => tra('This feature is still in place for backwards compatibility. While there are no flaws associated to it, it could be used as a vector for attacks causing a lot of damage. Webservice field or custom javascript is recommended instead of this field.'),
			'type' => 'flag',
			'default' => 'n',
		),
	);
}
