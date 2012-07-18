<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_tracker_list()
{
	return array(
		'tracker_remote_sync' => array(
			'name' => tr('Synchronize Remote Tracker'),
			'description' => tr('Allows to clone a tracker on a remote host and synchronize the data locally on demand.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'tracker_refresh_itemlink_detail' => array(
			'name' => tr('Refresh item link items when master is modified'),
			'description' => tr('To be used when item link is used in trackers so that the index remains in good shape when data on the master that is indexed with the detail is modified and used to search on.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'tracker_clone_item' => array(
			'name' => tr('Clone tracker items'),
			'description' => tr('Allow copying tracker item information into new tracker item.'),
			'type' => 'flag',
			'default' => 'n',
		),
	);
}
