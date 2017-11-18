<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 * Class Table_Settings_TikiAdminGroupsMembers
 *
 * Tablesorter settings for the table listing members of a group at tiki-admingroups.php
 *
 * @package Tiki
 * @subpackage Table
 * @uses Table_Settings_Standard
 */
class Table_Settings_TikiAdminGroupsMembers extends Table_Settings_Standard
{
	protected $ts = [
		'ajax' => [
			'url' => ['file' => 'tiki-admingroups.php'],
			'sortparam' => 'sort_mode_member',
			'offset' => 'membersOffset',
			'numrows' => 'membersMax',
			'requiredparams' => ['group']
		],
		'filters' => [
			'type' => false,
		],
		'columns' => [
			'#checkbox' => [
				'sort' => [
					'type' => false
				],
				'filter' => [
					'type' => false
				],
				'resizable' => false,
				'priority' => 'critical'
			],
			'#user' => [
				'sort' => [
					'type' => 'text',
					'dir' => 'asc',
					'ajax' => 'login'
				],
				'filter' => [
					'type' => false
				],
				'priority' => 'critical'
			],
			'#assigned' => [
				'sort' => [
					'type' => 'isoDate',
					'ajax' => 'created'
				],
				'filter' => [
					'type' => false
				],
				'priority' => 5
			],
			'#expires' => [
				'sort' => [
					'type' => 'isoDate',
					'ajax' => 'expire'
				],
				'filter' => [
					'type' => false
				],
				'priority' => 6
			],
			'#actions' => [
				'sort' => [
					'type' => false
				],
				'filter' => [
					'type' => false
				],
				'priority' => 1
			]
		]
	];
}
