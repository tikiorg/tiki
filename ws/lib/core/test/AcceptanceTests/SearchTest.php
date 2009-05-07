<?php

require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class  AcceptanceTests_SearchTest extends TikiSeleniumTestCase
{

    public function ___testRememberToReactivateAllTestsInSearchTest() {
       	$this->fail("Don't forget to do this");
    }
    
    public function testSearchFormIsWellFormed() {
       	$this->openTikiPage('tiki-index.php');
       	$this->logInIfNecessaryAs('admin');
       	$this->_assertSearchFormIsWellFormed();
    }    
    
    public function testFillSearchFormAndSubmit() {
    	$this->openTikiPage('tiki-index.php');
    	$this->logInIfNecessaryAs('admin');
        $query = 'feature';
        $this->_searchFor($query);
        $this->_assertSearchResultsWere(array(0 => "HomePage", 1 => 'Multilingual Test Page 1', 2 => 'Another page containing the word feature'), 
                                        $query, "");
    }
    
    
    public function testSearchIsCaseInsensitive() {
       $this->openTikiPage('tiki-index.php');
       $this->logInIfNecessaryAs('admin');	
       $query = 'hello';
       $this->_searchFor($query);
       $this->_assertSearchResultsWere(array(0 => "test page for search 1", 
                                             1 => 'test page for search 2'), 
                                        $query, "Bad list of search results for query '$query'. Search should have been case insensitive.");
    }
    
    public function testByDefaultSearchLooksForAnyOfTheQueryTerms() {
       $this->openTikiPage('tiki-index.php');
       $this->logInIfNecessaryAs('admin');
       $query = 'hello world';
       $this->_searchFor($query);
       $this->_assertSearchResultsWere(array(0 => "test page for search 1", 
                                             1 => "test page for search 2",
                                             2 => 'test page for search 3'), 
                                        $query, "Bad list of search results for multi word query '$query'. Could be that the search engine did not use an OR to combine the search words.");

    }

    /**************************************
     * Helper methods
     **************************************/

    protected function setUp()
    {
		$this->printImportantMessageForTestUsers();
		$this->setBrowser('*firefox C:\Program Files\Mozilla Firefox\firefox.exe');
        $this->setBrowserUrl('http://localhost/');
        $this->current_test_db = "searchTestDump.sql";
        $this->restoreDBforThisTest();
    }

    public function printImportantMessageForTestUsers() {
       die("SearchTest will not work unless:\n".
                   "- the name of the Tiki db is 'tiki_db_for_acceptance_tests' and \n".
				   "- the file 'searchTestDump.sql' (not in svn, due to its size) is copied in the mySql data directory.\n" .
				   "Comment out the call to printImportantMessageForTestUsers() in SearchTest::setUp() to run the tests.\n");
    }


    private function _searchFor($query) {
 		$this->type('fuser', $query);
    	$this->clickAndWait('search');
    }

    private function _assertSearchFormIsWellFormed() {
    
        $this->assertElementPresent("xpath=//form[@id='search-form']", 
                                    "Search form was not present");
        $this->assertElementPresent("fuser", 
                                    "Search input field not present");
        $this->assertElementPresent("xpath=//div[@id='sitesearchbar']", 
                                    "Site search bar was not present");
    }
     
    private function _assertSearchResultsWere($listOfHits, $query, $message) {
        $this->assertElementPresent("xpath=//div[@class='searchresults']",
                                    "List of search results was absent for query '$query'");
        $numExpectedHits = count($listOfHits);
        foreach ($listOfHits as $expectedHit) {
           $this->assertElementPresent("link=$expectedHit", "$message\nLink to expected hit '$expectedHit' was missing for query '$query'");
        } 
    }
}
?>
