<?php

require_once('importer/AllTests.php');
require_once('wikiparser/AllTests.php');
require_once('core/AllTests.php');

class AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('TikiTestSuite');

        $suite->addTestSuite('TikiImporter_AllTests');
        $suite->addTestSuite('TikiWikiParser_AllTests');
//      TODO: integrate lib/test/core tests
//      $suite->addTestSuite('Core_AllTests');
        
        return $suite;
    }
}
