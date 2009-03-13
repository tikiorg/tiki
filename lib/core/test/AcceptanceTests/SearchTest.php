<?php

require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class  AcceptanceTests_SearchTest extends PHPUnit_Extensions_SeleniumTestCase
{

    public function testRememberToReactivateAllTestsInSearchTest() {
       $this->fail("Don't forget to do this");
    }

    public function testSearchFormIsWellFormed() {
        $this->open('http://localhost/tiki-trunk/tiki-index.php');
        $this->_assertSearchFormIsWellFormed();
    }    
    
    public function ___testFillSearchFormAndSubmit() {
        $query = 'feature';
        $this->_searchFor($query);
        $this->_assertSearchResultsWere(array(0 => "HomePage", 1 => 'Multilingual Test Page 1', 2 => 'Another page containing the word feature'), 
                                        $query, "");
    }
    
    
    public function ___testSearchIsCaseInsensitive() {
       $query = 'hello';
       $this->_searchFor($query);
       $this->_assertSearchResultsWere(array(0 => "test page for search 1", 
                                             1 => 'test page for search 2'), 
                                        $query, "Bad list of search results for query '$query'. Search should have been case insensitive.");
    }
    
    public function ___testByDefaultSearchLooksForAnyOfTheQueryTerms() {
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
           $this->setBrowser('*firefox C:\Program Files\Mozilla Firefox\firefox.exe');
           $this->setBrowserUrl('http://www.example.com/');
    }

    private function _searchFor($query) {
        $this->open('http://localhost/tiki-trunk/tiki-index.php');
        $this->type("xpath=//form[@id='search-form']/input[@name='highlight']", $query);
        $this->clickAndWait("xpath=//form[@id='search-form']/input[@type='submit']");
    }

    private function _assertSearchFormIsWellFormed() {
    
        echo "-- _assertSearchFormIsWellFormed: content of active page is: \n";
        echo $this->getHTMLSource();
    
        $this->assertElementPresent("xpath=//form[@id='search-form']", 
                                    "Search form was not present");
        $this->assertElementPresent("xpath=//form[@id='search-form']/input[@name='highlight']",
                                    "Search query field was not present");
        $this->assertElementPresent("xpath=//form[@id='search-form']/span/select[@name='where']",
                                    "Picklist for where to search was not present");
        $this->assertElementPresent("xpath=//form[@id='search-form']/input[@type='submit']",
                                    "Submit button for search form was not present");
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
