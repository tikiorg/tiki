<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @group gui
 */

require_once 'TikiTestCase.php';

/* 
 * This test case verifies that we can access various Tiki libraries
 * from inside Acceptance tests. This can be useful for example,
 * to set starting conditions of the Tiki DB directly, without having
 * to go through Selenium actions in the browser (the later 
 * is slow).
 */

class  AcceptanceTests_TikiLibrariesAccessTest extends TikiTestCase
{

	protected function setUp()
	{
		$this->markTestSkipped("TikiLibrariesAccessTest is still experimental. So skipping it for now.");
	}


	public function testRememberToReactivateAllTestsInTikiLibrariesAccessTest()
	{
		$this->fail("Don't forget to do this");
	}

	/**
	 * @group gui
	 */ 
	public function testAccessPreferences()
	{
		global $tikilib, $prefs;

		$pref_name = 'feature_machine_translation';

		$gotPreference = $tikilib->get_preference($pref_name);
		$this->assertEquals('n', $gotPreference, "get_preference() should initially have returned 'n' for preference '$pref_name'");
		$gotPreference = $prefs[$pref_name];
		$this->assertEquals('n', $gotPreference, "\$prefs[$pref_name] should initially have been 'n'");

		$tikilib->set_preference($pref_name, 'y');
		$gotPreference = $tikilib->get_preference($pref_name);
		$this->assertEquals('y', $gotPreference, "After setting it, get_preference() should have returned 'y' after following preference was set: '$pref_name'");
		$gotPreference = $prefs[$pref_name];
		$this->assertEquals('y', $gotPreference, "\$prefs[$pref_name] should initially have been 'y' after that preference was set. NOTE: At this point, this test fails. I think set_preference() should not only update the DB, but also set \$prefs");
	}    

}
