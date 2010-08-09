<?php
require_once('AcceptanceTests/CollaborativeMultilingualTerminologyTest.php');
require_once('AcceptanceTests/ListPagesTest.php');
require_once('AcceptanceTests/MultilingualTest.php');
require_once('AcceptanceTests/MultilinguallibTest.php');
require_once('AcceptanceTests/SearchTest.php');
require_once('AcceptanceTests/TikiLibrariesAccessTest.php');


class AcceptanceTests_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('AcceptanceTestsSuite');

		$suite->addTestSuite('AcceptanceTests_CollaborativeMultilingualTerminologyTest');
		$suite->addTestSuite('AcceptanceTests_ListPagesTest');		
		$suite->addTestSuite('AcceptanceTests_MultilingualTest');
        $suite->addTestSuite('AcceptanceTests_MultilinguallibTest');
        $suite->addTestSuite('AcceptanceTests_SearchTest');
        $suite->addTestSuite('AcceptanceTests_TikiLibrariesAccessTest');
        return $suite;
    }
}
