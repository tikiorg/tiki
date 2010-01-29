<?php

/**
 * @group gui
 */
 
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';


class  AcceptanceTests_MultilinguallibTest extends TikiSeleniumTestCase
{
    protected function setUp()
    {
    	$this->markTestSkipped("These tests are still too experimental, so skipping it.");    	
    	$this->setBrowserUrl('http://localhost/');
        $this->current_test_db = "multilingualTestDump.sql";
        $this->restoreDBforThisTest();         
    }

   /**
    * @group gui
    */ 
	public function test_remember_to_reactivate_all_tests_in_MultilinguallibTest() {
		global $multilinguallib;
		print "-- test_remember_to_reactivate_all_tests_in_MultilinguallibTest: upon entry, \$multilinguallib=$multilinguallib\n";
		$this->fail("Don't forget!!!");
	}
	
	
	
       
}
