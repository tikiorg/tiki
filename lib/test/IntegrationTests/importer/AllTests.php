<?php

require_once 'PHPUnit/Framework.php';
require_once 'tikiimporter_test.php';
require_once 'tikiimporter_wiki_test.php';
require_once 'tikiimporter_wiki_mediawiki_test.php';
 
class TikiImporter_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('TikiImporter_TestSuite');
 
        $suite->addTestSuite('TikiImporter_Test');
        $suite->addTestSuite('TikiImporter_Wiki_Test');
        $suite->addTestSuite('TikiImporter_Wiki_InsertPage_Test');
        $suite->addTestSuite('TikiImporter_Wiki_Mediawiki_Test');
        
        return $suite;
    }
}
