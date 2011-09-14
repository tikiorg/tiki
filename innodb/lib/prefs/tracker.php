<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_tracker_list() {
	return array(
		'tracker_remote_sync' => array(
			'name' => tr('Synchronize Remote Tracker'),
			'description' => tr('Allows to clone a tracker on a remote host and synchronize the data locally on demand.'),
			'type' => 'flag',
			'default' => 'n',
		),
	);
}
