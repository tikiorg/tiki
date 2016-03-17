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
	protected $ts = array(
		'filters' => array(
			'external' => array(
				0 => array(
					'type' => 'dropdown',
					'options' => array(
						'Email not confirmed' => 'filterEmailNotConfirmed=on',
						'User not validated' => 'filterNotValidated=on',
						'Never logged in' => 'filterNeverLoggedIn=on',
					),
				),
			),
		),
		'ajax' => array(
			'url' => array(
				'file' => 'tiki-adminusers.php',
			),
		),
		'columns' => array(
			'#checkbox' => array(
				'sort' => array(
					'type' => false,
					'group' => 'checkbox',
				),
				'filter' => array(
					'type' => false,
				),
				'resizable' => false,
				'priority' => 'critical',
			),
			'#user' => array(
				'sort' => array(
					'type' => true,
					'dir' => 'asc',
					'ajax' => 'login',
					'group' => 'letter'
				),
				'filter' => array(
					'type' => 'text',
					'ajax' => 'find',
				),
				'priority' => 'critical',
			),
			'#email' => array(					//only if $prefs.login_is_email != 'y'
				'sort' => array(
					'type' => true,
					'ajax' =>'email',
					'group' => 'letter'
				),
				'filter' => array(
					'type' => 'text',
					'placeholder' => 'Enter valid email...',
					'ajax' => 'filterEmail',
				),
				'priority' => 1,
			),
			'#openid' => array(					//only if $prefs.auth_method == 'openid'
				'sort' => array(
					'type' => true,
					'ajax' => 'openid_url',
					'group' => 'letter'
				),
				'filter' => array(
					'type' => false,	//no filter since $userlib->get_users doesn't have it
				),
				'priority' => 6,
			),
			'#lastlogin' => array(
				'sort' => array(
					'type' => 'text',
					'ajax' => 'currentLogin',
					'group' => 'false'
				),
				'filter' => array(
					'type' => false,
				),
				'priority' => 4,
			),
			'#registered' => array(
				'sort' => array(
					'type' => 'isoDate',
					'ajax' => 'registrationDate',
					'group' => 'false'
				),
				'filter' => array(
					'type' => false,	//no filter since $userlib->get_users doesn't have it
				),
				'priority' => 5,
			),
			'#groups' => array(
				'sort' => array(
					'type' => false,
				),
				'filter' => array(
					'placeholder' => 'Select group',
					'type' => 'dropdown',
					'ajax' => 'filterGroup',
				),
				'priority' => 3,
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

