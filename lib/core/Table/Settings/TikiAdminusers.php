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
 * Class Table_Settings_TikiAdminusers
 *
 * Tablesorter settings for the table listing users at tiki-adminusers.php
 *
 * @package Tiki
 * @subpackage Table
 * @uses Table_Settings_Standard
 */
class Table_Settings_TikiAdminusers extends Table_Settings_Standard
{
	protected $ts = [
		'filters' => [
			'external' => [
				0 => [
					'type' => 'dropdown',
					'options' => [
						'All users (no filter)' => '',
						'Email not confirmed' => 'filterEmailNotConfirmed=on',
						'User not validated' => 'filterNotValidated=on',
						'Never logged in' => 'filterNeverLoggedIn=on',
					],
				],
			],
		],
		'ajax' => [
			'url' => [
				'file' => 'tiki-adminusers.php',
			],
		],
		'columns' => [
			'#checkbox' => [
				'sort' => [
					'type' => false,
				],
				'filter' => [
					'type' => false,
				],
				'resizable' => false,
				'priority' => 'critical',
			],
			'#user' => [
				'sort' => [
					'type' => true,
					'dir' => 'asc',
					'ajax' => 'login',
				],
				'filter' => [
					'type' => 'text',
					'ajax' => 'find',
				],
				'priority' => 'critical',
			],
			'#email' => [					//only if $prefs.login_is_email != 'y'
				'sort' => [
					'type' => true,
					'ajax' => 'email',
				],
				'filter' => [
					'type' => 'text',
					'placeholder' => 'Enter valid email...',
					'ajax' => 'filterEmail',
				],
				'priority' => 1,
			],
			'#openid' => [					//only if $prefs.auth_method == 'openid'
				'sort' => [
					'type' => true,
					'ajax' => 'openid_url',
				],
				'filter' => [
					'type' => false,	//no filter since $userlib->get_users doesn't have it
				],
				'priority' => 6,
			],
			'#lastlogin' => [
				'sort' => [
					'type' => 'text',
					'ajax' => 'currentLogin',
				],
				'filter' => [
					'type' => false,
				],
				'priority' => 4,
			],
			'#registered' => [
				'sort' => [
					'type' => 'isoDate',
					'ajax' => 'registrationDate',
				],
				'filter' => [
					'type' => false,	//no filter since $userlib->get_users doesn't have it
				],
				'priority' => 5,
			],
			'#groups' => [
				'sort' => [
					'type' => false,
				],
				'filter' => [
					'placeholder' => 'Select group',
					'type' => 'dropdown',
					'ajax' => 'filterGroup',
				],
				'priority' => 3,
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
