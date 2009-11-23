<?php
require_once 'PHPUnit/Framework.php';
require_once 'tikiwikiparser_test.php';
 
class TikiWikiParser_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('TikiWikiParser_TestSuite');
         
        $suite->addTestSuite('TikiWikiParser_Test');

        return $suite;
    }
}
