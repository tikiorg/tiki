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
 * Class Table_Settings_TikiAdminForums
 *
 * Tablesorter settings for the table listing of forums at tiki-admin_forums.php
 *
 * @package Tiki
 * @subpackage Table
 * @uses Table_Settings_Standard
 */
class Table_Settings_TikiAdminForums extends Table_Settings_Standard
{
	protected $ts = array(
		'ajax' => array(
			'url' => array(
				'file' => 'tiki-admin_forums.php',
			),
		),
		'columns' => array(
			'#checkbox' => array(
				'sort' => array(
					'type' => false,
				),
				'filter' => array(
					'type' => false,
				),
				'resizable' => false,
				'priority' => 'critical',
			),
			'#name' => array(
				'sort' => array(
					'type' => true,
					'ajax' =>'name',
				),
				'filter' => array(
					'type' => 'text',
					'ajax' =>'find',
				),
				'priority' => 'critical',
			),
			'#threads' => array(
				'sort' => array(
					'type' => 'digit',
					'ajax' =>'threads',
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 6,
			),
			'#comments' => array(
				'sort' => array(
					'type' => 'digit',
					'ajax' =>'comments',
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 6,
			),
			'#users' => array(
				'sort' => array(
					'type' => 'digit',
					'ajax' =>'users',
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 6,
			),
			'#age' => array(
				'sort' => array(
					'type' => 'digit',
					'ajax' =>'age',
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 6,
			),
			'#ppd' => array(
				'sort' => array(
					'type' => 'digit',
					'ajax' =>'posts_per_day',
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 6,
			),
			'#hits' => array(
				'sort' => array(
					'type' => false,
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 5,
			),
			'#actions' => array(
				'sort' => array(
					'type' => false,
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 1,
			),
		),
	);
}