<?php

class JitFilter_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('JitFilterSuite');

        $suite->addTestSuite('JitFilter_AccessTest');
        $suite->addTestSuite('JitFilter_FilterTest');
        $suite->addTestSuite('JitFilter_IteratorTest');
        $suite->addTestSuite('JitFilter_CallbackTest');
        $suite->addTestSuite('JitFilter_XssTest');
        $suite->addTestSuite('JitFilter_MapTest');
        $suite->addTestSuite('JitFilter_WordTest');

        return $suite;
    }
}

?>
