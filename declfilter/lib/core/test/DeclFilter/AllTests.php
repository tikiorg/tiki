<?php

class DeclFilter_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('DeclFilterSuite');

        $suite->addTestSuite('DeclFilter_BaseTest');
        $suite->addTestSuite('DeclFilter_StaticKeyTest');

        return $suite;
    }
}

?>
