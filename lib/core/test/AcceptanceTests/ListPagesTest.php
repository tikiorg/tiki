<?php

require_once 'PHPUnit/Extensions/SeleniumTestCase.php';


class  AcceptanceTests_ListPagesTest extends TikiSeleniumTestCase
{

    public function testListPagesTableIsWellFormed() {
        $this->openTikiPage('tiki-listpages.php');
        $this->_assertListPagesTableIsWellFormed();
        $this->_assertListedPagesWere(array(0 => "HomePage", 1 => 'EnglishTestPage'), "Listed pages");  
        $this->assertEquals("Page", $this->getTable("//div[@id='tiki-listpages-content']/table.0.0"));
    	$this->assertEquals("Hits", $this->getTable("//div[@id='tiki-listpages-content']/table.0.1"));
    	$this->assertEquals("Last mod", $this->getTable("//div[@id='tiki-listpages-content']/table.0.2"));
    	$this->assertEquals("Last author", $this->getTable("//div[@id='tiki-listpages-content']/table.0.3"));
    	$this->assertEquals("Vers.", $this->getTable("//div[@id='tiki-listpages-content']/table.0.4"));
    }    
    
    public function testPageSortingWorks()
    {
    	$this->open("/tiki-trunk/tiki-listpages.php");
    	$this->clickAndWait("link=Page");
    	$this->assertEquals("EnglishTestPage", $this->getTable("//div[@id='tiki-listpages-content']/table.1.0", "Pages were not sorted out in ascending order"));
    	$this->assertEquals("HomePage", $this->getTable("//div[@id='tiki-listpages-content']/table.2.0", "Pages were not sorted out in ascending order"));
    	$this->clickAndWait("link=Page");
    	$this->assertEquals("HomePage", $this->getTable("//div[@id='tiki-listpages-content']/table.1.0", "Pages were not sorted out in descending order"));
    	$this->assertEquals("EnglishTestPage", $this->getTable("//div[@id='tiki-listpages-content']/table.2.0", "Pages were not sorted out in descending order"));
  	}
    
    public function testDeleteAPageFromTheList() {
        $this->openTikiPage('tiki-listpages.php');
        $this->logInIfNecessaryAs('admin');
        $this->_assertListedPagesWere(array(0 => 'HomePage', 1 => 'EnglishTestPage'), "Listed pages");
		$this->assertTrue($this->isElementPresent("//img[@alt='Remove']"));
    	$this->clickAndWait("//img[@alt='Remove']");
    	$this->open('http://localhost/tiki-trunk/tiki-listpages.php');                        
    	$this->_assertListedPagesWere(array(0 => "HomePage"), "Listed pages");
    }
    
    public function testLinksInListPagesWork() {
		$this->openTikiPage('tiki-listpages.php');
		$this->logInIfNecessaryAs('admin');
        $this->assertElementPresent("link=EnglishTestPage", "EnglishTestPage was not there");
        $this->clickAndWait("link=EnglishTestPage");
        $this->assertTrue($this->isTextPresent("This is a test page in English"));
        $this->openTikiPage('tiki-listpages.php');
        $this->clickAndWait("link=HomePage");
        $this->assertTrue($this->isTextPresent("This is the default HomePage for your Tiki"));
    }
    
    
    /**************************************
     * Helper methods
     **************************************/

    protected function setUp()
    {
//    	$this->printImportantMessageForTestUsers();
        $this->setBrowser('*firefox C:\Program Files\Mozilla Firefox\firefox.exe');
        $this->setBrowserUrl('http://localhost/');
		$this->restoreDB(get_class($this));         
    }
    
    public function printImportantMessageForTestUsers() {
       die("ListPagesTest will not work unless:\n".
                   "- the name of the Tiki db is 'tiki_db_for_acceptance_tests' and \n".
				   "- the file 'listPagesTestDump.sql' (not in svn, due to its size) is copied in the mySql data directory.\n".	   
				   "Comment out the call to printImportantMessageForTestUsers() in ListPagesTest::setUp() to run the tests.\n");
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
?>
