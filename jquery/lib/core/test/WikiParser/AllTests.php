<?php

class WikiParser_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('WikiParserSuite');

        $suite->addTestSuite('WikiParser_PluginRepositoryTest');
        $suite->addTestSuite('WikiParser_PluginMatcherTest');
        $suite->addTestSuite('WikiParser_PluginArgumentParserTest');
        //$suite->addTestSuite('WikiParser_PluginParserTest');

        return $suite;
    }
}

?>
