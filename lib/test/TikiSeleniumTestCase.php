<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
 * Parent class of all Selenium test cases.
 */


class TikiSeleniumTestCase extends PHPUnit_Extensions_Selenium2TestCase
{
	protected $backupGlobals = FALSE;
	var $current_test_db; 
	var $user_credentials = array (
			'admin' => 'tiki'
			);

	function __construct($name = '')
	{
		parent::__construct($name);
		$this->configure();
	}

	private function configure()
	{
		$test_tiki_root_url = NULL;
		$config_fpath = './tests_config.php';

		if (! file_exists($config_fpath))
			return false;

		$lines = file($config_fpath);
		$source = implode('', $lines);
		echo "-- TikiSeleniumTestCase.configure: After reading config file: \$source='$source'\n";
		eval($source);
		echo "-- TikiSeleniumTestCase.configure: After evaluating config file: \$test_site_url='$test_site_url'\n";
		if ($test_tiki_root_url == NULL) {
			exit("Variable \$test_tiki_root_url MUST be defined in test configuration file: '$config_fpath'");
		} else {
			$this->setBrowserUrl($test_tiki_root_url);
		}	
		if (!preg_match('/^http\:\/\/local/', $test_tiki_root_url)) {
			exit("Error found in test configuration file '$config_fpath'\n".
					"The URL specified by \$test_tiki_root_url should start with http://local, in order to prevent accidentally running tests on a non-local test site.\n".
					"Value was: '$test_tiki_root_url'\n");
		}
	}

	public function openTikiPage($tikiPage)
	{
		$this->open("http://localhost/tiki-trunk/$tikiPage");
	}

	public function restoreDBforThisTest()
	{
		$dbRestorer = new TikiAcceptanceTestDBRestorerSQLDumps();
		$error_msg = $dbRestorer->restoreDB($this->current_test_db);
		if ($error_msg != null) {
			$this->markTestSkipped($error_msg);
		}
	}

	public function logInIfNecessaryAs($my_user)
	{
		if (!$this->_login_as($my_user)) {
			die("Couldn't log in as $my_user!");
		}
	}

	public function logOutIfNecessary()
	{
		if ($this->isElementPresent("link=Logout")) {
			$this->clickAndWait("link=Logout");
		} 
	}

	public function assertSelectElementContainsItems($selectElementID, $expItems, $message)
	{
		$this->assertElementPresent($selectElementID, "$message\nMarkup element '$selectElementID' did not exist");
		$selectElementLabels = 	$this->getSelectOptions($selectElementID);
		foreach ($expItems as $anItem => $anItemValue) {
			$this->assertTrue(in_array($anItem, $selectElementLabels), "$message\n$anItem is not in the select element list");		
			$thisItemElementID = "$selectElementID/option[@value='$anItemValue']";
			$this->assertElementPresent($thisItemElementID);
		}
	}

	public function assertSelectElementContainsAllTheItems($selectElementID, $expItems, $message)
	{
		$this->assertElementPresent($selectElementID, "$message\nMarkup element '$selectElementID' did not exist");
		$gotItemsText = $this->getSelectOptions($selectElementID);
		$expItemsText = array_keys($expItems);
		$this->assertEquals($gotItemsText, $expItemsText, "$message\nItems in the Select element '$selectElementID' were wrong.");                                    
		foreach ($expItems as $anItem => $anItemValue) {
			$thisItemElementID = "$selectElementID/option[@value='$anItemValue']";
			$this->assertElementPresent($thisItemElementID);
		}
	}

	public function assertSelectElementDoesNotContainItems($selectElementID, $expItems, $message)
	{
		$this->assertElementPresent($selectElementID, "$message\nMarkup element '$selectElementID' did not exist");
		$gotItemsText = $this->getSelectOptions($selectElementID);
		$expItemsText = array_keys($expItems);
		//        $this->assertEquals($gotItemsText, $expItemsText, "$message\nItems in the Select element '$selectElementID' were wrong.");                                    
		foreach ($expItems as $anItem => $anItemValue) {
			$thisItemElementID = "$selectElementID/option[@value='$anItemValue']";
			$this->assertFalse($this->isElementPresent($thisItemElementID));
		}
	}

	private function _login_as($user)
	{
		if ($this->isElementPresent("sl-login-user")) {
			$password = $this->user_credentials[$user];
			$this->type("sl-login-user", $user);
			$this->type("sl-login-pass", $password);
			$this->clickAndWait("login");
			if ($this->isTextPresent("Invalid username or password")) {
				return false;
			}
		}
		return true;
	}

	public function implode_with_key($glue = null, $pieces, $hifen = '=>')
	{
		$return = null;
		foreach ($pieces as $tk => $tv) $return .= $glue.$tk.$hifen.$tv;
		return substr($return, 1);
	}
} 
