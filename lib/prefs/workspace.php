<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_workspace_list()
{
	return array(
		'workspace_ui' => array(
			'name' => tr('Workspace UI'),
			'description' => tr('Combines multiple site features to create a workspace experience for workgroups.'),
			'type' => 'flag',
			'default' => 'n',
			'perspective' => false,
			'dependencies' => array(
				'feature_wiki',
				'namespace_enabled',
				'feature_perspective',
				'feature_categories',
			),
		),
		'workspace_root_category' => array(
			'name' => tr('Workspace root category'),
			'description' => tr('ID of the root category containing all workspaces.'),
			'type' => 'text',
			'filter' => 'int',
			'default' => 0,
			'perspective' => false,
			'warning' => tr('This value is automatically managed and should not need to be modified manually.'),
			'profile_reference' => 'category',
		),
	);
}

