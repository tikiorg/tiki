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
 * Class Table_Manager
 * Main class to apply jQuery Tablesorter plugin functionality to tables in Tiki.
 * Called from Table_Factory.
 *
 * @package Tiki
 * @subpackage Table
 */

class Table_Manager
{
	private $codeObj;

	/**
	 * Called by Table_Factory and uses the settings associated with the table to create the necessary jQuery and HTML
	 * and add the jQuery to the header
	 *
	 * @param $settings			settings created by running the appropriate Table_Settings class
	 */
	public function __construct($settings)
	{
		$this->setAllCode($settings);
		$this->addToHeader();
	}

	/**
	 * Uses table settings to generate necessary jQuery and HTML code
	 *
	 * @param $settings			settings created by running the appropriate Table_Settings class
	 */
	private function setAllCode($settings)
	{
		$this->codeObj = Table_Factory::build('manager', $settings, 'code');
		$this->codeObj->setCode();
	}

	/**
	 * Adds code to header
	 */
	private function addToHeader()
	{
		if (!empty(Table_Code_Manager::$code)) {
			$headerlib = TikiLib::lib('header');
			$headerlib->add_jq_onready(Table_Code_Manager::$code);
			//need to empty static $code in case there are multiple tables on a page
			Table_Code_Manager::$code = '';
		}
	}
}
