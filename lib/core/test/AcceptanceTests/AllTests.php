<?php

class AcceptanceTests_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('AcceptanceTestsSuite');

        $suite->addTestSuite('AcceptanceTests_SearchTest');

        return $suite;
    }
}

?>