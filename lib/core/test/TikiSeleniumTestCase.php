<?php
/*
 * Parent class of all Selenium test cases.
 */
 
 require_once 'PHPUnit/Extensions/SeleniumTestCase.php';
 
 
class TikiSeleniumTestCase extends PHPUnit_Extensions_SeleniumTestCase {
  
    public function openTikiPage($tikiPage) {
       $this->open("http://localhost/tiki-trunk/$tikiPage");
    }
    
    public function assertSelectElementContainsItems($selectElementID, $expItems, $message) {
        $this->assertElementPresent($selectElementID, "$message\nMarkup element '$selectElementID' did not exist");
        $gotItemsText = $this->getSelectOptions($selectElementID);
        $expItemsText = array_keys($expItems);
        $this->assertEquals($gotItemsText, $expItemsText, "$message\nItems in the Select element '$selectElementID' were wrong.");                                    
        foreach ($expItems as $anItem => $anItemValue) {
           $thisItemElementID = "$selectElementID/option[@value='$anItemValue']";
           $this->assertElementPresent($thisItemElementID);
        }
    }
} 
?>
