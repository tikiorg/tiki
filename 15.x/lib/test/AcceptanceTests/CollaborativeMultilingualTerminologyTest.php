<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*************************************************************
* Automated acceptance tests for the Collaborative Multilingual
* Terminology Profile.
*
* See: http://profiles.tiki.org/Collaborative_Multilingual_Terminology
*************************************************************/

/**
 * @group gui
 */

require_once 'TikiSeleniumTestCase.php';

class  AcceptanceTests_CollaborativeMultilingualTerminologyTest extends TikiSeleniumTestCase
{
	protected function setUp()
	{
		$this->markTestSkipped("This test still too experimental, so skipping it.");    	
		$this->current_test_db = "multilingualTestDump.sql";
		$this->restoreDBforThisTest();
		#        $this->applyProfile('Collaborative_Multilingual_Terminology',
		#                            'http://profiles.tiki.org/');
	}

	public function applyProfile($profileName, $profileRepositoryUrl)
	{
		echo("-- CollaborativeMultilingualTerminology.applyProfile: invoked\n");
		$this->logInIfNecessaryAs('admin');
		echo("-- CollaborativeMultilingualTerminology.applyProfile: logged as admin\n");
	}

	public function ___testRememberToReactivateAllTestsInCollaborativeMultilingualTerminologyTest()
	{
    	$this->fail("Don't forget to do this");
	}
}
