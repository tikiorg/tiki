<?php

class TikiFilter_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('TikiFilterSuite');

        $suite->addTestSuite('TikiFilter_CallbackTest');
        $suite->addTestSuite('TikiFilter_XssTest');
        $suite->addTestSuite('TikiFilter_MapTest');
        $suite->addTestSuite('TikiFilter_WordTest');

        return $suite;
    }
}

?>
