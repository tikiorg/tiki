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

        $suite->addTestSuite('DeclFilter_StaticKeyFilterTest');
        $suite->addTestSuite('DeclFilter_StaticKeyUnsetTest');

        $suite->addTestSuite('DeclFilter_KeyPatternFilterTest');
        $suite->addTestSuite('DeclFilter_KeyPatternUnsetTest');

        $suite->addTestSuite('DeclFilter_CatchAllFilterTest');
        $suite->addTestSuite('DeclFilter_CatchAllUnsetTest');

        $suite->addTestSuite('DeclFilter_BaseTest');
        $suite->addTestSuite('DeclFilter_ConfigureTest');

        return $suite;
    }
}

?>
