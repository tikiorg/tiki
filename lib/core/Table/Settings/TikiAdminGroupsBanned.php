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
 * Class Table_Settings_TikiAdminGroups
 *
 * Tablesorter settings for the table listing users banned from a group at tiki-admingroups.php
 *
 * @package Tiki
 * @subpackage Table
 * @uses Table_Settings_Standard
 */
class Table_Settings_TikiAdminGroupsBanned extends Table_Settings_Standard
{
	protected $ts = [
		'ajax' => [
			'url' => ['file' => 'tiki-admingroups.php'],
			'sortparam' => 'bannedSort',
			'offset' => 'bannedOffset',
			'numrows' => 'bannedMax',
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
					'ajax' => 'source_itemId'
				],
				'filter' => [
					'type' => false
				],
				'priority' => 'critical'
			],
			'#unban' => [
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

