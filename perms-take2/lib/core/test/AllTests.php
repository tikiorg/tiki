<?php

class AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('CoreSuite');

        $suite->addTest(Multilingual_AllTests::suite());
        
        return $suite;
    }
}
