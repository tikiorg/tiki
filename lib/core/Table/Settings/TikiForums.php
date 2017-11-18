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
 * Class Table_Settings_TikiForums
 *
 * Tablesorter settings for the table listing a forums at tiki-forums.php
 *
 * @package Tiki
 * @subpackage Table
 * @uses Table_Settings_Standard
 */
class Table_Settings_TikiForums extends Table_Settings_Standard
{
	protected $ts = [
		'ajax' => [
			'url' => [
				'file' => 'tiki-forums.php',
			],
		],
		'columns' => [
			'#name' => [
				'sort' => [
					'type' => true,
					'dir' => 'asc',
					'ajax' => 'name',
				],
				'filter' => [
					'type' => 'text',
					'ajax' => 'find',
				],
				'priority' => 'critical',
			],
			'#threads' => [
				'sort' => [
					'type' => 'digit',
					'ajax' => 'threads',
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 6,
			],
			'#comments' => [
				'sort' => [
					'type' => 'digit',
					'ajax' => 'comments',
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 6,
			],
			'#ppd' => [
				'sort' => [
					'type' => false,
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 6,
			],
			'#lastPost' => [
				'sort' => [
					'type' => 'isoDate',
					'ajax' => 'lastPost',
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 3,
			],
			'#hits' => [
				'sort' => [
					'type' => 'digit',
					'ajax' => 'hits',
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 5,
			],
			'#actions' => [
				'sort' => [
					'type' => false,
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 1,
			],
		],
	];
}
