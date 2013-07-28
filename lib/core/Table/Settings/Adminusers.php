<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
 * Class Table_Settings_Users
 *
 * Tablesorter settings for the table listing users at tiki-adminusers.php
 *
 * @package Tiki
 * @subpackage Table
 * @uses Table_Settings_Abstract
 */
class Table_Settings_Adminusers extends Table_Settings_Abstract
{
	protected $ts = array(
		'id' => 'usertable',
		'serverside' => true,
		'selflinks' => true,
		'sort' => array(
			'columns' => array(
				0 => array(					//checkbox
					'type' => false,
				),
				1 => array(					//user
					'type' => true,
					'dir' => 'asc',
					'ajax' => 'login',
					'group' => 'letter'
				),
				2 => array(					//email - only if $prefs.login_is_email != 'y'
					'type' => true,
					'ajax' =>'email',
					'group' => 'letter'
				),
				3 => array(					//openid - only if $prefs.auth_method == 'openid'
					'type' => true,
					'ajax' => 'openid_url',
					'group' => 'letter'
				),
				4 => array(					//last login
					'type' => 'text',
					'ajax' => 'currentLogin',
					'group' => 'word'
				),
				5 => array(					//registered
					'type' => true,
					'ajax' => 'registrationDate',
					'group' => 'date-year'
				),
				6 => array(					//group
					'type' => false,
				),
				7 => array(					//actions
					'type' => false,
				),
			),
		),
		'filters' => array(
			'columns' => array(
				0 => array(					//checkbox
					'type' => false,
				),
				1 => array(					//user
					'type' => 'text',
					'ajax' => 'find',
				),
				2 => array(					//email - only if $prefs.login_is_email != 'y'
					'type' => 'text',
					'placeholder' => 'Enter valid email...',
					'ajax' => 'filterEmail',
				),
				3 => array(					//openid - only if $prefs.auth_method == 'openid'
					'type' => false,		//no filter since $userlib->get_users doesn't have it
				),
				4 => array(					//last login
					'type' => 'dropdown',
					'options' => array(
						'Never logged in' => 'filterNeverLoggedIn=on',
					),
				),
				5 => array(					//registered
					'type' => false,		//no filter since $userlib->get_users doesn't have it
				),
				6 => array(					//group
					'type' => 'dropdown',
					'ajax' => 'filterGroup',
				),
				7 => array(					//actions
					'type' => 'dropdown',
					'options' => array(
						'Email not confirmed' => 'filterEmailNotConfirmed=on',
						'User not validated' => 'filterNotValidated=on',
					),
				),
			),
		),
		'pager' => array(
			'type' => true,
		),
		'ajax' => array(
			'url' => 'tiki-adminusers.php?{sort:sort}&{filter:filter}',
		)
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
			unset($this->ts['sort']['columns'][2]);
			unset($this->ts['filters']['columns'][2]);
		}
		if ($prefs['auth_method'] != 'openid') {
			unset($this->ts['sort']['columns'][3]);
			unset($this->ts['filters']['columns'][3]);
		}

		$this->ts['sort']['columns'] = array_values($this->ts['sort']['columns']);
		$this->ts['filters']['columns'] = array_values($this->ts['filters']['columns']);

		return $this->ts;
	}

}

