<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
	protected $ts = array(
		'ajax' => array(
			'url' => array(
				'file' => 'tiki-forums.php',
			),
		),
		'columns' => array(
			'#name' => array(
				'sort' => array(
					'type' => true,
					'dir' => 'asc',
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
			'#ppd' => array(
				'sort' => array(
					'type' => false,
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 6,
			),
			'#lastPost' => array(
				'sort' => array(
					'type' => 'isoDate',
					'ajax' => 'lastPost',
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 3,
			),
			'#hits' => array(
				'sort' => array(
					'type' => 'digit',
					'ajax' =>'hits',
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