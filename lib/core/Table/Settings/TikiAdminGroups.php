<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
 * Class Table_Settings_TikiAdminGroups
 *
 * Tablesorter settings for the table listing groups at tiki-admingroups.php
 *
 * @package Tiki
 * @subpackage Table
 * @uses Table_Settings_Standard
 */
class Table_Settings_TikiAdminGroups extends Table_Settings_Standard
{
	protected $ts = [
		'ajax' => [
			'url' => [
				'file' => 'tiki-admingroups.php'
			],
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
			'#id' => [
				'sort' => [
					'type' => 'digit',
					'ajax' => 'id'
				],
				'filter' => [
					'type' => false
				],
				'priority' => 5
			],
			'#group' => [
				'sort' => [
					'type' => true,
					'dir' => 'asc',
					'ajax' =>'groupName'
				],
				'filter' => [
					'type' => 'text',
					'ajax' => 'find'
				],
				'priority' => 'critical'
			],
			'#inherits' => [
				'sort' => [
					'type' => false,
				],
				'filter' => [
					'type' => false
				],
				'priority' => 6
			],
			'#home' => [					//only if $prefs.useGroupHome == 'y'
				'sort' => [
					'type' => 'text',
					'ajax' => 'groupHome'
				],
				'filter' => [
					'type' => false
				],
				'priority' => 6
			],
			'#choice' => [
				'sort' => [
					'type' => 'text',
					'ajax' => 'userChoice'
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

