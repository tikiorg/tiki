<?php

/*************************************************************
* Automated acceptance tests for Multilingual Features.
*************************************************************/


require_once 'TikiSeleniumTestCase.php';

class  AcceptanceTests_MultilingualTest extends TikiSeleniumTestCase
{

    public function ___testRememberToReactivateAllTestsInMultilingualTest() {
       $this->fail("Don't forget to do this");
    }
    
    public function testMultilingualPageDisplaysLanguagePicklist() {
       $this->openTikiPage('tiki-index.php?page=Multilingual+Test+Page+1');
       $this->assertLanguagePicklistHasLanguages(array('English' => 'Multilingual Test Page 1', 
                                                       'Français' => 'Page de test multilingue 1'));
    }
   
    /**************************************
     * Helper methods
     **************************************/

    protected function setUp()
    {
           $this->setBrowser('*firefox C:\Program Files\Mozilla Firefox\firefox.exe');
           $this->setBrowserUrl('http://www.example.com/');
    }
    

    public function assertLanguagePicklistHasLanguages($expAvailableLanguages) {
        $this->assertSelectElementContainsItems("xpath=//form[@id='available-languages-form']/select[@name='page' and @onchange='quick_switch_language( this )']",
                  $expAvailableLanguages, 
                  "Language picklist was wrong."); 
    }
}
?>
