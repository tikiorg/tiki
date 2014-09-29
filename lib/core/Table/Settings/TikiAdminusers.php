<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
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
 * @uses Table_Settings_Abstract
 */
class Table_Settings_TikiAdminusers extends Table_Settings_Abstract
{
	protected $ts = array(
		'id' => 'adminusers',
		'selflinks' => true,
		'sorts' => array(
			'type' => 'reset',
			'group' => true,
		),
		'filters' => array(
			'type' => 'reset',
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
		'pager' => array(
			'type' => true,
		),
		'ajax' => array(
			'type' => true,
			'url' => 'tiki-adminusers.php?{sort:sort}&{filter:filter}',
			'offset' => 'offset'
		),
		'columns' => array(
			0 => array(					//checkbox
				'sort' => array(
					'type' => false,
					'group' => 'checkbox',
				),
				'filter' => array(
					'type' => false,
				),
				'resizable' => false,
			),
			1 => array(					//user
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
			),
			2 => array(					//email - only if $prefs.login_is_email != 'y'
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
			),
			3 => array(					//openid - only if $prefs.auth_method == 'openid'
				'sort' => array(
					'type' => true,
					'ajax' => 'openid_url',
					'group' => 'letter'
				),
				'filter' => array(
					'type' => false,	//no filter since $userlib->get_users doesn't have it
				),
			),
			4 => array(					//last login
				'sort' => array(
					'type' => 'text',
					'ajax' => 'currentLogin',
					'group' => 'false'
				),
				'filter' => array(
					'type' => false,
				),
			),
			5 => array(					//registered
				'sort' => array(
					'type' => 'isoDate',
					'ajax' => 'registrationDate',
					'group' => 'false'
				),
				'filter' => array(
					'type' => false,	//no filter since $userlib->get_users doesn't have it
				),
			),
			6 => array(					//group
				'sort' => array(
					'type' => false,
				),
				'filter' => array(
					'placeholder' => 'Select group',
					'type' => 'dropdown',
					'ajax' => 'filterGroup',
				),
			),
			7 => array(					//actions
				'sort' => array(
					'type' => false,
				),
				'filter' => array(
					'type' => false,
				),
			),
		),
	);

	/**
	 * Manipulate table-specific settings as needed.
	 *
	 * @return array|null
	 */
	protected function getTableSettings()
	{
		//change columns based on prefs to be consistent with tiki-adminusers.tpl
		global $prefs;
		if ($prefs['login_is_email'] == 'y') {
			unset($this->ts['columns'][2]);
		}
		if ($prefs['auth_method'] != 'openid') {
			unset($this->ts['columns'][3]);
		}

		$this->ts['columns'] = array_values($this->ts['columns']);

		return $this->ts;
	}

}

