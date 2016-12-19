<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_flaggedrev_list()
{
	return array(
		'flaggedrev_approval' => array(
			'name' => tra('Revision Approval'),
			'description' => tra('Uses flagged revisions to hide unapproved wiki page revisions from users without necessary privileges.'),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'n',
			'help' => 'Flagged Revisions',
		),
		'flaggedrev_approval_categories' => array(
			'name' => tra('Revision Approval Categories'),
			'description' => tra('List of category IDs for which revision approval is required.'),
			'type' => 'text',
			'filter' => 'int',
			'separator' => ';',
			'profile_reference' => 'category',
			'dependencies' => array(
				'feature_categories',
			),
			'default' => array(''), //empty string needed to keep preference from setting unexpectedly
		),
	);
}

