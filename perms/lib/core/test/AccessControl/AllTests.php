<?php

class AccessControl_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('AccessControlSuite');

        $suite->addTest(AccessControl_Resolver_AllTests::suite());
        $suite->addTestSuite('AccessControl_BaseTest');

        return $suite;
    }
}

?>
