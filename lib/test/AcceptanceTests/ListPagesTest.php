<?php

/**
 * @group gui
 */
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';


class  AcceptanceTests_ListPagesTest extends TikiSeleniumTestCase
{
	
	public function ___testRememberToReactivateTestsIn_AcceptanceTests_ListPagesTest() {
		$this->fail("don't forget!!");
	}
	
   /**
    * @group gui
    */ 
    public function testListPagesTableIsWellFormed() {
        $this->openTikiPage('tiki-listpages.php');
        $this->_assertListPagesTableIsWellFormed();
        $this->_assertListedPagesWere(array(0 => "HomePage", 1 => "EnglishTestPage"), "Listed pages");  
        $this->assertEquals("Page", $this->getTable("//div[@id='tiki-listpages-content']/form/table.0.1"));
    	$this->assertEquals("Hits", $this->getTable("//div[@id='tiki-listpages-content']/form/table.0.2"));
    	$this->assertEquals("Last mod", $this->getTable("//div[@id='tiki-listpages-content']/form/table.0.3"));
    	$this->assertEquals("Last author", $this->getTable("//div[@id='tiki-listpages-content']/form/table.0.4"));
    	$this->assertEquals("Vers.", $this->getTable("//div[@id='tiki-listpages-content']/form/table.0.5"));
    }    
    
   /**
    * @group gui
    */ 
    public function testPageSortingWorks()
    {
    	$this->open("/tiki-trunk/tiki-listpages.php");
    	$this->clickAndWait("link=Page");
    	$this->assertEquals("EnglishTestPage", $this->getTable("//div[@id='tiki-listpages-content']/form/table.1.1", "Pages were not sorted out in ascending order"));
    	$this->assertEquals("HomePage", $this->getTable("//div[@id='tiki-listpages-content']/form/table.2.1", "Pages were not sorted out in ascending order"));
    	$this->clickAndWait("link=Page");
    	$this->assertEquals("HomePage", $this->getTable("//div[@id='tiki-listpages-content']/form/table.1.1", "Pages were not sorted out in descending order"));
    	$this->assertEquals("EnglishTestPage", $this->getTable("//div[@id='tiki-listpages-content']/form/table.2.1", "Pages were not sorted out in descending order"));
  	}
    
   /**
    * @group gui
    */ 
    public function testDeleteAPageFromTheList() {
        $this->openTikiPage('tiki-listpages.php');
        $this->logInIfNecessaryAs('admin');
        $this->_assertListedPagesWere(array(0 => 'HomePage', 1 => 'EnglishTestPage'), "Listed pages");
		$this->assertTrue($this->isElementPresent("//img[@alt='Remove']"));
    	$this->clickAndWait("//img[@alt='Remove']");
    	$this->open('http://localhost/tiki-trunk/tiki-listpages.php');                        
    	$this->_assertListedPagesWere(array(0 => "HomePage"), "Listed pages");
    }
    
   /**
    * @group gui
    */ 
    public function testLinksInListPagesWork() {
		$this->openTikiPage('tiki-listpages.php');
		$this->logInIfNecessaryAs('admin');
        $this->assertElementPresent("link=EnglishTestPage", "EnglishTestPage was not there");
        $this->clickAndWait("link=EnglishTestPage");
        $this->assertTrue($this->isTextPresent("This is a test page in English"));
        $this->openTikiPage('tiki-listpages.php');
        $this->clickAndWait("link=HomePage");
        $this->assertTrue($this->isTextPresent("Thank you for installing Tiki"));
    }
    
    
    /**************************************
     * Helper methods
     **************************************/

    protected function setUp()
    {
    	$this->markTestSkipped("These tests are still too experimental, so skipping it.");    	
    	$this->setBrowserUrl('http://localhost/');
        $this->current_test_db = "listPagesTestDump.sql";
        $this->restoreDBforThisTest();         
    }
 
    private function _assertListPagesTableIsWellFormed() {
    
        $this->assertElementPresent("xpath=//div[@id='tiki-listpages-content']", 
                                    "List Pages content was not present");
		$this->assertElementPresent("xpath=//a[contains(@title,'Last author')]", 
                                    "Last Author column was not present");
		$this->assertElementPresent("xpath=//a[contains(@title,'Versions')]", 
                                    "Versions column was not present");

    }
     
    private function _assertListedPagesWere($listOfPages, $message) {
        $this->assertElementPresent("xpath=//div[@id='tiki-listpages-content']",
                                    "List of pages was absent");
        foreach ($listOfPages as $expectedPage) {
           $this->assertElementPresent("link=$expectedPage", "$message\nLink to expected page '$expectedPage' was missing");
        } 
    }
       
}
