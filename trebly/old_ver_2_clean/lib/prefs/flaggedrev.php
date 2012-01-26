<?php

function prefs_flaggedrev_list()
{
	return array(
		'flaggedrev_approval' => array(
			'name' => tra('Revision Approval'),
			'description' => tra('Uses flagged revisions to hide unapproved wiki page revisions from users with lower privileges.'),
			'type' => 'flag',
			'perspective' => false,
		),
		'flaggedrev_approval_categories' => array(
			'name' => tra('Revision Approval Categories'),
			'description' => tra('List of categories on which revision approval is required.'),
			'type' => 'text',
			'filter' => 'int',
			'separator' => ';',
			'dependencies' => array(
				'feature_categories',
			),
		),
	);
}

