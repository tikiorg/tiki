<?php
/*
 * Parent class of all Selenium test cases.
 */
 
 require_once 'PHPUnit/Extensions/SeleniumTestCase.php';
 
 
class TikiSeleniumTestCase extends PHPUnit_Extensions_SeleniumTestCase {
	protected $backupGlobals = FALSE;
	var $current_test_db; 
	var $user_credentials = array (
		'admin' => 'tiki'
	);
	  
    public function openTikiPage($tikiPage) {
       $this->open("http://localhost/tiki-trunk/$tikiPage");
    }
    
    public function restoreDB($test_name) {
    	$dbRestorer = new TikiAcceptanceTestDBRestorer();
    	$dbRestorer->restoreDB($test_name);
    }
    
    public function logInIfNecessaryAs($my_user) {
    	if (!$this->_login_as($my_user)) {
    		die("Couldn't log in as $my_user!");
    	}
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
    
    private function _login_as($user) {
    	$password = $this->user_credentials[$user];
		$this->type("login-user", $user);
    	$this->type("login-pass", $password);
    	$this->clickAndWait("login");
		if ($this->isTextPresent("Invalid password")) {
			return false;
		}
		return true;
	}
} 
?>
