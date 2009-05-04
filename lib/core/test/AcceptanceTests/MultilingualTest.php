<?php

/*************************************************************
* Automated acceptance tests for Multilingual Features.
*************************************************************/


require_once 'TikiSeleniumTestCase.php';

class  AcceptanceTests_MultilingualTest extends TikiSeleniumTestCase
{

	public function __testRememberToReactivateAllTestsInMultilingualTest() {
    	$this->fail("Don't forget to do this");
    }
        
   public function testHomePageIsMultilingual() {
   		$this->openTikiPage('tiki-index.php');
   		$this->logInIfNecessaryAs('admin');
   		$this->assertLanguagePicklistHasLanguages(array('English' => 'HomePage'));
   }
    
    public function testMultilingualPageDisplaysLanguagePicklist() {
       	$this->openTikiPage('tiki-index.php?page=Multilingual+Test+Page+1');
       	$this->logInIfNecessaryAs('admin');
       	$this->assertLanguagePicklistHasLanguages(array('English' => 'Multilingual Test Page 1', 
                                                    'Français' => 'Page de test multilingue 1'));                                                    
    }
    
  	
  	public function testLanguageLinkLeadsToTranslatedPageInThatLanguage() {
  		$this->openTikiPage('tiki-index.php?page=Multilingual+Test+Page+1');
  		$this->logInIfNecessaryAs('admin');
  		$this->select("page", "label=Français");
    	$this->waitForPageToLoad("30000");
    	$this->assertTrue(preg_match('/page=Page\+de\+test\+multilingue\+1/', $this->getLocation()) == 1);
    	$this->assertElementPresent("link=Page de test multilingue 1");	
  	}
  	
  	public function testTranslateOptionAppearsOnlyWhenLoggedIn() {
  		$this->openTikiPage('tiki-index.php');
  		$this->logOutIfNecessary();
  		$this->assertLanguagePicklistHasNoTranslateOption();
  		$this->logInIfNecessaryAs('admin');
  		$this->assertLanguagePicklistHasTranslateOption();
  	}

    public function testClickOnTranslateShowsTranslatePage() {
    	$this->openTikiPage('tiki-index.php');
    	$this->logInIfNecessaryAs('admin');
    	$this->select("page", "label=Translate");
    	$this->waitForPageToLoad("30000");
    	$this->assertElementPresent("link=exact:Translate: HomePage (English, en)");
  	}

  	
  	public function testListOfLanguagesOnTranslatePageDoesNotContainAlreadyTranslatedLanguages() {
  		$this->openTikiPage('tiki-index.php?page=Multilingual+Test+Page+1');
    	$this->logInIfNecessaryAs('admin');
    	$this->select("page", "label=Translate");
    	$this->waitForPageToLoad("30000");
    	$this->assertSelectElementDoesNotContainItems(
                      "xpath=id('tiki-center')/form[1]/p/select[@name='lang']",
					   array('English' => 'en'),
					  "English should not have been present in the list of languages.");
  	}
    
    public function __testCannotGiveATranslationTheNameOfAnExistingPage() {
    	//NB. This is in fact wrong. If you have similar languages, say English and British English, 
    	//or Serbian (latin alphabet) and Croatian, the title of the page is bound to be the same. Here we force
    	//the translator to add a language tag to the title only to have the unique page name
    	//A multilingual system should add a language ID to the page name without the user's 
    	//intervention.
    	$this->openTikiPage('tiki-index.php?page=Multilingual+Test+Page+1');
    	$this->logInIfNecessaryAs('admin');
    	$this->select("page", "label=Français");
    	$this->waitForPageToLoad("30000");
    	$this->clickAndWait("link=Translate");
    	print $this->getHtmlSource();
    	$this->select("language_list", "label=English British (en-uk)");
    	$this->type("translation_name", "Multilingual Test Page 1");
    	$this->clickAndWait("//input[@value='Create translation']");
        $this->assertTrue($this->isTextPresent("Page already exists. Go back and choose a different name."));
    }
    
    
   
    /**************************************
     * Helper methods
     **************************************/

    protected function setUp()
    {
        $this->setBrowser('*firefox C:\Program Files\Mozilla Firefox\firefox.exe');
        $this->setBrowserUrl('http://localhost/');
        $this->current_test_db = "multilingualTestDump.sql";
        $this->restoreDBforThisTest();
        $this->printImportantMessageForTestUsers();
    }
    
    public function printImportantMessageForTestUsers() {
       die("MultilingualTest will not work unless:\n".
                   "- the name of the Tiki db is 'tiki_db_for_acceptance_tests' and \n".
				   "- the file 'multilingualTestDump.sql' (not in svn, due to its size) is copied in the mySql data directory.\n" .
				   "Comment out the call to printImportantMessageForTestUsers() in MultilingualTest::setUp() to run the tests.\n");
    }
    
    

    public function assertLanguagePicklistHasLanguages($expAvailableLanguages) {
        $this->assertSelectElementContainsItems("xpath=//select[@name='page' and @onchange='quick_switch_language( this )']",
                  $expAvailableLanguages, 
                  "Language picklist was wrong.");           
    }
    
    public function assertLanguagePicklistHasTranslateOption() {
        $this->assertElementPresent("xpath=//select[@name='page' and @onchange='quick_switch_language( this )']/option[@value='_translate_']",
                  "Translate option was not present.");           
    }
    
    public function assertLanguagePicklistHasNoTranslateOption() {
        $this->assertFalse($this->isElementPresent("xpath=//select[@name='page' and @onchange='quick_switch_language( this )']/option[@value='_translate_']",
                  "Translate option was present."));           
    }
}
?>
