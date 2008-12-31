<?php

class AccessControl_Resolver_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('AccessControlResolverSuite');

        $suite->addTestSuite('AccessControl_Resolver_StaticTest');
        $suite->addTestSuite('AccessControl_Resolver_StackTest');

        return $suite;
    }
}

?>
