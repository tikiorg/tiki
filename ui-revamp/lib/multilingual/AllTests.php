<?php

require_once 'BilingualAlignerTest.php';
require_once 'SentenceSegmentorTest.php';
require_once 'ShortestPathFinderTest.php';


class AllTests {
    public static function main() { 
    PHPUnit_TextUI_TestRunner::run(self::suite()); }

    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite('PHPUnit');
        $suite->addTestSuite('BilingualAlignerTest');
        $suite->addTestSuite('SentenceSegmentorTest');
        $suite->addTestSuite('ShortestPathFinderTest');
        return $suite;
    }
}

AllTests::main();
